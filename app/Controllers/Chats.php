<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class Chats extends BaseController
{

    public function index()
    {
        return $this->render('ecommerce/chat-list', [
            'title'   => 'Chats'
        ]);
    }
}