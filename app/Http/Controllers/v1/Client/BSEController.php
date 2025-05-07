<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BSEController extends Controller
{
    public function FunctionName() : Returntype {
        // {
        //     "UserId" : "",
        //     "MemberCode" : "",
        //     "Password" : "",
        //     "RegnType" : "NEW",
        //     "Param" : "SD121199|Shreya||Das|01|F|12/11/1999|01|SI|||||||||||||N||||HHAPD6054F||||||||P||||||||SB|11415|HDFC0000001|Y|||||||||||||||||||||Shreya Das|01|22/28|Rajamanindra|Road|Kolkata|WB|700037|INDIA|9163419789|||shreyadas871@gmail.com|P||||||||||||9163419789|||||||||||||||||||K||||||||||||N||P|||||",
        //     "Filler1" : "",
        //     "Filler2" : ""
        //     }
        

    }

    public function UCCRegistration()
    {
        // return env('BSE_PASSWORD');
        $Param="SD1211992|Shreya||Das|01|F|12/11/1999|01|SI|||||||||||||N||||HHAPD6054F||||||||P||||||||SB|11415123||HDFC0000002|Y|||||||||||||||||||||Shreya Das|01|22/28|Rajamanindra|Road|Kolkata|WB|700037|INDIA|||||shreyadas871@gmail.com|E||||||||||||9163419789|Chitta Maity|03|100|N|||||||||||||||K||||||||||||N||Z|||SE|SE|Y|O||||||||||||||||||";
        // $Param="ucc0001|Chitta||Maity|01|M|01/01/1970|01|SI|||||||||||||N||||AFEPK2130F||||||||P||||||||SB|11415||HDFC0000001|Y|||||||||||||||||||||FirstNameLastName|01|ADD1|ADD2|ADD3|MUMBAI|MA|400001|INDIA|22721233||||test@test.com|P||||||||||||9999999999|NomineeName1|01|100|N|||||||||||||||K||||||||||||N||P|||SE|SE|Y|O||||||||||||||";
        // return explode('|',$Param);
        // $RegnType="NEW";
        $RegnType="MOD";
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://bsestarmfdemo.bseindia.com/BSEMFWEBAPI/UCCAPI/UCCRegistration',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "UserId": "'.env('BSE_USER_ID').'",
            "MemberCode": "'.env('BSE_MEMBER_CODE').'",
            "Password": "'.env('BSE_PASSWORD').'",
            "RegnType": "'.$RegnType.'",
            "Param": "'.$Param.'",
            "Filler1": "",
            "Filler2": ""
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        return json_decode($response);
    }

    public function MFOrderGetPassword()
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://bsestarmfdemo.bseindia.com/MFOrderEntry/MFOrder.svc/Secure',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"
        xmlns:bses="http://bsestarmf.in/">
        <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
        <wsa:Action>http://bsestarmf.in/MFOrderEntry/getPassword</wsa:Action>
        <wsa:To>
        https://bsestarmfdemo.bseindia.com/MFOrderEntry/MFOrder.svc/Secure
        </wsa:To>
        </soap:Header>
        <soap:Body>
        <bses:getPassword>
        <bses:UserId>1562501</bses:UserId>
        <bses:Password>Bse@2025</bses:Password>
        <bses:PassKey>123456789</bses:PassKey>
        </bses:getPassword>
        </soap:Body>
        </soap:Envelope>',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/soap+xml'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        return json_decode($response);
    }

    public function index(Request $request)
    {
        // return 'hello';
        return $this->UCCRegistration();
        // $getpass=$this->MFOrderGetPassword();
        // return  $getpass;
        // z8L/ina6xHCmGGDlxE3UbYRfJTX1tB8GRitc6n/wLinV/PNWC0nMplLfnlmphQ3g

    }
}