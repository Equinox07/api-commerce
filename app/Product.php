<?php

namespace App;

use App\Seller;
use App\Category;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    const AVAILABLE_PRODUCT = 'available';
    const UNAVAILABLE_PRODUCT = 'unavailable';
    protected $fillable = ['name','description','price','quantity','status','image','seller_id'];

    protected $hidden = ['pivot'];
    protected $dates = ['deleted_at'];

    public $transformer = ProductTransformer::class;
    
    public function isAvailable()
    {
        return $this->status == Product::AVAILABLE_PRODUCT;
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class); 
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class); 
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class); 
    }
    
    //
}
