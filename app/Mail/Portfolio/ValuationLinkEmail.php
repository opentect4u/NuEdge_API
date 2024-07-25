<?php

namespace App\Mail\Portfolio;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ValuationLinkEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $client_name;
    public $valuation_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_name,$valuation_link)
    {
        $this->client_name=$client_name;
        $this->valuation_link=$valuation_link;
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
                    ->view('emails.valuation.mf_details_sum');
    }
}