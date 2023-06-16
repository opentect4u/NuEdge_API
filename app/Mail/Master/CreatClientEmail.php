<?php

namespace App\Mail\Master;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreatClientEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $client_name;
    public $subject;
    public $body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_name,$subject,$body)
    {
        $this->client_name=$client_name;
        $this->subject=$subject;
        $this->body=$body;
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
                    ->subject($this->subject)
                    ->view('emails.master.create-client');
                    // ->attachData($this->pdf->output(), 'invoice.pdf', [
                    //     'mime' => 'application/pdf',
                    // ]);
    }
}
