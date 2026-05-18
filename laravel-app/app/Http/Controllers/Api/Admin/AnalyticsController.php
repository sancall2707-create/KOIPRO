<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        $from = $request->filled('from')
            ? \Carbon\Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? \Carbon\Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        return response()->json([
            'period'         => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'summary'        => $this->getSummary($from, $to),
            'ordersByStatus' => $this->getOrdersByStatus($from, $to),
            'topProducts'    => $this->getTopProducts($from, $to),
            'dailySales'     => $this->getDailySales($from, $to),
            'recentOrders'   => $this->getRecentOrders($from, $to),
        ]);
    }

    private function getSummary($from, $to): array
    {
        $orders = Order::whereBetween('created_at', [$from, $to]);

        $totalOrders    = (clone $orders)->count();
        $completedOrders = (clone $orders)->where('status', 'completed')->count();
        $cancelledOrders = (clone $orders)->where('status', 'cancelled')->count();
        $activeOrders   = (clone $orders)->whereIn('status', ['pending','processing','preparing','ready'])->count();
        $totalRevenue   = (clone $orders)->where('status', '!=', 'cancelled')->sum('total_price');
        $avgOrderValue  = $totalOrders > 0 ? $totalRevenue / max($completedOrders + $activeOrders, 1) : 0;

        return [
            'totalOrders'     => $totalOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'activeOrders'    => $activeOrders,
            'totalRevenue'    => (float) $totalRevenue,
            'avgOrderValue'   => round((float) $avgOrderValue),
        ];
    }

    private function getOrdersByStatus($from, $to): array
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->get()
            ->map(fn($r) => ['status' => $r->status, 'count' => (int) $r->count])
            ->values()
            ->all();
    }

    private function getTopProducts($from, $to): array
    {
        return OrderItem::select(
                'order_items.product_id',
                DB::raw('MAX(products.name) as product_name'),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'productId'    => $r->product_id,
                'productName'  => $r->product_name,
                'totalQty'     => (int) $r->total_quantity,
                'totalRevenue' => (float) $r->total_revenue,
                'orderCount'   => (int) $r->order_count,
            ])
            ->values()
            ->all();
    }

    private function getDailySales($from, $to): array
    {
        return Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as order_count'),
                DB::raw("SUM(CASE WHEN status != 'cancelled' THEN total_price ELSE 0 END) as revenue")
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn($r) => [
                'date'       => $r->date,
                'orderCount' => (int) $r->order_count,
                'revenue'    => (float) $r->revenue,
            ])
            ->values()
            ->all();
    }

    private function getRecentOrders($from, $to): array
    {
        return Order::with(['items.product'])
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn($o) => [
                'id'           => $o->id,
                'orderNumber'  => $o->order_number,
                'customerName' => $o->customer_name,
                'tableNumber'  => $o->table_number,
                'orderType'    => $o->order_type,
                'status'       => $o->status,
                'totalPrice'   => (float) $o->total_price,
                'createdAt'    => $o->created_at->format('Y-m-d H:i'),
                'items'        => $o->items->map(fn($i) => [
                    'productName' => $i->product?->name,
                    'quantity'    => $i->quantity,
                    'subtotal'    => (float) $i->subtotal,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }
}
