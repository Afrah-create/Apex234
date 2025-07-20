<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RawMaterialOrderDeliveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Raw Material Order Delivered')
            ->line('Your raw material order #' . $this->order->id . ' has been delivered.')
            ->line('Material: ' . $this->order->material_name)
            ->line('Quantity: ' . $this->order->quantity . ' ' . $this->order->unit_of_measure)
            ->action('View Order', url('/supplier/raw-material-orders/' . $this->order->id))
            ->line('Thank you for using our platform.');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'material_name' => $this->order->material_name,
            'quantity' => $this->order->quantity,
            'unit_of_measure' => $this->order->unit_of_measure,
            'message' => 'Your raw material order #' . $this->order->id . ' has been delivered.',
            'url' => url('/supplier/raw-material-orders/' . $this->order->id),
        ];
    }
} 