<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class VendorAssignedOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Order Assigned to You')
            ->greeting('Hello ' . ($notifiable->business_name ?? 'Vendor') . ',')
            ->line('A new order (Order #' . $this->order->order_number . ') has been assigned to you.')
            ->line('Order Date: ' . $this->order->order_date)
            ->line('Total Amount: ' . number_format($this->order->total_amount, 2))
            ->action('View Order', url('/vendor/orders/' . $this->order->id))
            ->line('Thank you for being a valued vendor!');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'order_date' => $this->order->order_date,
            'total_amount' => $this->order->total_amount,
        ];
    }
} 