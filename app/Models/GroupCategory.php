<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class GroupCategory extends Model
{
    protected $table = 'groupcategory';
    protected $fillable = ['name','category_id'];

    public function product()
    {
        return $this->hasMany(Product::class,'groupcategory_id','id');
    }

}
