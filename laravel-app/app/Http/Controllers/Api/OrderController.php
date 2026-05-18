<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customerName' => 'required|string|max:255',
            'tableNumber'  => 'nullable|string',
            'orderType'    => 'required|in:dine_in,takeaway',
            'notes'        => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.productId' => 'required|integer|exists:products,id',
            'items.*.quantity'  => 'required|integer|min:1',
        ]);

        $productIds = collect($data['items'])->pluck('productId')->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $totalPrice = 0;
        $enriched = [];

        foreach ($data['items'] as $item) {
            $product = $products->get($item['productId']);
            if (!$product) {
                return response()->json(['error' => "Product {$item['productId']} not found"], 400);
            }
            $price    = (float) $product->price;
            $subtotal = $price * $item['quantity'];
            $totalPrice += $subtotal;
            $enriched[] = [
                'product'  => $product,
                'quantity' => $item['quantity'],
                'price'    => $price,
                'subtotal' => $subtotal,
            ];
        }

        $order = DB::transaction(function () use ($data, $totalPrice, $enriched) {
            $order = Order::create([
                'order_number'  => $this->generateOrderNumber(),
                'customer_name' => $data['customerName'],
                'table_number'  => $data['tableNumber'] ?? null,
                'order_type'    => $data['orderType'],
                'status'        => 'pending',
                'notes'         => $data['notes'] ?? null,
                'total_price'   => $totalPrice,
            ]);

            foreach ($enriched as $ei) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $ei['product']->id,
                    'quantity'   => $ei['quantity'],
                    'price'      => $ei['price'],
                    'subtotal'   => $ei['subtotal'],
                ]);
            }

            return $order;
        });

        return response()->json($this->formatOrder($order), 201);
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($this->formatOrder($order));
    }

    private function formatOrder(Order $order): array
    {
        $items = $order->items()->with('product')->get()->map(fn($i) => [
            'id'           => $i->id,
            'productId'    => $i->product_id,
            'productName'  => $i->product?->name,
            'productImage' => $i->product?->image,
            'quantity'     => $i->quantity,
            'price'        => (float) $i->price,
            'subtotal'     => (float) $i->subtotal,
        ])->values()->all();

        return [
            'id'           => $order->id,
            'orderNumber'  => $order->order_number,
            'customerName' => $order->customer_name,
            'tableNumber'  => $order->table_number,
            'orderType'    => $order->order_type,
            'status'       => $order->status,
            'notes'        => $order->notes,
            'totalPrice'   => (float) $order->total_price,
            'createdAt'    => (string) $order->created_at,
            'items'        => $items,
        ];
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $rand = rand(1000, 9999);
        return "KTA-{$date}-{$rand}";
    }
}
