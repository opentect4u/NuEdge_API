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
            if ($file) {
                $portfolio=$file->getClientOriginalExtension();
                $folio_file_name=(microtime(true)*10000).".".$portfolio;
                $file->move(public_path('portfolio/'),$folio_file_name);
            }
            
            $final_arr=[];
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_arr);
    }
}