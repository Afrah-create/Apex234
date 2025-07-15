<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\RawMaterialOrder;

class RawMaterialOrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $vendor;

    public function __construct(RawMaterialOrder $order, $vendor)
    {
        $this->order = $order;
        $this->vendor = $vendor;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'vendor_id' => $this->vendor->id,
            'vendor_name' => $this->vendor->name,
            'material_type' => $this->order->material_type,
            'material_name' => $this->order->material_name,
            'quantity' => $this->order->quantity,
            'unit_of_measure' => $this->order->unit_of_measure,
            'message' => 'New raw material order placed by ' . $this->vendor->name . ' for ' . $this->order->quantity . ' ' . $this->order->unit_of_measure . ' of ' . $this->order->material_name . '.',
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