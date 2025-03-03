<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\Operation\SendAckEmail;
use App\Models\MutualFundTransaction;
use App\Models\MutualFund;

class AckFinalSubmitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $item;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // MutualFundTransaction
        // \Log::info($this->item);
        // \Log::info($this->item['tin_no']);
        $data=MutualFund::join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
                ->select('td_mutual_fund.id','td_mutual_fund.first_client_id','td_mutual_fund.app_form_scan','td_mutual_fund.ack_copy_scan',
                'md_client.client_name as first_client_name','md_client.email as first_client_email','md_client.pan as first_client_pan')
                ->where('td_mutual_fund.tin_no',$this->item['tin_no'])
                ->first();  
        // \Log::info($data);
        $pan=$data['first_client_pan'];
        $name=$data['first_client_name'];
        if($pan!='' && $pan!=null){
            $count=MutualFundTransaction::where('first_client_pan',$pan)->count();
        }else {
            $count=MutualFundTransaction::where('first_client_name',$name)->count();
        }
        $app_form=public_path('application-form/'.$this->item['app_form_scan']);
        $email=$data['first_client_email'];
        // $count=0;
        // \Log::info($count);
        // \Log::info($name);
        // \Log::info($app_form);
        // \Log::info($email);
        Mail::to($email)->send(new SendAckEmail($name,$app_form,$count));
        MutualFund::where('tin_no',$this->item['tin_no'])->update(['ack_final_submit'=>'Y']);
    }
}