<?php

namespace App\Services;

class CartService
{

    public function addItem($data)
    {
        $db = \Config\Database::connect();

        // =========================
        // MODE: VENDOR ITEM
        // =========================
        if (!empty($data['vendor_item_id'])) {

            $vendorItem = $db->table('vendor_items vi')
                ->select('vi.*, v.id as vendor_id, v.name as vendor_name')
                ->join('vendors v', 'v.id = vi.vendor_id', 'left')
                ->where('vi.id', $data['vendor_item_id'])
                ->get()
                ->getRow();

            if (!$vendorItem) {
                throw new \Exception('Vendor item tidak ditemukan');
            }

            $db->table('cart_items')->insert([
                'cart_id'        => $data['cart_id'],
                'item_id'        => $vendorItem->id,
                'vendor_item_id' => null,
                'source'         => 'vendor_items',
                'quantity'       => $data['quantity'],
                'price'          => $data['price'],
                'created_at'     => $data['date'],
            ]);

        } 
        // =========================
        // MODE: ITEM BIASA
        // =========================
        else {

            $item = $db->table('items')
                ->where('id', $data['item_id'])
                ->get()
                ->getRow();

            if (!$item) {
                throw new \Exception('Item tidak ditemukan');
            }

            $db->table('cart_items')->insert([
                'cart_id'        => $data['cart_id'],
                'item_id'        => $item->id,
                'vendor_item_id' => null,
                'source'         => 'items',
                'quantity'       => $data['quantity'],
                'price'          => $data['price'],
                'created_at'     => $data['date'],
            ]);
        }

        return true;
    }

}