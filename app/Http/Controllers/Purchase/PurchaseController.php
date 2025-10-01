<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\PurchaseCreateRequest;
use App\Http\Requests\Purchase\PurchaseUpdateRequest;
use App\Models\CashMovement;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::all();
        $query = Purchase::with('supplier')
            ->when($request->from_date, function ($query, $from_date) {
                return $query->where('created_at', '>=', Carbon::parse($from_date));
            })
            ->when($request->to_date, function ($query, $to_date) {
                return $query->where('created_at', '<=', Carbon::parse($to_date));
            })
            ->when($request->supplier_id, function ($query, $supplier_id) {
                return $query->where('supplier_id', $supplier_id);
            });
        $purchases = $query->orderBy('created_at', 'desc')->paginate(20);
        return Inertia::render('Purchase/Index', ['purchases' => $purchases, 'suppliers' => $suppliers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $suppliers = Supplier::all();
        $products = Product::query()
            ->where('code', 'like', "%{$request->search}%")
            ->orWhere('name', 'like', "%{$request->search}%")
            ->paginate(10)
            ->withQueryString();
        return Inertia::render('Purchase/Create', [
            'products' => $products,
            'suppliers' => $suppliers,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Throwable
     */
    public function store(PurchaseCreateRequest $request)
    {
        DB::transaction(function () use ($request) {
            $items = json_decode($request->input('order_items'), true) ?? [];
            $paid = (int)($request->input('paid_amount') ?? 0);
            $date = $request->date ? \Carbon\Carbon::parse($request->date)->toDateString() : now()->toDateString();

            // 1) compute totals on server
            $subtotal = collect($items)->sum(fn($i) => (int)$i['quantity'] * (int)$i['price']);
            $total = $subtotal;

            // 2) create the purchase document
            $purchaseDoc = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'date' => $date,
                'total' => $total,
                'paid_amount' => $paid,
                'due_amount' => max(0, $total - $paid),
                'status' => $paid === 0 ? 'unpaid' : ($paid < $total ? 'partial' : 'paid'),
                'note' => $request->note,
            ]);

            // 3) items + stock movements
            foreach ($items as $item) {
                $line = PurchaseItem::create([
                    'purchase_id' => $purchaseDoc->id,
                    'product_id' => (int)$item['id'],
                    'quantity' => (int)$item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'currency_id' => $item['currency_id'],
                 ]);

                // increase stock immediately
                $product = $this->increaseAmount($line->product_id, $line->quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'direction' => 'in',
                    'qty' => $line->quantity,
                    'reference_type' => PurchaseItem::class,
                    'reference_id' => $line->id,   // <-- reference the line, not the purchase
                    'moved_at' => now(),
                    'note' => 'Purchase',
                ]);
            }

            // 4) initial payment (cash OUT) + cash movement
            if ($paid > 0) {
                $payment = $purchaseDoc->payments()->create([
                    'direction' => 'out',
                    'amount' => $paid,
                    'paid_at' => now(),
                    'method' => 'cash',
                    'note' => 'پارەی سەرەتا',
                    'user_id' => auth()->id(),
                ]);

                CashMovement::create([
                    'source_type' => Payment::class,
                    'source_id' => $payment->id,
                    'direction' => 'out',
                    'currency_id'  => $payment['currency_id'],
                    'amount' => $paid,
                    'occurred_at' => $payment->paid_at,
                    'note' => 'Purchase payment',
                    'user_id' => auth()->id(),
                ]);
            }
        });
    }


    public function increaseAmount($id, $qty)
    {
        $product = Product::find($id);
        $product->increment('quantity', $qty);
        return $product;
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier:id,name,phone,address',
            'items.product:id,name',
            'payments' // optional: if you want to show past payments too
        ]);
        $payload = [
            'id' => $purchase->id,
            'date' => $purchase->date,
            'status' => $purchase->status,        // unpaid | partial | paid
            'note' => $purchase->note,
            'total' => (int)$purchase->total,
            'paid_amount' => (int)$purchase->paid_amount,
            'due_amount' => (int)$purchase->due_amount,
            'supplier' => $purchase->supplier ? [
                'id' => $purchase->supplier->id,
                'name' => $purchase->supplier->name,
                'phone' => $purchase->supplier->phone,
                'address' => $purchase->supplier->address,
                'purchases_url' => route('suppliers.purchases', $purchase->supplier->id),
            ] : null,
            'items' => $purchase->items->map(function ($line) {
                return [
                    'id' => $line->id,
                    'product_id' => $line->product_id,
                    'name' => optional($line->product)->name,
                    'quantity' => (int)$line->quantity ?? (int)($line->quantity ?? 0), // use your column name
                    'price' => (int)$line->price ?? (int)($line->price ?? 0),
                    'total' => (int)$line->total ?? (int)(($line->quantity ?? $line->quantity) * ($line->price ?? $line->price)),
                    'note' => $line->note,
                ];
            })->values(),
            // optional payments list (remove if not needed)
            'payments' => $purchase->payments
                ? $purchase->payments->map(fn($p) => [
                    'id' => $p->id,
                    'direction' => $p->direction, // 'out'
                    'amount' => (int)$p->amount,
                    'paid_at' => $p->paid_at,
                    'method' => $p->method,
                    'note' => $p->note,
                ])->values()
                : [],
            // convenient actions
            'pay_url' => route('payments.payForPurchasePage', $purchase->id),
        ];

        return Inertia::render('Purchase/Show', [
            'purchase' => $payload,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase, Request $request)
    {
        $suppliers = Supplier::all();
        $products = Product::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();
        $purchase->load(['supplier', 'purchase_items.product']);
        return Inertia::render('Purchase/Edit', ['purchase' => $purchase, 'suppliers' => $suppliers, 'products' => $products]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PurchaseUpdateRequest $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
