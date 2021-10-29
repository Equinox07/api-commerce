<?php

namespace App;

use App\Transaction;
use App\Scope\BuyerScope;
use App\Transformers\BuyerTransformer;

// use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new BuyerScope);
    }

    public $transformer = BuyerTransformer::class;
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    //
}
