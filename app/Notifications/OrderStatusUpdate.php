<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'confirmed' => 'Your order has been confirmed and is being prepared for processing.',
            'processing' => 'Your order is now being processed and prepared for shipping.',
            'shipped' => 'Your order has been shipped and is on its way to you.',
            'delivered' => 'Your order has been delivered successfully!',
            'cancelled' => 'Your order has been cancelled.',
        ];

        $message = $statusMessages[$this->newStatus] ?? 'Your order status has been updated.';

        return (new MailMessage)
            ->subject('Order Status Update - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order status has been updated.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Previous Status: ' . ucfirst($this->oldStatus))
            ->line('New Status: ' . ucfirst($this->newStatus))
            ->line($message)
            ->action('View Order Details', route('customer.orders.show', $this->order->id))
            ->line('Thank you for choosing our services!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Order status updated from ' . ucfirst($this->oldStatus) . ' to ' . ucfirst($this->newStatus),
            'type' => 'order_status_update',
        ];
    }
} 