<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class NSEController extends Controller
{
    public function index()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://www.nsenmf.com/NMFIITrxnService/NMFTrxnService/IINDETAILS',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="UTF-8"?>
<NMFIIService>
    <service_request>
        <appln_id></appln_id>
        <password></password>
        <broker_code></broker_code>
        <iin>5014342826</iin>
    </service_request>
</NMFIIService>',
CURLOPT_HTTPHEADER => array(
'Content-Type: application/xml',
),
));

$response = curl_exec($curl);

curl_close($curl);
// echo $response;


$xml=$this->XMlToJSON($response);
// return $xml;
// return $xml['DataSet']['diffgr:diffgram']['NMFIISERVICES'];
$service_status=$xml['DataSet']['diffgr:diffgram']['NMFIISERVICES']['service_status'];
// return $service_status;
$data=[];
foreach ($service_status as $key => $value) {
// return $key;
if(!str_starts_with($key, '@')){
$data[$key]=$service_status[$key]['$'];
}
}
// return $data;

$service_response=$xml['DataSet']['diffgr:diffgram']['NMFIISERVICES']['service_response'];
// return $service_response;
$service_response_data=[];
foreach ($service_response as $key => $value) {
// return $key;
if(!str_starts_with($key, '@')){
$service_response_data[$key]=isset($service_response[$key]['$'])?$service_response[$key]['$']:'';
}
// array_push($data,$mydata);
// $data[$key]=isset($value[$key]['$'])?$value[$key]['$']:'';

}
$data['response_data']=$service_response_data;
return $data;

}



public function XMlToJSON($return){
$dom = new \DOMDocument();
$dom->loadXML($return);
$json = new \FluentDOM\Serializer\Json\RabbitFish($dom);
$object = json_decode($json,true);
return $object;
}
}