<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name','description'];
    
    protected $hidden = ['pivot'];

    public $transformer = CategoryTransformer::class;
    

    public function products()
    {
        //belongsToMany :: for many to many relationship
        return $this->belongsToMany(Product::class); 
    }
    //
}
