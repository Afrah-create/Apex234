<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RawMaterialOrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $oldStatus;
    public $newStatus;

    public function __construct($order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Raw Material Order Status Updated')
            ->line('Your raw material order #' . $this->order->id . ' status has been updated.')
            ->line('Old Status: ' . ucfirst($this->oldStatus))
            ->line('New Status: ' . ucfirst($this->newStatus))
            ->action('View Order', url('/supplier/raw-material-orders/' . $this->order->id))
            ->line('Thank you for using our platform.');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Your raw material order #' . $this->order->id . ' status updated from ' . ucfirst($this->oldStatus) . ' to ' . ucfirst($this->newStatus) . '.',
            'url' => url('/supplier/raw-material-orders/' . $this->order->id),
        ];
    }
} 