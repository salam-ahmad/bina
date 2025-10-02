<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class OrderItem extends Model
{
    protected $fillable = ['product_id', 'order_id', 'quantity', 'unit_price', 'currency_id'];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:4',
    ];

    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    protected static function booted()
    {
        static::creating(function (self $item) {
            $prod = $item->product ?? Product::find($item->product_id);
            if ($prod && (int)$prod->currency_id !== (int)$item->currency_id) {
                throw ValidationException::withMessages([
                    'currency_id' => 'Line currency must match the product currency.',
                ]);
            }
        });
    }
}
