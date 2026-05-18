<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TableController extends Controller
{
    public function index()
    {
        return view('admin.tables.index');
    }
}
