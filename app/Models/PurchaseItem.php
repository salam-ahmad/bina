<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class PurchaseItem extends Model
{
    protected $table = "purchase_items";
    protected $fillable = ['purchase_id', 'product_id', 'quantity', 'unit_price', 'currency_id'];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_price' => 'decimal:4',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    { return $this->belongsTo(Product::class); }
    public function purchase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    { return $this->belongsTo(Purchase::class); }
    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    { return $this->belongsTo(Currency::class); }

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
