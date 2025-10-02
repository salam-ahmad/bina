<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    protected $guarded = [];
    protected $casts = [
        'amount'        => 'decimal:4',
        'balance_after' => 'decimal:4',
        'occurred_at'   => 'datetime',
    ];

    public function source(): \Illuminate\Database\Eloquent\Relations\MorphTo
    { return $this->morphTo(); }                 // matches morphs('source')
    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    { return $this->belongsTo(Currency::class); }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    { return $this->belongsTo(User::class); }
}
