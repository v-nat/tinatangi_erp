<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewOrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load([
            'orderItemsRS.productRS',
            'userRS',
            'statusRS'
        ]);
    }

    public function broadcastOn(): array
    {
        // KDS subscribes to this channel
        return [
            new Channel('pos-orders'),
        ];
    }

    public function broadcastAs()
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        $order = $this->order;

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
    }
}
