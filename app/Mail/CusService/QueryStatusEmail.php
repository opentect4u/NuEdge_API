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
    public $files;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$investor_name,$query_status,$query_status_id,$data,$files)
    {
        $this->subject=$subject;
        $this->investor_name=$investor_name;
        $this->query_status=$query_status;
        $this->query_status_id=$query_status_id;
        $this->data=$data;
        $this->files=$files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from_email=env('MAIL_FROM_ADDRESS');
        $email = $this->from($from_email)
            ->subject($this->subject)
            ->view('emails.customer_service.query_desk_email');
            
        if (count($this->files)>0) {
            foreach ($this->files as $file) {
                $email->attach($file, [
                    'as' => basename($file),
                    'mime' => mime_content_type($file)
                ]);
            }
        }
        return $email; 
    }
}