<?php

namespace App\Transformers;

use App\Seller;
use League\Fractal\TransformerAbstract;

class SellerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Seller $seller)
    {
        return [
            'identifier'=> $seller->id,
            'name'=> (string)$seller->name,
            'email'=> (string)$seller->email,
            'isVerified'=> (int)$seller->verified,
            'creationDate'=> (string)$seller->created_at,
            'lastChange'=> (string)$seller->updated_at,
            'deletedDate'=>isset($seller->deleted_at) ? (string) $seller->deleted_at : null ,
        ];
    }
    public static function originalAttributes($index)
    {
        $attributes = [
            'identifier'=> 'id',
            'name'=> 'name',
            'email'=> 'email',
            'isVerified'=> 'verified',
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
             'name'=> 'name',
             'email'=> 'email',
             'verified'=> 'isVerified',
             'created_at'=> 'creationDate',
             'updated_at'=> 'lastChange',
             'deleted_at'=> 'deletedDate',
        ];

        return isset($attributes[$index]) ? $attributes [$index] : null;
    }
}
