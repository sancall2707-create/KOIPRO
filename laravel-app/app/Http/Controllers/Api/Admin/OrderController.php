<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get()->map(fn($o) => $this->format($o)));
    }

    public function update(Request $request, int $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $data = $request->validate([
            'status' => 'required|in:pending,processing,preparing,ready,completed,cancelled',
        ]);

        $order->update(['status' => $data['status']]);

        return response()->json($this->format($order->fresh(['items.product'])));
    }

    private function format(Order $order): array
    {
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
            'items'        => $order->items->map(fn($i) => [
                'id'           => $i->id,
                'productId'    => $i->product_id,
                'productName'  => $i->product?->name,
                'productImage' => $i->product?->image,
                'quantity'     => $i->quantity,
                'price'        => (float) $i->price,
                'subtotal'     => (float) $i->subtotal,
            ])->values()->all(),
        ];
    }
}
