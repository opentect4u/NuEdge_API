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

class PDFController extends Controller
{
    public function generatePDF() 
    {
        return view('emails.client.test');
    }
}