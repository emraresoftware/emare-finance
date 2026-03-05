<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $paymentTypes = PaymentType::orderBy('sort_order')->get();
        return view('payment-types.index', compact('paymentTypes'));
    }
}
