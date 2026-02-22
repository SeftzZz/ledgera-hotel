<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return redirect()->to('/login');
    }

    // public function index(): string
    // {
    //     return view('welcome_message');
    // }
}
