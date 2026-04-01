<?php

namespace App\Services;

class CartService
{

    public function addItem($data)
    {

        $db = \Config\Database::connect();

        $item = $db->table('items')
        ->where('id', $data['item_id'])
        ->get()
        ->getRow();

        $db->table('cart_items')->insert([
            'cart_id'   => $data['cart_id'],
            'item_id'   => $data['item_id'],
            'quantity'  => $data['quantity'],
            'price'     => $data['price'],
            'created_at'=> $data['date'],
        ]);

        return true;

    }

}