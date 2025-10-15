<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class KitchenDisplayController extends Controller
{
    public function fetchTodayOrders(): JsonResponse
    {
        $pendingStatuses = [28, 29, 30];

        $orders = Order::whereIn('status', $pendingStatuses)
            ->whereDate('created_at', today())
            ->with([
                'orderItemsRS.productRS',
                'userRS',
            ])
            ->orderBy('created_at', 'desc')
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
                'status'          => Status::statusAlert($order->status),
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

    public function checkNewOrders(): JsonResponse
    {
        $pendingStatuses = [28, 29, 30];
        $latestOrderId = Order::whereIn('status', $pendingStatuses)
            ->whereDate('created_at', today())
            ->max('id');

        return response()->json([
            'latest_id' => $latestOrderId ?? 0,
        ]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        // --- 1. DEFINE YOUR STATUS ID MAP ---
        // *** CRITICAL: REPLACE THESE PLACEHOLDER IDs WITH YOUR ACTUAL DATABASE STATUS IDs ***
        $statusMap = [
            'IN QUEUE'  => 28, // Example ID for 'In Queue'
            'IN PREP'   => 29, // Example ID for 'In prep'
            'READY'     => 30, // Example ID for 'Ready'
            'COMPLETED' => 23, // Example ID for 'Completed'
        ];

        // 2. GET & VALIDATE INPUTS
        $orderId = $request->input('order_id');
        // Convert status name to uppercase for case-insensitive lookup
        $newStatusName = strtoupper($request->input('new_status'));
        $newStatusId = $statusMap[$newStatusName] ?? null;

        if (!$newStatusId) {
            return response()->json(['success' => false, 'message' => 'Invalid status requested.'], 400);
        }

        try {
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }

            // 3. UPDATE ORDER STATUS
            $order->status = $newStatusId;
            $order->save();

            // 4. RETURN SUCCESS
            return response()->json([
                'success' => true,
                'message' => "Order status updated to {$newStatusName}",
                'new_status_id' => $newStatusId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
