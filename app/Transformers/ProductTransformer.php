<?php

namespace App\Transformers;

use App\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            'identifier'=> (int)$product->id,
            'title'=> (string)$product->name,
            'details'=> (string)$product->description,
            'stock'=> (int)$product->quantity,
            'condition'=> (string)$product->status,
            'picture'=> url("img/{$product->image}"),
            'seller'=> (int)$product->seller_id,
            'creationDate'=> (string)$product->created_at,
            'lastChange'=> (string)$product->updated_at,
            'deletedDate'=>isset($product->deleted_at) ? (string) $product->deleted_at : null ,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('products.show', $product->id),
                ],
                [
                    'rel' => 'products.buyers',
                    'href' => route('products.buyers.index', $product->id),
                ],
                [
                    'rel' => 'products',
                    'href' => route('products.categories.index', $product->id),
                ],
                [
                    'rel' => 'transactions',
                    'href' => route('products.transactions.index', $product->id),
                ],
                [
                    'rel' => 'seller',
                    'href' => route('sellers.show', $product->seller_id),
                ],
            ]
        ];
    }

    public static function originalAttributes($index)
    {
        $attributes = [
            'identifier'=> 'id',
            'title'=> 'name',
            'details'=> 'description',
            'stock'=> 'quantity',
            'condition'=> status,
            'picture'=> 'image',
            'seller'=> 'seller_id',
            'creationDate'=> 'created_at',
            'lastChange'=> 'updated_at',
            'deletedDate'=>'deleted_at'
        ];

        return isset($attributes[$index]) ? $attributes [$index] : null;
    }
    public static function transformedAttributes($index)
    {
        $attributes = [
            'id'=> 'identifier',
            'name'=> 'title',
            'description'=> 'details',
            'quantity'=> 'stock',
            'status'=> 'condition',
            'image'=> 'picture',
            'seller_id'=> 'seller',
            'created_at'=> 'creationDate',
            'updated_at'=> 'lastChange',
            'deleted_at'=> 'deletedDate',
        ];

        return isset($attributes[$index]) ? $attributes [$index] : null;
    }

}
