<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('id')->get();
        return response()->json($products->map(fn($p) => $this->format($p)));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'categoryId'  => 'nullable|integer|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|string',
            'stock'       => 'required|integer|min:0',
            'status'      => 'required|in:active,inactive',
        ]);

        $product = Product::create([
            'name'        => $data['name'],
            'category_id' => $data['categoryId'] ?? null,
            'description' => $data['description'] ?? null,
            'price'       => $data['price'],
            'image'       => $data['image'] ?? null,
            'stock'       => $data['stock'],
            'status'      => $data['status'],
        ]);

        return response()->json($this->format($product->load('category')), 201);
    }

    public function update(Request $request, int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'categoryId'  => 'nullable|integer|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'image'       => 'nullable|string',
            'stock'       => 'sometimes|integer|min:0',
            'status'      => 'sometimes|in:active,inactive',
        ]);

        $mapped = [];
        if (isset($data['name'])) $mapped['name'] = $data['name'];
        if (array_key_exists('categoryId', $data)) $mapped['category_id'] = $data['categoryId'];
        if (array_key_exists('description', $data)) $mapped['description'] = $data['description'];
        if (isset($data['price'])) $mapped['price'] = $data['price'];
        if (array_key_exists('image', $data)) $mapped['image'] = $data['image'];
        if (isset($data['stock'])) $mapped['stock'] = $data['stock'];
        if (isset($data['status'])) $mapped['status'] = $data['status'];

        $product->update($mapped);

        return response()->json($this->format($product->load('category')));
    }

    public function destroy(int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(null, 204);
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
