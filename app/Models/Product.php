<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['name','code','unit_id','currency_id','quantity','description',
        'default_buy_price','default_sell_price'];
    protected $casts = [
        'quantity'            => 'decimal:3',
        'default_buy_price'   => 'decimal:4',
        'default_sell_price'  => 'decimal:4',
    ];
    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}

