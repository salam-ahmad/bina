<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $guarded = [];
    public $timestamps = false;

    public function payable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    { return $this->belongsTo(Currency::class); }

    public function cashMovements(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(CashMovement::class, 'source');
    }
}
