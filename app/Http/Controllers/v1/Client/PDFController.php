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
        $pdf = Pdf::loadView('emails.client.test1', $data);

        // return Pdf::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
        // Pdf::loadHTML('emails.client.test1')->setPaper('a4', 'landscape')->setWarnings(false)->save('public/gen-pdf/myfile.pdf');

        // return $pdf->download('invoice.pdf');

        // $pdf = PDF::loadView('emails.client.test1');

        // $content = $pdf->download()->getOriginalContent();
        // $content = $pdf->output();
        $noOrder=(microtime(true)*10000).'.pdf';
        // return $noOrder;
        file_put_contents('public/gen-pdf/'.$noOrder, $pdf->output() );

        return $noOrder;
    }
}