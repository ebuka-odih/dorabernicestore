<?php

namespace App\Http\Controllers;

class AccountController extends Controller
{
    public function orders()
    {
        $orders = auth()->user()->orders()->with('items')->latest()->paginate(10);

        return view('account.orders', compact('orders'));
    }
}
