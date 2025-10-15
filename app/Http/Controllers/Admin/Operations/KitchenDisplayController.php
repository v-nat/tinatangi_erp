<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use App\Models\Order; // Add this model
use Illuminate\Http\JsonResponse; // Add this class
use App\Models\Status; // Add this model

class KitchenDisplayController extends Controller
{
    public function fetchTodayOrders(): JsonResponse
    {
        // Status IDs that the kitchen should be working on (e.g., New, Pending, Cooking)
        $pendingStatuses = [28, 29, 30]; // Using your provided IDs

        $orders = Order::whereIn('status', $pendingStatuses)
                       ->whereDate('created_at', today())
                       ->with([
                           'orderItemsRS.productRS',
                           'userRS',
                       ])
                       ->orderBy('created_at', 'asc')
                       ->get();

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id'              => $order->id,
                'order_id'        => $order->order_id,
                'total_amount'    => number_format($order->total_amount, 2),
                'order_type'      => strtoupper($order->order_type),
                'payment_method'  => strtoupper($order->payment_method),
                'created_at'      => $order->created_at->format('M d, Y h:i A'),
                'cashier_name'    => $order->userRS->full_name ?? 'N/A',
                'status'          => Status::getStatusText($order->status),
                'items'           => $order->orderItemsRS->map(function ($item) {
                    return [
                        'product_name' => $item->productRS->name ?? 'N/A',
                        'quantity'     => $item->quantity,
                        'subtotal'     => number_format($item->subtotal, 2),
                    ];
                }),
            ];
        });

        return response()->json($formattedOrders);
    }
}
