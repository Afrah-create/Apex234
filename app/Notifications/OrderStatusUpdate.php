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
    protected $extra;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus, array $extra = [])
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->extra = $extra;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
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
            'assigned' => 'You have been assigned a new order to process.',
        ];

        $message = $statusMessages[$this->newStatus] ?? 'Your order status has been updated.';

        $mail = (new MailMessage)
            ->subject('Order Status Update - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order status has been updated.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Previous Status: ' . ucfirst($this->oldStatus))
            ->line('New Status: ' . ucfirst($this->newStatus))
            ->line($message);
        if (!empty($this->extra['warehouse_staff_name'])) {
            $mail->line('Assigned Warehouse Staff: ' . $this->extra['warehouse_staff_name']);
        }
        $mail->action('View Order Details', route('customer.orders.show', $this->order->id))
            ->line('Thank you for choosing our services!');
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $delivery = $this->order->delivery;
        $array = [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Order status updated from ' . ucfirst($this->oldStatus) . ' to ' . ucfirst($this->newStatus),
            'type' => 'order_status_update',
            'delivery' => $delivery ? [
                'delivery_id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'scheduled_delivery_date' => $delivery->scheduled_delivery_date,
                'scheduled_delivery_time' => $delivery->scheduled_delivery_time,
                'status' => $delivery->delivery_status,
                'driver_name' => $delivery->driver_name,
                'vehicle_number' => $delivery->vehicle_number,
                'delivery_address' => $delivery->delivery_address,
                'recipient_name' => $delivery->recipient_name,
                'recipient_phone' => $delivery->recipient_phone,
            ] : null,
        ];
        if (!empty($this->extra['warehouse_staff_name'])) {
            $array['warehouse_staff_name'] = $this->extra['warehouse_staff_name'];
        }
        return $array;
    }
} 