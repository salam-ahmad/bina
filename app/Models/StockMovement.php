<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['product_id','quantity_change','date','note'];

    protected $casts = [
        'quantity_change' => 'decimal:3',
        'date'            => 'date',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    { return $this->belongsTo(Product::class); }

    public function source(): \Illuminate\Database\Eloquent\Relations\MorphTo
    { return $this->morphTo(); }
}
