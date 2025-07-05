<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeAssignedToVendor extends Notification
{
    use Queueable;

    public $role;
    public $vendor;

    /**
     * Create a new notification instance.
     */
    public function __construct($role, $vendor)
    {
        $this->role = $role;
        $this->vendor = $vendor;
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
        return (new MailMessage)
            ->subject('You have been assigned a new role and vendor')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned the role of ' . $this->role . '.')
            ->line('Vendor Details:')
            ->line('Business Name: ' . ($this->vendor->business_name ?? 'N/A'))
            ->line('Address: ' . ($this->vendor->business_address ?? 'N/A'))
            ->line('Phone: ' . ($this->vendor->phone_number ?? 'N/A'))
            ->line('Contact Person: ' . ($this->vendor->contact_person ?? 'N/A'))
            ->line('Contact Email: ' . ($this->vendor->contact_email ?? 'N/A'))
            ->line('Status: ' . ucfirst($this->vendor->status ?? 'N/A'))
            ->action('View Dashboard', url('/dashboard/employee'))
            ->line('Thank you for being part of our team!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'role' => $this->role,
            'vendor' => [
                'business_name' => $this->vendor->business_name ?? 'N/A',
                'business_address' => $this->vendor->business_address ?? 'N/A',
                'phone_number' => $this->vendor->phone_number ?? 'N/A',
                'contact_person' => $this->vendor->contact_person ?? 'N/A',
                'contact_email' => $this->vendor->contact_email ?? 'N/A',
                'status' => $this->vendor->status ?? 'N/A',
            ],
            'message' => 'You have been assigned the role of ' . $this->role . ' at ' . ($this->vendor->business_name ?? 'a vendor') . '.',
        ];
    }
}
