<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Password Reset Code - Caramel Yogurt')
            ->view('emails.reset-password-token', [
                'token' => $this->token,
                'user' => $notifiable,
            ]);
    }
} 