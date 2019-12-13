<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['description', 'price'];

    public function colors(){
        return $this->belongsToMany(Color::class, 'products_colors')->get();
    }
}
