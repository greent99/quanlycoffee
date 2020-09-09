<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'date_create', 'cash_given', 'cash_return', 'total_price','discount', 'user_id'
    ];
    public function getOrderDetail()
    {
        return $this->hasMany(OrderDetail::class, "order_id", "id");
    }
}
