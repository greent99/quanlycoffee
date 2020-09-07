<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'date_create', 'cash_given', 'cash_return', 'discount', 'user_id'
    ];
}
