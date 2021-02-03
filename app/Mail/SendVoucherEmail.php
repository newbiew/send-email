<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVoucherEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('SENDER_EMAIL','sender@sender.com'),env('SENDER_NAME','Sender'))
        ->subject("Happy Lunar New Year")
        ->view('mail.voucher-email');

    }
}
