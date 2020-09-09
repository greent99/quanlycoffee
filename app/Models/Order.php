<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";
    protected $fillable = [
        'date_create', 'total_price', 'cash_given', 'cash_return', 'discount', 'user_id'
    ];
    public function getOrderDetail()
    {
        return $this->hasMany(OrderDetail::class,'order_id','id');
    }
}
