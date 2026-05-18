<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('status', 'active')
            ->orderBy('id');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->get()->map(fn($p) => $this->format($p)));
    }

    public function show(int $id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($this->format($product));
    }

    private function format(Product $p): array
    {
        return [
            'id'           => $p->id,
            'categoryId'   => $p->category_id,
            'categoryName' => $p->category?->name,
            'name'         => $p->name,
            'description'  => $p->description,
            'price'        => (float) $p->price,
            'image'        => $p->image,
            'stock'        => $p->stock,
            'status'       => $p->status,
            'createdAt'    => (string) $p->created_at,
        ];
    }
}
