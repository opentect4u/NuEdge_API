<?php
namespace App\Helpers;
use App\Helpers\Helper;

class SMSHelper {
    public static function createShortUrl($url)
    {
        $apiKey = urlencode(env('SMS_API_KEY'));
        $data = array('apikey' => $apiKey, 'url' => $url);
        $ch = curl_init(env('SMS_CREATE_SHORT_URL'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response=json_decode($response);
        return $response;
    }

    
    public static function send($mobile_no,$message)
    {
        // $mobile_no=(int)$mobile_no;
        $apiKey = urlencode(env('SMS_API_KEY'));
        // Message details
        // $numbers = array(11111111, 918987654321);
        $numbers = array($mobile_no);
        $sender = urlencode(env('SMS_SENDER_NAME'));
        $message = rawurlencode($message);
        // return $message;
        $numbers = implode(',', $numbers);
        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
        // Send the POST request with cURL
        $ch = curl_init(env('SMS_SEND_URL'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        // echo $response;
        return $response;
    }

    public function registerReOpen($mobile_no,$short_url,$query_status,$investor_name,$query_id)
    {
        $message='Dear '.$investor_name.',

Greetings from NuEdge Corporate Private Limited

Your below query is '.$query_status.'.

Query Id- '.$query_id.'.

Query Details- '.str_replace("https://","https://\r",$short_url).'.


Now you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.


Regards,
NuEdge Corporate Private Limited.
AMFI- Registered Mutual Fund Distributor

Mutual Fund investments are subject to market risks, read all scheme related documents carefully.';
        $data=SMSHelper::send($mobile_no,$message);
            //throw $th;
        return $data;
    }

    public function inReinProcess($mobile_no,$short_url,$query_status,$investor_name,$query_id,$expected_close_date)
    {
        $message='Dear '.$investor_name.',

Greetings from NuEdge Corporate Private Limited

Your below query is '.$query_status.'.

Query Id- '.$query_id.'.

Query Details- '.str_replace("https://","https://\r",$short_url).'.

Expected Close Date- '.$expected_close_date.'.

Now you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.

We sincerely regret the inconvenience caused by the delay.


Regards,
NuEdge Corporate Private Limited.
AMFI- Registered Mutual Fund Distributor

Mutual Fund investments are subject to market risks, read all scheme related documents carefully.';
        $data=SMSHelper::send($mobile_no,$message);
        return $data;
    }

    public function completedReCompleted($mobile_no,$short_url,$query_status,$investor_name,$query_id,$close_date,$feedback_url)
    {
        $message='Dear '.$investor_name.',

Greetings from NuEdge Corporate Private Limited

Your below query is '.$query_status.'.

Query Id- '.$query_id.'.

Query Details- '.str_replace("https://","https://\r",$short_url).'.

Actual Close Date- '.$close_date.'.

Now you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.

Customer satisfaction is always a number one priority for us. Your feedback will help us make our services better. Please share your feedback click here- '.str_replace("https://","https://\r",$feedback_url).'


Regards,
NuEdge Corporate Private Limited.
AMFI- Registered Mutual Fund Distributor

Mutual Fund investments are subject to market risks, read all scheme related documents carefully.';
        $data=SMSHelper::send($mobile_no,$message);
        return $data;
    }
}