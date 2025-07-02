<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DeliveryNoteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $delivery;

    public function __construct($delivery)
    {
        $this->delivery = $delivery;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'delivery_id' => $this->delivery->id,
            'delivery_number' => $this->delivery->delivery_number,
            'scheduled_delivery_date' => $this->delivery->scheduled_delivery_date,
            'scheduled_delivery_time' => $this->delivery->scheduled_delivery_time,
            'status' => $this->delivery->delivery_status,
            'driver_name' => $this->delivery->driver_name,
            'vehicle_number' => $this->delivery->vehicle_number,
            'delivery_address' => $this->delivery->delivery_address,
            'recipient_name' => $this->delivery->recipient_name,
            'recipient_phone' => $this->delivery->recipient_phone,
            'message' => 'A new delivery has been created and scheduled.',
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