<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Models\CashMovement;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $customers = Customer::all();
        $query = Order::with('customer')
            ->when($request->from_date, function ($query, $from_date) {
                return $query->where('created_at', '>=', Carbon::parse($from_date));
            })
            ->when($request->to_date, function ($query, $to_date) {
                return $query->where('created_at', '<=', Carbon::parse($to_date));
            })
            ->when($request->customer_id, function ($query, $customer_id) {
                return $query->where('customer_id', $customer_id);
            });
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        return Inertia::render('Order/Index', ['orders' => $orders, 'customers' => $customers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): \Inertia\Response
    {
        $customers = Customer::all();
        $products = Product::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Order/Create', [
            'products' => $products,
            'customers' => $customers,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {

        DB::transaction(function () use ($request) {
            $items = json_decode($request->input('order_items'), true) ?? [];
            $paid = (int)($request->input('paid_amount') ?? 0);
            $date = $request->date ? Carbon::parse($request->date)->toDateString() : now()->toDateString();

            // 1) compute totals on server (never trust client totals)
            $subtotal = collect($items)->sum(fn($i) => (int)$i['quantity'] * (int)$i['price']);
            $total = $subtotal;

            // 2) create the order (sale document)
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
                'date' => $date,
                'total' => $total,
                'paid_amount' => $paid,
                'due_amount' => max(0, $total - $paid),
                'status' => $paid === 0 ? 'unpaid' : ($paid < $total ? 'partial' : 'paid'),
                'note' => $request->note,
            ]);

            // 3) items + stock movements (OUT) with stock safety checks
            foreach ($items as $it) {
                $productId = (int)$it['id'];
                $qty = (int)$it['quantity'];
                $price = (int)$it['price'];

                // (a) assert sufficient stock — lock row for update to avoid race conditions
                $product = Product::lockForUpdate()->findOrFail($productId);
                if ($product->quantity < $qty) {
                    throw ValidationException::withMessages([
                        'order_items' => ["Insufficient stock for product ID {$productId}. Available: {$product->stock_qty}, requested: {$qty}."],
                    ]);
                }

                // (b) create line
                $line = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $price,
                    'rate_in' => $it['rate_in'] ?? null, // keep if you use it for reporting
                ]);

                // (c) decrease stock
                $product->decrement('quantity', $qty);

                // (d) log stock movement (OUT), reference the line
                StockMovement::create([
                    'product_id' => $product->id,
                    'direction' => 'out',
                    'qty' => $qty,
                    'reference_type' => OrderItem::class,
                    'reference_id' => $line->id,
                    'moved_at' => now(),
                    'note' => 'Sale',
                ]);
            }

            // 4) initial payment (cash IN) + cash ledger
            if ($paid > 0) {
                $payment = $order->payments()->create([
                    'direction' => 'in',      // YOU receive cash
                    'amount' => $paid,
                    'paid_at' => now(),
                    'method' => 'cash',
                    'note' => 'Initial payment',
                    'user_id' => auth()->id(), // if you have this column
                ]);

                CashMovement::create([
                    'source_type' => Payment::class,
                    'source_id' => $payment->id,
                    'direction' => 'in',
                    'amount' => $paid,
                    'occurred_at' => $payment->paid_at,
                    'note' => 'Sale payment',
                    'user_id' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }


    public function receivePayment(Request $request, Order $order)
    {
        DB::transaction(function () use ($request, $order) {
            $amount = (int)$request->amount;

            $payment = $order->payments()->create([
                'direction' => 'in',
                'amount' => $amount,
                'paid_at' => now(),
                'method' => 'cash',
                'note' => $request->note,
                'received_by' => auth()->id(),
            ]);

            \App\Models\CashMovement::create([
                'source_type' => \App\Models\Payment::class,
                'source_id' => $payment->id,
                'direction' => 'in',
                'amount' => $amount,
                'occurred_at' => $payment->paid_at,
                'note' => 'Sale payment',
                'user_id' => auth()->id(),
            ]);

            // Refresh derived fields from actual payments
            $paid = (int)$order->payments()->sum('amount');
            $order->paid_amount = $paid;
            $order->due_amount = max(0, $order->total - $paid);
            $order->status = $paid === 0 ? 'unpaid' : ($paid < $order->total ? 'partial' : 'paid');
            $order->save();
        });

        return back()->with('success', 'Payment recorded.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Order $order): \Inertia\Response
    {
        $order->load([
            'customer:id,name,phone,address',
            'items.product:id,name',
            'payments', // optional: to show received payments list
        ]);
        $payload = [
            'id' => $order->id,
            'date' => $order->date,
            'status' => $order->status,           // unpaid | partial | paid
            'note' => $order->note,
            'total' => (int)$order->total,
            'paid_amount' => (int)$order->paid_amount,
            'due_amount' => (int)$order->due_amount,
            'customer' => $order->customer ? [
                'id' => $order->customer->id,
                'name' => $order->customer->name,
                'phone' => $order->customer->phone,
                'address' => $order->customer->address,
                // if you have a "customer orders" page, link it here:
                // 'orders_url' => route('customers.orders', $order->customer->id),
            ] : null,

            'items' => $order->items->map(function ($line) {
                // Your columns were: product_id, quantity, price, rate_in (on the item)
                $qty = (int)($line->quantity ?? $line->qty ?? 0);
                $unit_price = (int)($line->price ?? $line->unit_price ?? 0);

                return [
                    'id' => $line->id,
                    'product_id' => $line->product_id,
                    'name' => optional($line->product)->name,
                    'qty' => $qty,
                    'unit_price' => $unit_price,
                    'line_total' => (int)($line->line_total ?? $qty * $unit_price),
                    'note' => $line->note,
                ];
            })->values(),

            // Optional payments list
            'payments' => $order->payments
                ? $order->payments->map(fn($p) => [
                    'id' => $p->id,
                    'direction' => $p->direction, // 'in'
                    'amount' => (int)$p->amount,
                    'paid_at' => $p->paid_at,
                    'method' => $p->method,
                    'note' => $p->note,
                ])->values()
                : [],

            // Convenient action: go to “Receive Payment” page for this order
            'receive_url' => route('payments.receiveForOrderPage', $order->id),
        ];

        return Inertia::render('Order/Show', [
            'order' => $payload,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order, Request $request): \Inertia\Response
    {
        $deposit = Deposit::where('order_id', $order->id)->first();
        $order->load(['customer', 'order_items.product']);
        $customers = Customer::all();
        $products = Product::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Order/Edit', ['order' => $order, 'deposit' => $deposit, 'products' => $products, 'customers' => $customers]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        DB::beginTransaction();
        try {
            $order->update([
                'customer_id' => $request->customer_id,
                'total' => $request->total,
                'status' => $request->status,
                'note' => $request->note,
            ]);
            $orderItems = json_decode($request->input('order_items'), true);
            $this->updateOrderItems($order, $orderItems);
            $this->updateOrCreateDeposit($order, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
        }
        return redirect()->route('orders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): void
    {
        $order->delete();

    }

    public function decreaseAmount($id, $qty): void
    {
        $product = Product::find($id);
        $product->decrement('quantity', $qty);
    }

    public function increaseAmount($id, $qty): void
    {
        $product = Product::find($id);
        $product->increment('quantity', $qty);
    }

    public function getOrdersByCustomer(Customer $customer)
    {
//        $total = Order::where('customer_id', $customer->id)->sum('total');

        return response()->json([
            'total' => 'hi'
        ], 200);
    }

    public function order_items(Order $order): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => new OrderDetailsApiResource($order->load('order_items.product'))
        ], 200);
    }

    private function updateOrderItems(Order $order, $orderItems): void
    {
        $this->increaseAmount($orderItems[0]['product_id'], $orderItems[0]['quantity']);
        $order->order_items()->delete();
        foreach ($orderItems as $item) {
            OrderItem::create([
                'product_id' => $item['product_id'],
                'category_id' => $item['category_id'],
                'order_id' => $order['id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'rate_in' => $item['rate_in'],
                'created_at' => $order['created_at'],
            ]);
            $this->decreaseAmount($item['product_id'], $item['quantity']);
        }
    }

    private function updateOrCreateDeposit(Order $order, $request): void
    {
        $deposit = Deposit::where('order_id', $order->id)->first();

        if ($deposit) {
            $this->updateExistingDeposit($deposit, $request);
        } else {
            $this->createNewDepositIfNeeded($order, $request);
        }
    }

    private function updateExistingDeposit(Deposit $deposit, $request): void
    {
        $deposit->update([
            'customer_id' => $request->customer_id,
            'amount' => $request->cashAmount,
            'status' => $request->status,
        ]);
    }

    private function createNewDepositIfNeeded(Order $order, $request): void
    {
        if ($order->status === "loan" && $request->cashAmount >= 250) {
            $this->createDeposit($order, $request, "loan", $request->cashAmount);
        } elseif ($order->status === "cash") {
            $this->createDeposit($order, $request, "cash", $order->total);
        }
    }

    private function createDeposit(Order $order, $request, $status, $amount): void
    {
        Deposit::create([
            'customer_id' => $request->customer_id,
            'order_id' => $order->id,
            'amount' => $amount,
            'status' => $status,
        ]);
    }
}
