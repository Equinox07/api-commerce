<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'identifier'=> $user->id,
            'name'=> (string)$user->name,
            'email'=> (string)$user->email,
            'isVerified'=> (int)$user->verified,
            'isAdmin'=> ($user->admin === 'true'),
            'creationDate'=> (string)$user->created_at,
            'lastChange'=> (string)$user->updated_at,
            'deletedDate'=>isset($user->deleted_at) ? (string) $user->deleted_at : null ,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('users.show', $user->id),
                ],
                // [
                //     'rel' => 'transaction.categories',
                //     'href' => route('transactions.categories.index', $transaction->id),
                // ],
                // [
                //     'rel' => 'transaction.seller',
                //     'href' => route('transactions.sellers.index', $transaction->id),
                // ],
                // [
                //     'rel' => 'product',
                //     'href' => route('products.show', $transaction->product_id),
                // ],
                // [
                //     'rel' => 'buyer',
                //     'href' => route('buyers.show', $transaction->buyer_id),
                // ],
            ]
        ];
    }

    public static function originalAttributes($index)
    {
        $attributes = [
            'identifier'=> 'id',
            'name'=> 'name',
            'email'=> 'email',
            'isVerified'=> 'verified',
            'isAdmin'=> 'admin',
            'creationDate'=> 'created_at',
            'lastChange'=> 'updated_at',
            'deletedDate'=>'deleted_at'
        ];

        return isset($attributes[$index]) ? $attributes [$index] : null;
    }
    public static function transformedAttributes($index)
    {
        $attributes = [
            'id'=>  'identifier' ,
            'name'=>  'name' ,
            'email'=>  'email' ,
            'verified'=>  'isVerified' ,
            'admin'=>  'isAdmin',
            'created_at'=>  'creationDate' ,
            'updated_at'=>  'lastChange' ,
            'deleted_at'=>  'deletedDate' ,
        ];

        return isset($attributes[$index]) ? $attributes [$index] : null;
    }
}
