<?php
namespace App\Helpers;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Http;

class WAHelper {
    
    public static function send($bodyValues)
    {
        // return $bodyValues;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('GALLABOX_SEND_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $bodyValues,
            CURLOPT_HTTPHEADER => array(
              'apiSecret: '.env('GALLABOX_API_SECRET'),
              'apiKey: '.env('GALLABOX_API_KEY'),
              'Content-Type: application/json'
            ),
            // CURLOPT_SSL_VERIFYPEER => false,
            // CURLOPT_SSL_VERIFYHOST => false,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        // echo $response;
        return $response;
    }

    public static function registerReOpen($mobile_no,$short_url,$query_status,$investor_name,$query_id)
    {
        $bodyValues='{
            "channelId": "'.env('GALLABOX_CHANNEL_ID').'",
            "channelType": "whatsapp",
            "recipient":  {
                "name": "'.$investor_name.'",
                "phone": "91'.$mobile_no.'"
            },
            "whatsapp": {
                "type": "template",
                "template": {
                    "templateName": "'.env("GALLABOX_registerReOpen").'",
                    "bodyValues": {
                        "name": "'.$investor_name.'",
                        "status": "'.$query_status.'",
                        "query_id": "'.$query_id.'",
                        "query_details": "'.$short_url.'"
                    }
                }
            }
        }';
        // return $bodyValues;
        $response=self::send($bodyValues);
        // $response=WAHelper::send($channelId,$recipient,$templateName,$bodyValues);
        $response=json_decode($response);
        return $response;
    }

    public static function inReinProcess($mobile_no,$short_url,$query_status,$investor_name,$query_id,$expected_close_date)
    {
        $bodyValues='{
                "channelId": "'.env('GALLABOX_CHANNEL_ID').'",
                "channelType": "whatsapp",
                "recipient":  {
                    "name": "'.$investor_name.'",
                    "phone": "91'.$mobile_no.'"
                },
                "whatsapp": {
                    "type": "template",
                    "template": {
                        "templateName": "'.env("GALLABOX_inReinProcess").'",
                        "bodyValues": {
                            "name": "'.$investor_name.'",
                            "status": "'.$query_status.'",
                            "query_id": "'.$query_id.'",
                            "query_details": "'.$short_url.'",
                            "date": "'.$expected_close_date.'"
                        }
                    }
                }
        }';
        // return $bodyValues;
        $response=self::send($bodyValues);
        $response=json_decode($response);
        return $response;
    }

    public static function completedReCompleted($mobile_no,$short_url,$query_status,$investor_name,$query_id,$close_date,$feedback_url)
    {
        $bodyValues='{
            "channelId": "'.env('GALLABOX_CHANNEL_ID').'",
            "channelType": "whatsapp",
            "recipient":  {
                "name": "'.$investor_name.'",
                "phone": "91'.$mobile_no.'"
            },
            "whatsapp": {
                "type": "template",
                "template": {
                    "templateName": "'.env("GALLABOX_completedReCompleted").'",
                    "bodyValues": {
                        "name": "'.$investor_name.'",
                        "status": "'.$query_status.'",
                        "query_id": "'.$query_id.'",
                        "query_details": "'.$short_url.'",
                        "date": "'.$close_date.'",
                        "feedback": "'.$feedback_url.'"
                    }
                }
            }
        }';
        // return $bodyValues;
        $response=self::send($bodyValues);
        $response=json_decode($response);
        return $response;
    }
}