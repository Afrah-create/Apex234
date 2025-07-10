<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Order;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $sender;

    public function __construct(Order $order, $sender)
    {
        $this->order = $order;
        $this->sender = $sender;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'order_status' => $this->order->order_status,
            'total_amount' => $this->order->total_amount,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message' => 'A new order has been placed.',
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
} 