<?php
namespace App\Helpers;
use App\Http\Controllers\Controller;
use DB;

class TransHelper{

    public static function transSubTypeKFIN($trans_sub_type_code,$trans_flag)
    {
        $sub_type_name="";
        switch ($trans_sub_type_code) {
            case 'NEW':
                $sub_type_name="Fresh";
                break;
            case 'ADD':
                $sub_type_name="Additional";
                break;
            case 'IPO':
                $sub_type_name="NFO";
                break;
            case 'IPOR':
                $sub_type_name="NFO Rejection";
                break;
            case 'IPOD':
                $sub_type_name="NFO Pre-Rejection";
                break;
            case 'NEWR':
                $sub_type_name="Fresh";
                break;
            case 'ADDR':
                $sub_type_name="Additional";
                break;
            case 'NEWD':
                $sub_type_name="Fresh Pre-Rejection";
                break;
            case 'BNS':
                $sub_type_name="Bonus Unit";
                break;
            case 'BNSR':
                $sub_type_name="Bonus Rejection";
                break;
            case 'BNSRR':
                $sub_type_name="Bonus Rejection Reversal";
                break;
            case 'CNI':
                $sub_type_name="Consolidation In";
                break;
            case 'CNO':
                $sub_type_name="Consolidation Out";
                break;
            case 'DMT':
                $sub_type_name="DEMET";
                break;
            case 'DIV':
                if ($trans_flag=='DP') {
                    $sub_type_name="DIVIEDEND Payout";
                }elseif ($trans_flag=='DR') {
                    $sub_type_name="DIVIEDEND Reinvestment";
                }
                break;
            case 'LTINR' || 'LTIAR':
                $sub_type_name="Lateral Switch In Rejection";
                break;
            case 'LTOFR' || 'LTOPR':
                $sub_type_name="Lateral Switch Out Rejection";
                break;
            case 'LTIA' || 'LTIN':
                $sub_type_name="Lateral Switch In";
                break;
            case 'LTIAD' || 'LTIND':
                $sub_type_name="Lateral Switch In Rejection";
                break;
            case 'LTOF' || 'LTOP':
                $sub_type_name="Lateral Switch Out";
                break;
            case 'LTOPD':
                $sub_type_name="Lateral Switch Out Rejection";
                break;
            case 'PLDO':
                $sub_type_name="Lien-In";
                break;
            case 'RED':
                $sub_type_name="Pertial Redemtion";
                break;
            case 'FUL':
                $sub_type_name="Full Redemtion";
                break;
            case 'FULD':
                $sub_type_name="Redemtion Rejection";
                break;
            case 'REDR':
                $sub_type_name="Pertial Redemtion Rejection";
                break;
            case 'FULR':
                $sub_type_name="Full Redemtion Rejection";
                break;
            case 'REDRR':
                $sub_type_name="Redemtion Rejection Reversal";
                break;
            case 'RFD':
                $sub_type_name="Refund";
                break;
            case 'STPA' || 'STPI' || 'STPN':
                $sub_type_name="STP In";
                break;
            case 'STPAR' || 'STPNR':
                $sub_type_name="STP In Rejection";
                break;
            case 'STPO':
                $sub_type_name="STP Out";
                break;
            case 'STPOR':
                $sub_type_name="STP Out Rejection";
                break;
            case 'BNS':
                $sub_type_name="Segregated";
                break;
            case 'BNSR':
                $sub_type_name="Segregated Unit Rejection";
                break;
            case 'SINR':
                $sub_type_name="SIP Rejection";
                break;
            case 'SWIAR' || 'SWINR':
                $sub_type_name="Switch In Rejection";
                break;
            case 'SWOFR':
                $sub_type_name="Switch Out Rejection";
                break;
            case 'SWIA' || 'SWIN':
                $sub_type_name="Switch In";
                break;
            case 'SWOF' || 'SWOP':
                $sub_type_name="Switch Out";
                break;
            case 'SWDR':
                $sub_type_name="SWP Rejection";
                break;
            case 'SWDPR':
                $sub_type_name="SWP Rejection Reversal";
                break;
            case 'SIM':
                $sub_type_name="SIP Purchase";
                break;
            case 'SIND':
                $sub_type_name="SIP Installment Rejection";
                break;
            case 'SWD':
                $sub_type_name="SWP";
                break;
            case 'SWDD':
                $sub_type_name="SWP Installment Rejection";
                break;
            case 'TMO' || 'TRMO':
                $sub_type_name="Transmission Out";
                break;
            case 'TMI' || 'TRMI':
                $sub_type_name="Transmission In";
                break;
            default:
                $sub_type_name="N/A";
                break;
        }
        return $sub_type_name;
    }

