<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class ScreenController extends Controller
{
    /**
     * Ekran seçici menüsü — POS, Sipariş, Terminal arası geçiş.
     */
    public function menu()
    {
        return view('screens.menu');
    }

    /**
     * Tam ekran dokunmatik POS satış ekranı.
     */
    public function pos()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $customers  = Customer::orderBy('name')->limit(100)->get();

        return view('screens.pos', compact('categories', 'products', 'customers'));
    }

    /**
     * Dokunmatik sipariş alma ekranı (kafe/restoran).
     */
    public function order()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $products   = Product::where('is_active', true)->orderBy('name')->get();

        return view('screens.order', compact('categories', 'products'));
    }

    /**
     * El terminali ekranı (stok sayım, fiyat sorgulama).
     */
    public function terminal()
    {
        return view('screens.terminal');
    }
}
