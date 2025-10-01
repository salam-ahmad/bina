<?php

namespace App\Services;

use App\Models\Order;

// your sales document
use App\Models\Purchase;

// your purchase document
use App\Models\Payment;
use App\Models\CashMovement;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

/**
 * Centralized payment/debt operations and reporting.
 * IQD amounts are integers (dinar).
 */
class PayService
{
    /**
     * Receive cash from a customer for a given Order (A/R collection).
     */
    public function receiveCustomerDebt(Order $order, int $amount, ?string $note = null): Payment
    {
        return DB::transaction(function () use ($order, $amount, $note) {
            // 1) create a payment (cash IN)
            $payment = $order->payments()->create([
                'direction' => 'in',
                'amount' => $amount,
                'paid_at' => now(),
                'method' => 'cash',
                'note' => $note,
                'user_id' => Auth::id(),   // or user_id if you named it differently
            ]);

            // 2) mirror to cash ledger
            CashMovement::create([
                'source_type' => Payment::class,
                'source_id' => $payment->id,
                'direction' => 'in',
                'amount' => $amount,
                'occurred_at' => $payment->paid_at,
                'note' => $note ?? 'Sale payment',
                'user_id' => Auth::id(),
            ]);

            // 3) refresh derived fields on the order
            $this->refreshPaymentStatusForOrder($order->fresh());

            return $payment;
        });
    }

    /**
     * Pay cash to a supplier for a given Purchase (A/P payment).
     */
    public function paySupplierDebt(Purchase $purchase, int $amount, ?string $note = null): Payment
    {
        return DB::transaction(function () use ($purchase, $amount, $note) {
            // 1) create a payment (cash OUT)
            $payment = $purchase->payments()->create([
                'direction' => 'out',
                'amount' => $amount,
                'paid_at' => now(),
                'method' => 'cash',
                'note' => $note,
                'user_id' => Auth::id(),
            ]);

            // 2) mirror to cash ledger
            CashMovement::create([
                'source_type' => Payment::class,
                'source_id' => $payment->id,
                'direction' => 'out',
                'amount' => $amount,
                'occurred_at' => $payment->paid_at,
                'note' => $note ?? 'Purchase payment',
                'user_id' => Auth::id(),
            ]);

            // 3) refresh derived fields on the purchase
            $this->refreshPaymentStatusForPurchase($purchase->fresh());

            return $payment;
        });
    }

    /**
     * Recompute paid/due/status from actual payments for Order (sale).
     */
    public function refreshPaymentStatusForOrder(Order $order): void
    {
        $paid = (int)$order->payments()->sum('amount'); // only 'in' exist here by our usage
        $order->paid_amount = $paid;
        $order->due_amount = max(0, (int)$order->total - $paid);
        $order->status = $paid === 0 ? 'unpaid' : ($paid < (int)$order->total ? 'partial' : 'paid');
        $order->save();
    }

    /**
     * Recompute paid/due/status from actual payments for Purchase.
     */
    public function refreshPaymentStatusForPurchase(Purchase $purchase): void
    {
        $paid = (int)$purchase->payments()->sum('amount'); // only 'out' by our usage
        $purchase->paid_amount = $paid;
        $purchase->due_amount = max(0, (int)$purchase->total - $paid);
        $purchase->status = $paid === 0 ? 'unpaid' : ($paid < (int)$purchase->total ? 'partial' : 'paid');
        $purchase->save();
    }

    // ---------------------------
    // Reporting (simple balances)
    // ---------------------------

    /**
     * Per-customer AR: total_sold, total_received, balance_due.
     * Returns a collection of lightweight DTO-like arrays.
     *
     * Optional filters: fromDate, toDate (Y-m-d) restrict by order date.
     */

