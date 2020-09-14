<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GroupCategory;

class Product extends Model
{
    protected $table = 'product';
    protected $fillable = [
        'name','price','image','groupcategory_id','description'
    ];
}
