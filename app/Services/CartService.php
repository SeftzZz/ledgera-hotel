<?php

namespace App\Services;

class CartService
{
    public function addItem($data)
    {
        $db = \Config\Database::connect();

        // =========================
        // 🔥 1. CEK VENDOR ITEM (PRIORITAS)
        // =========================
        if (!empty($data['item_id'])) {

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
        }

        // =========================
        // 🔥 2. CEK ITEM DARI CATEGORY (HEYWORK)
        // =========================
        if (!empty($data['category'])) {

            $item = $db->table('items')
                ->where('code', $data['category'])
                ->where('status', 'available')
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

            throw new \Exception('Item tidak ditemukan untuk category: ' . $data['category']);
        }

        // =========================
        // 🔥 3. FALLBACK ITEM BIASA (LEDGERA)
        // =========================
        if (!empty($data['item_id'])) {

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
        }

        // =========================
        // ❌ NOT FOUND
        // =========================
        throw new \Exception('Item tidak ditemukan');
    }
}