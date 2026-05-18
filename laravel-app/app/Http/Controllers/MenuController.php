<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        return view('menu.index', ['tableNumber' => $request->query('table')]);
    }

    public function show(int $id, Request $request)
    {
        return view('menu.show', ['productId' => $id, 'tableNumber' => $request->query('table')]);
    }
}
