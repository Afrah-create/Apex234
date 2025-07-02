<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryNoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $delivery;
    public $pdfContent;

    public function __construct($delivery, $pdfContent)
    {
        $this->delivery = $delivery;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->markdown('emails.delivery-note')
            ->subject('Delivery Note')
            ->attachData($this->pdfContent, 'delivery-note.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
} 