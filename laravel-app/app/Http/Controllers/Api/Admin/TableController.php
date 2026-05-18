<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoffeeTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        return response()->json(CoffeeTable::orderBy('table_number')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tableNumber' => 'required|string|max:50|unique:coffee_tables,table_number',
            'status'      => 'sometimes|in:available,occupied',
        ]);

        $table = CoffeeTable::create([
            'table_number' => $data['tableNumber'],
            'status'       => $data['status'] ?? 'available',
        ]);

        return response()->json($table, 201);
    }

    public function update(Request $request, int $id)
    {
        $table = CoffeeTable::find($id);
        if (!$table) {
            return response()->json(['error' => 'Table not found'], 404);
        }

        $data = $request->validate([
            'tableNumber' => 'sometimes|string|max:50',
            'status'      => 'sometimes|in:available,occupied',
        ]);

        $mapped = [];
        if (isset($data['tableNumber'])) $mapped['table_number'] = $data['tableNumber'];
        if (isset($data['status'])) $mapped['status'] = $data['status'];

        $table->update($mapped);
        return response()->json($table);
    }

    public function destroy(int $id)
    {
        $table = CoffeeTable::find($id);
        if (!$table) {
            return response()->json(['error' => 'Table not found'], 404);
        }

        $table->delete();
        return response()->json(null, 204);
    }
}
