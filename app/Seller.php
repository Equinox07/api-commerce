<?php

namespace App;

use App\Product;
use App\Scope\SellerScope;
use App\Transformers\SellerTransformer;

// use Illuminate\Database\Eloquent\Model;

class Seller extends User
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SellerScope);
    }

    public $transformer = SellerTransformer::class;
    
    public function products()
    {
        return $this->hasMany(Product::class); 
    }
    //
}
