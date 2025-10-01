<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PartyPaymentsController extends Controller
{
    /**
     * Payments you made to a supplier (direction = 'out', payable_type = Purchase).
     */
    public function supplier(Request $request, Supplier $supplier)
    {
        $filters = $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date',
        ]);

        // Base query: payments -> purchases -> supplier
        $q = DB::table('payments')
            ->join('purchases', 'purchases.id', '=', 'payments.payable_id')
            ->where('payments.payable_type', Purchase::class)
            ->where('payments.direction', 'out')
            ->where('purchases.supplier_id', $supplier->id)
            ->when($filters['from'] ?? null, fn($qq,$v) => $qq->whereDate('payments.paid_at','>=',$v))
            ->when($filters['to'] ?? null,   fn($qq,$v) => $qq->whereDate('payments.paid_at','<=',$v))
            ->select([
                'payments.id',
                'payments.amount',
                'payments.paid_at',
                'payments.method',
                'payments.note',
                'purchases.id as purchase_id',
                'purchases.date as purchase_date',
                'purchases.total as purchase_total',
            ])
            ->orderByDesc('payments.paid_at')
            ->orderByDesc('payments.id');

        // Totals + pagination
        $totalPaid = (int) $q->clone()->sum('payments.amount');

        $payments = $q->paginate(15)->withQueryString();
        $payments->getCollection()->transform(function ($row) {
            return [
                'id'             => (int)$row->id,
                'amount'         => (int)$row->amount,
                'paid_at'        => (string)$row->paid_at,
                'method'         => $row->method,
                'note'           => $row->note,
                'purchase_id'    => (int)$row->purchase_id,
                'purchase_date'  => $row->purchase_date,
                'purchase_total' => (int)$row->purchase_total,
                'purchase_url'   => route('purchases.show', (int)$row->purchase_id),
            ];
        });

        return Inertia::render('Supplier/Payments', [
            'supplier'    => ['id' => $supplier->id, 'name' => $supplier->name],
            'filters'     => $filters,
            'payments'    => $payments,
            'total_paid'  => $totalPaid,
        ]);
    }

    /**
     * Payments a customer made to you (direction = 'in', payable_type = Order).
     */
    public function customer(Request $request, Customer $customer)
    {
        $filters = $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date',
        ]);

        $q = DB::table('payments')
            ->join('orders', 'orders.id', '=', 'payments.payable_id')
            ->where('payments.payable_type', Order::class)
            ->where('payments.direction', 'in')
            ->where('orders.customer_id', $customer->id)
            ->when($filters['from'] ?? null, fn($qq,$v) => $qq->whereDate('payments.paid_at','>=',$v))
            ->when($filters['to'] ?? null,   fn($qq,$v) => $qq->whereDate('payments.paid_at','<=',$v))
            ->select([
                'payments.id',
                'payments.amount',
                'payments.paid_at',
                'payments.method',
                'payments.note',
                'orders.id as order_id',
                'orders.date as order_date',
                'orders.total as order_total',
            ])
            ->orderByDesc('payments.paid_at')
            ->orderByDesc('payments.id');

        $totalReceived = (int) $q->clone()->sum('payments.amount');

        $payments = $q->paginate(15)->withQueryString();
        $payments->getCollection()->transform(function ($row) {
            return [
                'id'          => (int)$row->id,
                'amount'      => (int)$row->amount,
                'paid_at'     => (string)$row->paid_at,
                'method'      => $row->method,
                'note'        => $row->note,
                'order_id'    => (int)$row->order_id,
                'order_date'  => $row->order_date,
                'order_total' => (int)$row->order_total,
                'order_url'   => route('orders.show', (int)$row->order_id),
            ];
        });

        return Inertia::render('Customer/Payments', [
            'customer'       => ['id' => $customer->id, 'name' => $customer->name],
            'filters'        => $filters,
            'payments'       => $payments,
            'total_received' => $totalReceived,
        ]);
    }
}
