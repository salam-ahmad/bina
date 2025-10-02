<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    protected $table = 'customers';
    protected $guarded = [];



    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getDebtAttribute()
    {
        $sales = $this->orders()->sum('total');
        $payments = $this->payments()->sum('amount');
        return $sales - $payments;
    }
    // Example: compute balances per currency (IQD, USD, ...)
    public function currencyBalances(?string $from = null, ?string $to = null): array
    {
        $sales = DB::table('orders')
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $this->id)
            ->when($from, fn($q) => $q->whereDate('orders.date', '>=', $from))
            ->when($to,   fn($q) => $q->whereDate('orders.date', '<=', $to))
            ->groupBy('order_items.currency_id')
            ->selectRaw('order_items.currency_id, SUM(order_items.quantity * order_items.unit_price) AS total')
            ->pluck('total', 'order_items.currency_id');

        $payments = DB::table('payments')
            ->join('orders', function ($join) {
                $join->on('orders.id', '=', 'payments.payable_id')
                    ->where('payments.payable_type', Order::class);
            })
            ->where('orders.customer_id', $this->id)
            ->where('payments.direction', 'in')
            ->when($from, fn($q) => $q->whereDate('payments.date', '>=', $from))
            ->when($to,   fn($q) => $q->whereDate('payments.date', '<=', $to))
            ->groupBy('payments.currency_id')
            ->selectRaw('payments.currency_id, SUM(payments.amount) AS paid')
            ->pluck('paid', 'payments.currency_id');

        $ids = collect($sales->keys())->merge($payments->keys())->unique();
        $out = [];
        foreach ($ids as $cid) {
            $total = (float)($sales[$cid] ?? 0);
            $paid  = (float)($payments[$cid] ?? 0);
            $out[$cid] = ['total' => $total, 'paid' => $paid, 'balance' => $total - $paid];
        }
        return $out;
    }
}
