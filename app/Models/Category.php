<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GroupCategory;

class Category extends Model
{
    protected $table = 'category';
    protected $fillable = [
        'name'
    ];
    public function group_category()
    {
        return $this->hasMany(GroupCategory::class,'category_id','id');
    }
}
