<?php

namespace App\Services;

class CartService
{

    public function addItem($data)
    {
        $db = \Config\Database::connect();

        // =========================
        // CEK VENDOR ITEM
        // =========================
        $vendorItem = $db->table('vendor_items')
            ->where('id', $data['item_id'])
            ->get()
            ->getRow();

        if ($vendorItem) {
            $db->table('cart_items')->insert([
                'cart_id'    => $data['cart_id'],
                'item_id'    => $vendorItem->id,
                'quantity'   => $data['quantity'],
                'price'      => $data['price'],
                'created_at' => $data['date'],
            ]);
            return true;
        }

        // =========================
        // CEK ITEM BIASA
        // =========================
        $item = $db->table('items')
            ->where('id', $data['item_id'])
            ->get()
            ->getRow();

        if ($item) {
            $db->table('cart_items')->insert([
                'cart_id'    => $data['cart_id'],
                'item_id'    => $item->id,
                'quantity'   => $data['quantity'],
                'price'      => $data['price'],
                'created_at' => $data['date'],
            ]);
            return true;
        }

        throw new \Exception('Item tidak ditemukan');
    }
}