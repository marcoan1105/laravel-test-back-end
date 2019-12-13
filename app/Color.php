<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'description'
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'products_colors');
    }
}
