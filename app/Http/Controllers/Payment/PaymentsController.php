<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Purchase;
use App\Services\PayService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentsController extends Controller
{
    public function __construct(private PayService $pay)
    {
    }

    // ---------- Actions (POST) ----------
    public function receiveForOrder(Request $request, Order $order)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        $this->pay->receiveCustomerDebt($order, (int)$data['amount'], $data['note'] ?? null);

        return back()->with('success', 'Payment received.');
    }

    public function payForPurchase(Request $request, Purchase $purchase)
    {
         $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        $this->pay->paySupplierDebt($purchase, (int)$data['amount'], $data['note'] ?? null);

        return redirect()->route('suppliers.purchases', ['supplier' => $purchase->supplier_id])
            ->with('message', 'بەسەرکەوتویی پارەکە درا');
    }

    // ---------- Pages (GET) ----------
    public function arReport(Request $request)
    {
        $rows = $this->pay->getCustomerBalances($request->query('from'), $request->query('to'));
        return Inertia::render('Reports/AR', [
            'rows' => $rows,
            'filters' => [
                'from' => $request->query('from'),
                'to' => $request->query('to'),
            ],
        ]);
    }

    public function arOnlyOwing(Request $request)
    {
        $rows = $this->pay->getOwingCustomers($request->query('from'), $request->query('to'));
        return Inertia::render('Reports/AROwing', [
            'rows' => $rows,
            'filters' => [
                'from' => $request->query('from'),
                'to' => $request->query('to'),
            ],
        ]);
    }

    public function apReport(Request $request)
    {
        $rows = $this->pay->getSupplierBalances($request->query('from'), $request->query('to'));
        return Inertia::render('Reports/AP', [
            'rows' => $rows,
            'filters' => [
                'from' => $request->query('from'),
                'to' => $request->query('to'),
            ],
        ]);
    }

    // Optional single-document payment pages
    public function receiveForOrderPage(Order $order)
    {
        return Inertia::render('Payments/ReceiveForOrder', [
            'order' => [
                'id' => $order->id,
                'customer' => optional($order->customer)->name,
                'total' => (int)$order->total,
                'paid_amount' => (int)$order->paid_amount,
                'due_amount' => (int)$order->due_amount,
                'status' => $order->status,
            ],
        ]);
    }

    public function payForPurchasePage(Purchase $purchase)
    {
        return Inertia::render('Payments/PayForPurchase', [
            'purchase' => [
                'id' => $purchase->id,
                'supplier' => optional($purchase->supplier)->name,
                'total' => (int)$purchase->total,
                'paid_amount' => (int)$purchase->paid_amount,
                'due_amount' => (int)$purchase->due_amount,
                'status' => $purchase->status,
            ],
        ]);
    }

    // app/Http/Controllers/PaymentsController.php

    public function receiveIndex(Request $request)
    {
        // Orders with due > 0
        $orders = \App\Models\Order::query()
            ->with('customer:id,name')
            ->whereColumn('paid_amount', '<', 'total')
            ->orderByDesc('id')
            ->select('id', 'customer_id', 'total', 'paid_amount', 'due_amount', 'status', 'date')
            ->paginate(15)
            ->through(fn($o) => [
                'id' => $o->id,
                'customer' => optional($o->customer)->name,
                'total' => (int)$o->total,
                'paid_amount' => (int)$o->paid_amount,
                'due_amount' => (int)$o->due_amount,
                'status' => $o->status,
                'date' => $o->date,
                'pay_url' => route('payments.receiveForOrderPage', $o->id),
            ]);

        return \Inertia\Inertia::render('Payments/ReceiveIndex', ['orders' => $orders]);
    }

    public function payIndex(Request $request)
    {
        // Purchases with due > 0
        $purchases = \App\Models\Purchase::query()
            ->with('supplier:id,name')
            ->whereColumn('paid_amount', '<', 'total')
            ->orderByDesc('id')
            ->select('id', 'supplier_id', 'total', 'paid_amount', 'due_amount', 'status', 'date')
            ->paginate(15)
            ->through(fn($p) => [
                'id' => $p->id,
                'supplier' => optional($p->supplier)->name,
                'total' => (int)$p->total,
                'paid_amount' => (int)$p->paid_amount,
                'due_amount' => (int)$p->due_amount,
                'status' => $p->status,
                'date' => $p->date,
                'pay_url' => route('payments.payForPurchasePage', $p->id),
            ]);

        return \Inertia\Inertia::render('Payments/PayIndex', ['purchases' => $purchases]);
    }

    public function dashboard(Request $request)
    {
        $metrics = $this->pay->getDashboardMetrics(
            $request->query('from'),
            $request->query('to')
        );

        return Inertia::render('Dashboard/Finance', [
            'metrics' => $metrics,
        ]);
    }
}
