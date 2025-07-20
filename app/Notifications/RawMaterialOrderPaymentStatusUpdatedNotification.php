<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class RawMaterialOrderPaymentStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $supplier;

    public function __construct($order, $supplier)
    {
        $this->order = $order;
        $this->supplier = $supplier;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Raw Material Order Payment Status Updated')
            ->line('Supplier "' . ($this->supplier->name ?? 'Supplier') . '" has updated the payment status for Raw Material Order #' . $this->order->id . '.')
            ->line('New Payment Status: ' . ucfirst($this->order->payment_status ?? 'pending'))
            ->action('View Order', url('/admin/raw-material-orders/' . $this->order->id))
            ->line('Thank you.');
    }

    public function toArray($notifiable)
    {
        // Determine the correct URL based on the notifiable's role
        $orderId = $this->order->id;
        $isAdmin = method_exists($notifiable, 'roles') && $notifiable->roles()->where('name', 'admin')->exists();
        $isSupplier = method_exists($notifiable, 'supplier') && $notifiable->supplier;
        $isVendor = method_exists($notifiable, 'vendor') && $notifiable->vendor;
        if ($isAdmin) {
            $url = url('/admin/raw-material-orders/' . $orderId);
        } elseif ($isSupplier || $isVendor) {
            $url = route('notifications.index');
        } else {
            $url = url('/admin/raw-material-orders/' . $orderId);
        }
        return [
            'order_id' => $orderId,
            'payment_status' => $this->order->payment_status,
            'supplier_name' => $this->supplier->name ?? 'Supplier',
            'message' => 'Supplier "' . ($this->supplier->name ?? 'Supplier') . '" updated payment status for Raw Material Order #' . $orderId . ' to ' . ucfirst($this->order->payment_status ?? 'pending') . '.',
            'url' => $url,
        ];
    }
} 