    public static function transTypeToCodeCAMS($trxn_type)
    {
        $trxn_type_code="";
        if (str_starts_with($trxn_type, 'P')) {
            $trxn_type_code="P";
        }else if (str_starts_with($trxn_type, 'R')) {
            $trxn_type_code="R";
        }else if (str_starts_with($trxn_type, 'SI')) {
            $trxn_type_code="SI";
        }else if (str_starts_with($trxn_type, 'SO')) {
            $trxn_type_code="SO";
        }else if (str_starts_with($trxn_type, 'TI')) {
            $trxn_type_code="TI";
        }else if (str_starts_with($trxn_type, 'TO')) {
            $trxn_type_code="TO";
        }else if (str_starts_with($trxn_type, 'DR')) {
            $trxn_type_code="DR";
        }else if (str_starts_with($trxn_type, 'J')) {
            $trxn_type_code="J";
        }else {
            $trxn_type_code="ALL Others";
        }
        return $trxn_type_code;
    }

    public static function trxnNatureCodeCAMS($trxn_nature)
    {
        $trxn_nature_code="";
        if (str_starts_with($trxn_nature, 'Systematic')) {
            $trxn_nature_code="Systematic";
        // }else if (str_starts_with($trxn_nature, 'NFO Purchase')) {
        //     $trxn_nature_code="NFO Purchase";
        }else {
            $trxn_nature_code=$trxn_nature;
        }
        return $trxn_nature_code;
    }


    // public function my_xirr()
    // {
    //     $values = [
    //         -4999.75,
    //         -4999.75,
    //         10246.82,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         65905.17,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         14672.61,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         -4999.75,
    //         20934.67,
    //         -4999.75,
    //         4846.07,
    //         -4999.75,
    //         4884,
    //         -4999.75,
    //         -4999.75,
    //         52307
    //     ];
            
    //     $dates = [
    //         "2021-06-01",
    //         "2021-07-12",
    //         "2021-08-10",
    //         "2021-08-10",
    //         "2021-09-13",
    //         "2021-10-11",
    //         "2021-11-10",
    //         "2021-12-13",
    //         "2022-01-11",
    //         "2022-02-10",
    //         "2022-03-10",
    //         "2022-04-11",
    //         "2022-05-10",
    //         "2022-06-13",
    //         "2022-07-11",
    //         "2022-08-10",
    //         "2022-09-07",
    //         "2022-09-13",
    //         "2022-10-10",
    //         "2022-11-10",
    //         "2022-11-14",
    //         "2022-12-13",
    //         "2023-01-10",
    //         "2023-02-10",
    //         "2023-03-10",
    //         "2023-04-10",
    //         "2023-05-10",
    //         "2023-06-12",
    //         "2023-07-11",
    //         "2023-08-10",
    //         "2023-09-11",
    //         "2023-10-09",
    //         "2023-10-10",
    //         "2023-11-02",
    //         "2023-11-10",
    //         "2023-11-13",
    //         "2023-12-11",
    //         "2024-01-10",
    //         "2024-02-06"
    //         ];
            
    //     $guess = 0;
    //     $xirr = $this->XIRR($values,$dates,$guess);
    //     return ($xirr *100);
    // }

    // public function XIRR($values, $dates, $guess) {
            
    //     $irrResult = function($values, $dates, $rate) {
    //         $r=$rate + 1;
    //         $result = $values[0];
    //         for ($i = 1; $i < count($values); $i++) {
    //             $result += $values[i] / pow($r, moment($dates[$i]).diff(moment($dates[0]), 'days') / 365);
    //         }
    //         return result;
    //     }
      
    //     var irrResultDeriv = function($values, $dates, rate) {
    //       var r = rate + 1;
    //       var result = 0;
    //       for (var i = 1; i < $values.length; i++) {
    //         var frac = moment($dates[i]).diff(moment($dates[0]), 'days') / 365;
    //         result -= frac * $values[i] / Math.pow(r, frac + 1);
    //       }
    //       return result;
    //     }
       
      
    //     var positive = false;
    //     var negative = false;
    //     for (var i = 0; i < $values.length; i++) {
    //       if ($values[i] > 0) positive = true;
    //       if ($values[i] < 0) negative = true;
    //     }
       
    //     if (!positive || !negative)
    //     {
    //         console.log('asdasdsad')
    //         return '#NUM!';
           
    //     }
      
    //     var $guess = (typeof $guess === 'undefined') ? 0.1 : $guess;
    //     var resultRate = $guess;
       
    //     console.log(resultRate)
       
    //     var epsMax = 1e-10;
       
    //     var iterMax = 20;
      
    //     var newRate, epsRate, resultValue;
    //     var iteration = 0;
    //     var contLoop = true;
    //     do {
    //       resultValue = irrResult($values, $dates, resultRate);
    //       newRate = resultRate - resultValue / irrResultDeriv($values, $dates, resultRate);
    //       epsRate = Math.abs(newRate - resultRate);
    //       resultRate = newRate;
    //       contLoop = (epsRate > epsMax) && (Math.abs(resultValue) > epsMax);
    //     } while(contLoop && (++iteration < iterMax));
    //     if(contLoop)return '#NUM!';
      
    //     // Return internal rate of return
    //     console.log(resultRate)
    //     return resultRate  ;
    // }
}