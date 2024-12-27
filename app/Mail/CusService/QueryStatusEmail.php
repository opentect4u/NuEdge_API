<?php

namespace App\Mail\CusService;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class QueryStatusEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $investor_name;
    public $query_status;
    public $query_status_id;
    public $data;
    public $files;
    public $total_client_count;
    public $total_amu_balance;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$investor_name,$query_status,$query_status_id,$data,$files,$total_client_count,$total_amu_balance)
    {
        $this->subject=$subject;
        $this->investor_name=$investor_name;
        $this->query_status=$query_status;
        $this->query_status_id=$query_status_id;
        $this->data=$data;
        $this->files=$files;
        $this->total_client_count=$total_client_count;
        $this->total_amu_balance=$total_amu_balance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $html = view('emails.customer_service.query_desk_email', [
        //     'investor_name'=> $this->investor_name,
        //     'data' => $this->data,
        //     'query_status'=> $this->query_status,
        //     'query_status_id'=> $this->query_status_id,
        //     'total_client_count'=> $this->total_client_count,
        //     ])->render();
        // $css = file_get_contents(public_path('css/email.css'));

        // $inliner = new CssToInlineStyles();
        // $htmlWithInlineCss = $inliner->convert($html, $css);
            // dd($htmlWithInlineCss);
        // return $htmlWithInlineCss;
        
        $from_email=env('MAIL_FROM_ADDRESS');
        $email = $this->from($from_email)
            ->subject($this->subject)
            // ->html($htmlWithInlineCss);
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