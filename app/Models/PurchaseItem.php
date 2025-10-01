<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $table = "purchase_items";
    protected $fillable = ['purchase_id', 'product_id', 'quantity', 'price', 'rate_in'];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function purchase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Purchase::class);
    }
    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    { return $this->belongsTo(Currency::class); }
}
