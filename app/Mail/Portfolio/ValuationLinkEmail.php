<?php

namespace App\Mail\Portfolio;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ValuationLinkEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $email;
    public $LeaderName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($LeaderName,$email)
    {
        $this->LeaderName=$LeaderName;
        $this->LeaderName=$LeaderName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from_email=env('MAIL_FROM_ADDRESS');
        return $this->from($from_email)
                    ->subject('NuEdge Corporate - Valuation Details')
                    ->view('emails.hotel.register-invoice');
    }
}