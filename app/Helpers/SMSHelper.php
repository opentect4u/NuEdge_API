<?php
namespace App\Helpers;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Http;

class SMSHelper {
    public static function createShortUrl($url)
    {
        $apiKey = urlencode(env('SMS_API_KEY'));
        $data = array('apikey' => $apiKey, 'url' => $url);
        $ch = curl_init(env('SMS_CREATE_SHORT_URL'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $response=json_decode($response);
        return $response;
    }

    
    public static function send($message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SMS_HUB_SEND_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$message,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        // echo $response;
        return $response;
    }

    public static function registerReOpen($mobile_no,$short_url,$query_status,$investor_name,$query_id)
    {
        $message='<SmsQueue>
        <Account>
        <User>'.env('SMS_HUB_USER_ID').'</User>
        <Password>'.env('SMS_HUB_PASS').'</Password>
        <SenderId>'.env('SMS_HUB_SENDER_ID').'</SenderId>
        <Channel>Trans</Channel>
        <DCS>0</DCS>
        <FlashSms>0</FlashSms>
        <Route>2</Route>
        </Account>
        <Messages>
        <Message>
        <Number>'.$mobile_no.'</Number>
        <Text>Dear '.$investor_name.',

Greetings from NuEdge Corporate Private Limited

Your below query is '.$query_status.'.

Query Id- '.$query_id.'.

Query Details- #Link1#.


Now you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.


Regards,
NuEdge Corporate Private Limited.
AMFI- Registered Mutual Fund Distributor

Mutual Fund investments are subject to market risks, read all scheme related documents carefully.</Text>
        </Message>
        </Messages>
        <Links>
        <Links>
        <Link>'.str_replace("https://","",$short_url).'</Link>
        </Links>
        </Links>
        </SmsQueue>';
        $response=SMSHelper::send($message);
        $response=json_decode($response, true);
        return $response;
    }

    public static function inReinProcess($mobile_no,$short_url,$query_status,$investor_name,$query_id,$expected_close_date)
    {
        $message='<SmsQueue>
        <Account>
        <User>'.env('SMS_HUB_USER_ID').'</User>
        <Password>'.env('SMS_HUB_PASS').'</Password>
        <SenderId>'.env('SMS_HUB_SENDER_ID').'</SenderId>
        <Channel>Trans</Channel>
        <DCS>0</DCS>
        <FlashSms>0</FlashSms>
        <Route>2</Route>
        </Account>
        <Messages>
        <Message>
        <Number>'.$mobile_no.'</Number>
        <Text>Dear '.$investor_name.',

Greetings from NuEdge Corporate Private Limited

Your below query is '.$query_status.'.

Query Id- '.$query_id.'.

Query Details- #Link1#.

Expected Close Date- '.$expected_close_date.'.

Now you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.

We sincerely regret the inconvenience caused by the delay.


Regards,
NuEdge Corporate Private Limited.
AMFI- Registered Mutual Fund Distributor

Mutual Fund investments are subject to market risks, read all scheme related documents carefully.</Text>
        </Message>
        </Messages>
        <Links>
        <Links>
        <Link>'.str_replace("https://","",$short_url).'</Link>
        </Links>
        </Links>
        </SmsQueue>';
        $response=SMSHelper::send($message);
        $response=json_decode($response, true);
        return $response;
    }

    public static function completedReCompleted($mobile_no,$short_url,$query_status,$investor_name,$query_id,$close_date,$feedback_url)
    {
        $message='<SmsQueue>
<Account>
<User>'.env('SMS_HUB_USER_ID').'</User>
<Password>'.env('SMS_HUB_PASS').'</Password>
<SenderId>'.env('SMS_HUB_SENDER_ID').'</SenderId>
<Channel>Trans</Channel>
<DCS>0</DCS>
<FlashSms>0</FlashSms>
<Route>2</Route>
</Account>
<Messages>
<Message>
<Number>'.$mobile_no.'</Number>
<Text>Dear '.$investor_name.',

Greetings from NuEdge Corporate Private Limited

Your below query is '.$query_status.'.

Query Id- '.$query_id.'.

Query Details- #Link1#.

Actual Close Date- '.$close_date.'.

Now you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.

Customer satisfaction is always a number one priority for us. Your feedback will help us make our services better. Please share your feedback click here- #Link2#


Regards,
NuEdge Corporate Private Limited.
AMFI- Registered Mutual Fund Distributor

Mutual Fund investments are subject to market risks, read all scheme related documents carefully.</Text>
</Message>
</Messages>
<Links>
<Links>
<Link>'.str_replace("https://","",$short_url).'</Link>
</Links>
<Links>
<Link>'.str_replace("https://","",$feedback_url).'</Link>
</Links>
</Links>
</SmsQueue>';
        $response=SMSHelper::send($message);
        $response=json_decode($response, true);
        return $response;
    }
}