<?php

namespace App\Mail\Operation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAckEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $LeaderName;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($LeaderName,$email,$passwords,$pdf)
    {
        $this->LeaderName=$LeaderName;
        $this->email=$email;
        $this->passwords=$passwords;
        $this->pdf=$pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('view.name');
        $from_email='info@puriurbanruralcoop.com';
        return $this->from($from_email)
                    ->subject('Cloud Travel - Login Details and Invoice')
                    ->view('emails.hotel.register-invoice')
                    ->attachData($this->pdf->output(), 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
