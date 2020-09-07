<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'orderdetail';
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price'
    ];
}
