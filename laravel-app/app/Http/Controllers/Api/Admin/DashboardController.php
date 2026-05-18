<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders  = Order::count();
        $activeOrders = Order::whereIn('status', ['pending', 'processing', 'preparing', 'ready'])->count();
        $dailySales   = Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        $recentOrders = Order::with(['items.product'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn($o) => $this->formatOrder($o));

        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(fn($r) => ['status' => $r->status, 'count' => (int) $r->count]);

        return response()->json([
            'totalOrders'    => $totalOrders,
            'activeOrders'   => $activeOrders,
            'dailySales'     => (float) $dailySales,
            'recentOrders'   => $recentOrders,
            'ordersByStatus' => $ordersByStatus,
        ]);
    }

    private function formatOrder(Order $order): array
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
