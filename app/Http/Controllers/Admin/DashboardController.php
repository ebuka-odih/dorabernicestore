<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'revenue' => Order::whereIn('status', ['processing', 'shipped', 'completed'])->sum('total'),
        ];

        $recentOrders = Order::query()->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