    public function getCustomerBalances(?string $fromDate = null, ?string $toDate = null): Collection
    {
        // Sum payments per ORDER first (one row per order)
        $paymentSums = DB::table('payments')
            ->select('payable_id', DB::raw('SUM(amount) AS paid_sum'))
            ->where('payable_type', Order::class)
            ->where('direction', 'in') // customer pays you
            ->groupBy('payable_id');

        $rows = DB::table('customers')
            ->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
            ->when($fromDate, fn ($q) => $q->whereDate('orders.date', '>=', $fromDate))
            ->when($toDate,   fn ($q) => $q->whereDate('orders.date', '<=', $toDate))
            ->leftJoinSub($paymentSums, 'pp', function ($join) {
                $join->on('pp.payable_id', '=', 'orders.id');
            })
            ->groupBy('customers.id', 'customers.name')
            ->selectRaw('
            customers.id,
            customers.name,
            COALESCE(SUM(orders.total), 0)                          AS total_sold,
            COALESCE(SUM(COALESCE(pp.paid_sum, 0)), 0)              AS total_received,
            (COALESCE(SUM(orders.total), 0) - COALESCE(SUM(COALESCE(pp.paid_sum, 0)), 0))
                                                                     AS balance_due
        ')
            ->orderByDesc('balance_due')
            ->get();

        return $rows->map(fn ($r) => (object)[
            'customer_id'    => (int) $r->id,
            'customer_name'  => $r->name,
            'total_sold'     => (int) $r->total_sold,
            'total_received' => (int) $r->total_received,
            'balance_due'    => (int) $r->balance_due,
        ]);
    }

    /**
     * Only customers who still owe you (> 0).
     */
    public function getOwingCustomers(?string $fromDate = null, ?string $toDate = null): Collection
    {
        return $this->getCustomerBalances($fromDate, $toDate)
            ->filter(fn($r) => $r->balance_due > 0)
            ->values();
    }

    /**
     * Per-supplier AP: total_bought, total_paid, balance_payable.
     * Optional filters: fromDate, toDate (Y-m-d) restrict by purchase date.
     */
    public function getSupplierBalances(?string $fromDate = null, ?string $toDate = null): Collection
    {
        // 1) Sum payments per purchase (one row per purchase)
        $paymentSums = DB::table('payments')
            ->select('payable_id', DB::raw('SUM(amount) AS paid_sum'))
            ->where('payable_type', Purchase::class)
            ->where('direction', 'out')       // you pay suppliers
            ->groupBy('payable_id');

        // 2) Join suppliers -> purchases (many) -> paymentSums (1 per purchase)
        $rows = DB::table('suppliers')
            ->leftJoin('purchases', 'purchases.supplier_id', '=', 'suppliers.id')
            ->when($fromDate, fn($q) => $q->whereDate('purchases.date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('purchases.date', '<=', $toDate))
            ->leftJoinSub($paymentSums, 'pp', function ($join) {
                $join->on('pp.payable_id', '=', 'purchases.id');
            })
            ->groupBy('suppliers.id', 'suppliers.name')
            ->selectRaw('
            suppliers.id,
            suppliers.name,
            COALESCE(SUM(purchases.total), 0)                           AS total_bought,
            COALESCE(SUM(COALESCE(pp.paid_sum, 0)), 0)                  AS total_paid,
            (COALESCE(SUM(purchases.total), 0) - COALESCE(SUM(COALESCE(pp.paid_sum, 0)), 0))
                                                                         AS balance_payable
        ')
            ->orderByDesc('balance_payable')
            ->get();

        return $rows->map(fn($r) => (object)[
            'supplier_id' => (int)$r->id,
            'supplier_name' => $r->name,
            'total_bought' => (int)$r->total_bought,
            'total_paid' => (int)$r->total_paid,
            'balance_payable' => (int)$r->balance_payable,
        ]);
    }

    public function getDashboardMetrics(?string $fromDate = null, ?string $toDate = null): array
    {
        $orderDate = fn($q) => $q->when($fromDate, fn($qq) => $qq->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn($qq) => $qq->whereDate('date', '<=', $toDate));

        // Sales & purchases
        $totalSold = (int)\App\Models\Order::query()->tap($orderDate)->sum('total');
        $totalBought = (int)\App\Models\Purchase::query()->tap($orderDate)->sum('total');

        // Accounts receivable / payable
        $arDue = (int)\App\Models\Order::query()->tap($orderDate)->sum(\DB::raw('GREATEST(total - paid_amount, 0)'));
        $apDue = (int)\App\Models\Purchase::query()->tap($orderDate)->sum(\DB::raw('GREATEST(total - paid_amount, 0)'));

        // Optional cash in/out
        $cashIn = (int)\App\Models\Payment::where('direction', 'in')
            ->when($fromDate, fn($q) => $q->whereDate('paid_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('paid_at', '<=', $toDate))
            ->sum('amount');

        $cashOut = (int)\App\Models\Payment::where('direction', 'out')
            ->when($fromDate, fn($q) => $q->whereDate('paid_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('paid_at', '<=', $toDate))
            ->sum('amount');

        // 🔹 Inventory value (sum of stock qty * cost price)
        $inventoryValue = (int)\App\Models\Product::query()
            ->selectRaw('SUM(quantity * rate_in) as total_value')
            ->value('total_value');

        return [
            'total_sold' => $totalSold,
            'total_bought' => $totalBought,
            'ar_due' => $arDue,
            'ap_due' => $apDue,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'inventory_value' => $inventoryValue,
            'from' => $fromDate,
            'to' => $toDate,
        ];
    }


}
