<?php

namespace App\Mail\Operation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAckEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $app_form;
    public $count;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$app_form,$count)
    {
        $this->name=$name;
        $this->app_form=$app_form;
        $this->count=$count;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->count > 0) {
            # code...
        }else {
            $email=$this->from(env('MAIL_FROM_ADDRESS'))
                ->subject('Welome Mail')
                ->view('emails.operation.welcome');
                    
            $email->attach($this->app_form, [
                'as' => basename($this->app_form),
                'mime' => mime_content_type($this->app_form)
            ]);
            return $email;
        }
    }
}