<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
