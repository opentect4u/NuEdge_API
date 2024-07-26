<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MFTransTypeSubType,
    NAVDetailsSec,
    BrokerChangeTransReport,
    Disclaimer
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;
use Session;
use Illuminate\Support\Facades\Crypt;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Mail; 
use App\Mail\Portfolio\ValuationLinkEmail;
use Illuminate\Support\Facades\Storage;

class PDFController extends Controller
{
    public function generatePDF() 
    {
        // return view('emails.client.test');
        return view('emails.client.test1');
    }

    public function generatePDFTest(Request $request)
    {
        // return view('emails.client.test1');
        // $dataSource=Crypt::decrypt($request->dataSource);
        $dataSource=json_decode($request->dataSource);


        $data=[];
        $pdf = Pdf::setOption(['dpi' => 122, 'defaultFont' => 'arial'])->loadView('emails.client.test1', $data);
        // $pdf = Pdf::setOption(['dpi' => 110, 'defaultFont' => 'sans-serif'])->loadView('emails.client.test1', $data);
        // $pdf->setBasePath(public_path());

        return $pdf->stream(); 
        // return Pdf::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
        // Pdf::loadHTML('emails.client.test1')->setPaper('a4', 'landscape')->setWarnings(false)->save('public/gen-pdf/myfile.pdf');

        // return $pdf->download('invoice.pdf');

        // $pdf = PDF::loadView('emails.client.test1');

        // $content = $pdf->download()->getOriginalContent();
        // $content = $pdf->output();
        // $noOrder=(microtime(true)*10000).'.pdf';
        $noOrder='test.pdf';
        // return $noOrder;
        file_put_contents('public/gen-pdf/'.$noOrder, $pdf->output() );

        return $noOrder;
    }

    public function sendEmailWithLink(Request $request)
    {
        try {
            // return $request;
            $file=$request->file;
            $client_name=$request->client_name;
            $pan_no=$request->pan_no;
            $pan_no=$request->pan_no;
            $guardians_pan=$request->guardians_pan;
            $dob=$request->dob;
            $email=$request->email;
            $phone=$request->phone;
            $flag=$request->flag;
            // $pan_no='BPPPS4831C';
            $folio_file_name='';
            if ($file) {
                $portfolio=$file->getClientOriginalExtension();
                $folio_file_name=uniqid().".".$portfolio;
                $file->move(public_path('portfolio/'),$folio_file_name);
            }
            
            $filePath=public_path('portfolio/'.$folio_file_name);
            $outputPath=public_path('portfolio/'.$folio_file_name);

            $password=($pan_no)?$pan_no:$guardians_pan;
            Helper::encrypt($filePath, $outputPath, $password);

            $path = 'portfolio-valuation/'. $folio_file_name;
            Storage::disk('s3')->put($path, file_get_contents($filePath));
            unlink($filePath);
            $fileUrl = Storage::disk('s3')->url($path);
            // return $fileUrl;
            $token = Str::random(64);
            DB::table('td_portfolio_valuation_details')->insert([
              'url' => $fileUrl, 
              'token' => $token, 
              'created_at' => Carbon::now(),
              'updated_at' => Carbon::now()
            ]);

            if ($flag=='S') {

            } else if($flag=='E' || $flag=='We') {  // for email send
                $valuation_link=env('VALUATION_LINK').$token;
                $email='chittaranjan@synergicsoftek.com';
                $client_name='Chittaranjan Maity';
                Mail::to($email)->send(new ValuationLinkEmail($client_name,$valuation_link));
            }
            $final_arr=[];
            $final_arr['valuation_link']=$valuation_link;
            $final_arr['outputPath']=$outputPath;
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_arr);
    }

    public function downloadValuation(Request $request){
        try {
            $token=$request->token;
            $is_has=DB::table('td_portfolio_valuation_details')->where('token',$token)->get();
            $final_arr=[];
            $final_arr['count']=count($is_has);
            $final_arr['details']=$is_has;
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_arr);
    }
}