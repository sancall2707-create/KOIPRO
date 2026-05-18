<?php

namespace App\Http\Controllers;

class OrderTrackingController extends Controller
{
    public function show(string $orderNumber)
    {
        return view('orders.show', ['orderNumber' => $orderNumber]);
    }
}
