<?php

namespace App\Mail\CusService;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QueryStatusEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $investor_name;
    public $query_status;
    public $query_status_id;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$investor_name,$query_status,$query_status_id,$data)
    {
        $this->subject=$subject;
        $this->investor_name=$investor_name;
        $this->query_status=$query_status;
        $this->query_status_id=$query_status_id;
        $this->data=$data;
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
                    ->view('emails.customer_service.query_desk_email');
                    // ->attachData($this->pdf->output(), 'invoice.pdf', [
                    //     'mime' => 'application/pdf',
                    // ]);
    }
}