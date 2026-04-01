<?php

namespace App\Controllers\Api;

use App\Services\CartService;
use App\Models\CartModel;
use App\Models\CartItemModel;

class Cart extends BaseApiController
{

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['branch_id'])) {
            return $this->error('branch_id required');
        }

        $db = \Config\Database::connect();

        $userModel = new \App\Models\UserModel();
        $cartModel = new \App\Models\CartModel();

        // =========================
        // GET INPUT CUSTOMER
        // =========================
        $name  = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;

        if (!$email) {
            return $this->error('Email required');
        }

        // =========================
        // CEK USER BY EMAIL
        // =========================
        $user = $userModel
            ->where('email', $email)
            ->first();

        if ($user) {

            $userId = $user['id'];

        } else {

            // =========================
            // CREATE USER BARU
            // =========================
            $userId = $userModel->insert([
                'branch_id' => $data['branch_id'],
                'name'      => $name,
                'email'     => $email,
                'phone'     => $phone,
                'password'  => password_hash('123456', PASSWORD_DEFAULT), // default
                'role'      => 'customer',
                'status'    => 'active'
            ]);
        }

        // =========================
        // CREATE CART
        // =========================
        $cartId = $cartModel->insert([
            'user_id'   => $userId,
            'branch_id' => $data['branch_id'],
            'status'    => 'active'
        ]);

        return $this->success([
            'cart_id' => $cartId,
            'user_id' => $userId
        ], 'Cart created');
    }

    public function add()
    {

        $service = new CartService();

        $data = $this->request->getJSON(true);

        $service->addItem($data);

        return $this->success([], 'Item added');

    }

    public function get($userId)
    {
        $db = \Config\Database::connect();

        $cart = $db->table('carts')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$cart) {
            return $this->success([]);
        }

        $items = $db->table('cart_items')
            ->select('
                cart_items.*,
                items.name,
                items.image,
                branches.branch_name
            ')
            ->join('items', 'items.id = cart_items.item_id')
            ->join('carts', 'carts.id = cart_items.cart_id')
            ->join('branches', 'branches.id = carts.branch_id')
            ->where('cart_items.cart_id', $cart['id'])
            ->get()
            ->getResultArray();

        return $this->success([
            'cart'  => $cart,
            'items' => $items
        ]);
    }

    public function update($id = null)
    {

        $data = $this->request->getJSON(true);

        if (!isset($data['cart_item_id']) || !isset($data['quantity'])) {
            return $this->error('cart_item_id and quantity required');
        }

        $cartItemModel = new CartItemModel();

        $cartItemModel->update($data['cart_item_id'], [
            'quantity' => $data['quantity']
        ]);

        return $this->success([], 'Cart updated');

    }

    public function remove()
    {

        $data = $this->request->getJSON(true);

        if (!isset($data['cart_item_id'])) {
            return $this->error('cart_item_id required');
        }

        $cartItemModel = new CartItemModel();

        $cartItemModel->delete($data['cart_item_id']);

        return $this->success([], 'Item removed');

    }

    public function getAll($userId)
    {
        $db = \Config\Database::connect();

        // 🔥 ambil semua cart user
        $carts = $db->table('carts')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        if (!$carts) {
            return $this->success([]);
        }

        $result = [];

        foreach ($carts as $cart) {

            $items = $db->table('cart_items')
                ->select('
                    cart_items.*,
                    items.name,
                    items.image,
                    branches.branch_name
                ')
                ->join('items', 'items.id = cart_items.item_id')
                ->join('carts', 'carts.id = cart_items.cart_id')
                ->join('branches', 'branches.id = carts.branch_id')
                ->where('cart_items.cart_id', $cart['id'])
                ->get()
                ->getResultArray();

            $result[] = [
                'cart'  => $cart,
                'items' => $items
            ];
        }

        return $this->success($result);
    }
}