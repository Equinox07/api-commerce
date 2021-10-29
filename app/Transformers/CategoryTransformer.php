<?php

namespace App\Transformers;

use App\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            'identifier'=> $category->id,
            'title'=> (string)$category->name,
            'details'=> (string)$category->description,
            'creationDate'=> (string)$category->created_at,
            'lastChange'=> (string)$category->updated_at,
            'deletedDate'=>isset($category->deleted_at) ? (string) $category->deleted_at : null ,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('categories.show', $category->id),
                ],
                [
                    'rel' => 'category.buyers',
                    'href' => route('categories.buyers.index', $category->id),
                ],
                [
                    'rel' => 'products',
                    'href' => route('categories.products.index', $category->id),
                ],
                [
                    'rel' => 'sellers',
                    'href' => route('categories.sellers.index', $category->id),
                ],
                [
                    'rel' => 'transactions',
                    'href' => route('categories.transactions.index', $category->id),
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
             'created_at'=> 'creationDate',
             'updated_at'=> 'lastChange',
             'deleted_at'=> 'deletedDate',
        ];

        return isset($attributes[$index]) ? $attributes [$index] : null;
    }
    
}
