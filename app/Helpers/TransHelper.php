<?php
namespace App\Helpers;
use App\Http\Controllers\Controller;
use DB;
use App\Models\{
    BrokerChangeTransReport,
    Client,
    MutualFundTransaction
};

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

      /* CAGR calculation $nper=271/365;
                $cagr = pow((6193.43/4561),(1/$nper)) - 1; */
    
    public static function getClientDetails($client_rawQuery,$view_type)
    {
        if ($view_type=='C') {
            $client_details=Client::leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                ->leftJoin('md_city','md_city.id','=','md_client.city')
                ->leftJoin('md_states','md_states.id','=','md_client.state')
                ->leftJoin('md_district','md_district.id','=','md_client.dist')
                ->select('md_client.*','md_pincode.pincode as pincode','md_city.name as city_name','md_states.name as state_name','md_district.name as dist_name')
                ->whereRaw($client_rawQuery)->first();
        }else {
            $client_details=Client::leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                ->leftJoin('md_city','md_city.id','=','md_client.city')
                ->leftJoin('md_states','md_states.id','=','md_client.state')
                ->leftJoin('md_district','md_district.id','=','md_client.dist')
                ->select('md_client.*','md_pincode.pincode as pincode','md_city.name as city_name','md_states.name as state_name','md_district.name as dist_name')
                ->whereRaw($client_rawQuery)->get();
        }
        return $client_details;
    }

    public static function calculate($foliotrans){
        $purchase_data=[];
        $redemption_data=[];
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];
        $all_amt_arr=[];
        $all_date_arr=[];
        $return_data=[];
        /*******************************************Start CAMS Broker Change Data**********************************************************/
        $get_rejection_data=[];
        // if ($foliotrans[0]['rnt_id']==1 && $foliotrans[0]['transaction_type']=='Transfer In' && $foliotrans[0]['transaction_subtype']=='Transfer In') {
            $final_foliotrans=[];
            foreach ($foliotrans as $key => $foliotrans_value) {
                if ($foliotrans_value->transaction_type=="Transfer In" && $foliotrans_value->transaction_subtype=="Transfer In") {
                    // return $foliotrans_value;
                    $broker_data=TransHelper::getBrokerData($foliotrans_value);
                    // return $broker_data;
                    if (count($broker_data)> 0) {
                        foreach ($broker_data as $key => $broker_data_value) {
                            if ($broker_data_value->amount < 0) {
                                $broker_data_value->transaction_type=$broker_data_value->transaction_type." Rejection";
                                $broker_data_value->transaction_subtype=$broker_data_value->transaction_subtype." Rejection";
                            }
                            if( strpos($broker_data_value->transaction_subtype, 'Rejection' ) == false) {
                                array_push($final_foliotrans,$broker_data_value);
                            }else {
                                array_push($get_rejection_data,$broker_data_value);
                            }
                            // array_push($final_foliotrans,$broker_data_value);
                        }
                    }else {
                        array_push($final_foliotrans,$foliotrans_value);
                    }
                }else {
                    if ($foliotrans_value->rnt_id==1 && $foliotrans_value->amount < 0) {
                        $foliotrans_value->transaction_type=$foliotrans_value->transaction_type." Rejection";
                        $foliotrans_value->transaction_subtype=$foliotrans_value->transaction_subtype." Rejection";
                    }
                    if( strpos($foliotrans_value->transaction_subtype, 'Rejection' ) == false) {
                        array_push($final_foliotrans,$foliotrans_value);
                    }else {
                        array_push($get_rejection_data,$foliotrans_value);
                    }
                    // array_push($final_foliotrans,$foliotrans_value);
                }
            }
            $foliotrans=$final_foliotrans;
        // }
        // return $foliotrans;
        // return $get_rejection_data;
        /*******************************************End CAMS Broker Change Data**********************************************************/
        // **************************Start Rejection Amount Delete*************************************
        foreach ($get_rejection_data as $key_0001 => $value_0001) {
            $amount=str_replace("-","",$value_0001->amount) ;
            $trans_date=$value_0001->trans_date;
            $get_final_success_data=[];
            foreach ($foliotrans as $key_002 => $value_002) {
                if ($value_002->trans_date==$trans_date && $value_002->amount==$amount) {
                    $amount=0;
                }else {
                    array_push($get_final_success_data,$value_002);
                }
            }
            $foliotrans=$get_final_success_data;
        }
        // **************************End Rejection Amount Delete*************************************
        // return $foliotrans;
        if (count($foliotrans)==0) {
            $foliotrans=$get_rejection_data;
        }
        $return_data['inv_since']=date('Y-m-d',strtotime($foliotrans[0]['trans_date']));
        $return_data['pur_nav']=$foliotrans[0]['pur_price'];
        $return_data['nifty50']=$foliotrans[0]['nifty50'];
        $return_data['sensex']=$foliotrans[0]['sensex'];
        /*************************************start transaction_type_subtype modify**********************************************************/
        if ($foliotrans[0]['transaction_type']=="Purchase" && $foliotrans[0]['transaction_subtype']=="Fresh Purchase") {
            if ((isset($foliotrans[1]['transaction_type']) && $foliotrans[1]['transaction_type']=="SIP Purchase") && (isset($foliotrans[1]['transaction_subtype']) && $foliotrans[1]['transaction_subtype']=="SIP Purchase Installment")) {
                $return_data['transaction_type']=$foliotrans[1]['transaction_type'];
                $return_data['transaction_subtype']=$foliotrans[1]['transaction_subtype'];
            }else {
                $return_data['transaction_type']=$foliotrans[0]['transaction_type'];
                $return_data['transaction_subtype']=$foliotrans[0]['transaction_subtype'];
            }
        }else {
            $return_data['transaction_type']=$foliotrans[0]['transaction_type'];
            $return_data['transaction_subtype']=$foliotrans[0]['transaction_subtype'];
        }
        /*************************************end transaction_type_subtype modify**********************************************************/

        foreach ($foliotrans as $key => $value) {
            if(strpos($value->transaction_subtype, 'Purchase' )!== false || strpos($value->transaction_subtype, 'Switch In' )!== false 
                || strpos($value->transaction_subtype, 'Dividend Reinvestment')!== false || strpos($value->transaction_subtype, 'STP In')!== false) {
                if ($key > 0) {
                    $value->cumml_units=number_format((float)($value->tot_units + $foliotrans[($key-1)]->cumml_units) , 4, '.', '') ;
                }else {
                    $value->cumml_units=$value->tot_units;
                }
                array_push($purchase_data,$value);
                // array_push($purchase_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,-$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }elseif (strpos($value->transaction_subtype, 'Redemption' )!== false || strpos($value->transaction_subtype, 'Switch Out' )!== false 
                || strpos($value->transaction_subtype, 'Transfer Out')!== false || strpos($value->transaction_subtype, 'SWP')!== false
                || strpos($value->transaction_subtype, 'STP Out')!== false) {
                $value->cumml_units=0;
                array_push($redemption_data,$value);
                // array_push($redemption_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }
        }
        // return $purchase_data;

        // *********************for pledging condition*****************
        $purchase_data_recheck=[];
        foreach ($purchase_data as $key_001 => $value_001) {
            if ($key_001 > 0) {
                $value_001->cumml_units=number_format((float)($value_001->tot_units + $purchase_data[($key_001-1)]->cumml_units) , 4, '.', '');
            }else {
                $value_001->cumml_units=number_format((float)$value_001->tot_units, 4, '.', '');
            }
            array_push($purchase_data_recheck,$value_001);
        }
        // return $purchase_data_recheck;
        $purchase_data=$purchase_data_recheck;
        // *********************for pledging condition*****************

        $idcw_reinv=0;
        $idcw_paid=0;
        $inv_cost=0;
        if (count($redemption_data) > 0) {
            /*******************************************start purchase and redemption case******************************************/
            foreach ($redemption_data as $redemption_key => $redemption_value) {
                $rdm_tot_units=number_format((float)$redemption_value->tot_units, 4, '.', '');
                $deduct_unit_array=[];
                $flag='Y';
                foreach ($purchase_data as $purchase_key => $purchase_value) {
                    if ($purchase_value['cumml_units'] >= 0) {
                        $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
                        $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
                        if ($purchase_value['cumml_units'] >= 0 ) {
                            $calculation_cumml_unit=isset($purchase_data[($purchase_key - 1)]['cumml_units'])?$purchase_data[($purchase_key - 1)]['cumml_units']:0;
                            if ($calculation_cumml_unit < 0) {
                                $set_units=$purchase_value['cumml_units'];
                                $purchase_value['cumml_units']=0;
                                array_push($deduct_unit_array,$purchase_value);
                                $rdm_tot_units=0;
                                $newarr=[];
                                $newarr['id']=$purchase_value['id'];
                                $newarr['trans_date']=$purchase_value['trans_date'];
                                $newarr['pur_price']=$purchase_value['pur_price'];
                                $newarr['sensex']=$purchase_value['sensex'];
                                $newarr['nifty50']=$purchase_value['nifty50'];
                                $newarr['curr_nav']=$purchase_value['curr_nav'];
                                $newarr['days']=$purchase_value['days'];
                                $newarr['trans_mode']=$purchase_value['trans_mode'];
                                $newarr['transaction_type']="Remaining";
                                $newarr['transaction_subtype']="Remaining";
                                $newarr['tot_units']=$set_units;
                                $newarr['cumml_units']=$set_units;
                                $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                                array_push($deduct_unit_array,$newarr);
                                $flag='N';
                            }else {
                                if ($flag=='Y') {
                                    $set_units=$purchase_value['cumml_units'];
                                    $purchase_value['cumml_units']=0;
                                    array_push($deduct_unit_array,$purchase_value);
                                    $rdm_tot_units=0;
                                    $newarr=[];
                                    $newarr['id']=$purchase_value['id'];
                                    $newarr['trans_date']=$purchase_value['trans_date'];
                                    $newarr['pur_price']=$purchase_value['pur_price'];
                                    $newarr['sensex']=$purchase_value['sensex'];
                                    $newarr['nifty50']=$purchase_value['nifty50'];
                                    $newarr['curr_nav']=$purchase_value['curr_nav'];
                                    $newarr['days']=$purchase_value['days'];
                                    $newarr['trans_mode']=$purchase_value['trans_mode'];
                                    $newarr['transaction_type']="Remaining";
                                    $newarr['transaction_subtype']="Remaining";
                                    $newarr['tot_units']=$set_units;
                                    $newarr['cumml_units']=$set_units;
                                    $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                                    array_push($deduct_unit_array,$newarr);
                                    $flag='N';
                                }else{
                                    $purchase_value['cumml_units']=number_format((float)$purchase_value['tot_units'], 4, '.', '') + number_format((float)$deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'], 4, '.', '') ;
                                    $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                    array_push($deduct_unit_array,$purchase_value);
                                }
                            }
                        }else {
                            $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                            array_push($deduct_unit_array,$purchase_value);
                            // return $deduct_unit_array;
                        }
                    }else {
                        $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                        array_push($deduct_unit_array,$purchase_value);
                    }
                }
                // return  $deduct_unit_array;
                $purchase_data=$deduct_unit_array;
            }
            // return $purchase_data;
            /*******************************************end purchase and redemption case******************************************/
            // $final_array=array_merge($deduct_unit_array,$purchase_data);
            // return $final_array;
            // $final_data_arr=[];
            $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                if ($value['cumml_units'] > 0) {
                    if (strpos($value['transaction_subtype'], 'Dividend Reinvestment')!== false) {
                        $idcw_reinv +=number_format((float)$value['tot_amount'], 2, '.', '');
                    }
                    $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
                }
            }
        }else {
            // $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
            }
        }
        // return $all_amt_arr;
        // return $all_date_arr;
        $return_data['inv_cost']=$inv_cost;
        $return_data['tot_units']=(count($purchase_data) > 0)?$purchase_data[(count($purchase_data) - 1)]['cumml_units']:0;
        $return_data['all_amt_arr']=$all_amt_arr;
        $return_data['all_date_arr']=$all_date_arr;
        $return_data['idcw_reinv']=$idcw_reinv;
        $return_data['idcw_paid']=$idcw_paid;
        return $return_data;
    }

    public static function getBrokerData($foliotrans_value)
    {
        $rawInnerQuery='';
        $queryString='tt_broker_change_trans_report.folio_no';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->folio_no,$rawInnerQuery,$queryString);
        $queryString='tt_broker_change_trans_report.product_code';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->product_code,$rawInnerQuery,$queryString);
        // return $rawInnerQuery;

        $broker_data=BrokerChangeTransReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_broker_change_trans_report.product_code')
            ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            ->select('tt_broker_change_trans_report.rnt_id','tt_broker_change_trans_report.folio_no','tt_broker_change_trans_report.product_code',
            'tt_broker_change_trans_report.isin_no','tt_broker_change_trans_report.trans_date','tt_broker_change_trans_report.trxn_type',
            'tt_broker_change_trans_report.trxn_type_flag','tt_broker_change_trans_report.trxn_nature','tt_broker_change_trans_report.amount',
            'tt_broker_change_trans_report.stamp_duty','tt_broker_change_trans_report.tds','tt_broker_change_trans_report.units','tt_broker_change_trans_report.pur_price',
            'tt_broker_change_trans_report.trans_no',
            'md_scheme.scheme_name as scheme_name')
            ->selectRaw('sum(units) as tot_units')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('IF(tt_broker_change_trans_report.tds!="",sum(tds),0.00)as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->selectRaw('(SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as transaction_type')
            ->selectRaw('(SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as transaction_subtype')
            ->selectRaw('(SELECT lmf_pl FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as lmf_pl')
            ->where('tt_broker_change_trans_report.delete_flag','N')
            ->where('tt_broker_change_trans_report.amc_flag','N')
            ->where('tt_broker_change_trans_report.scheme_flag','N')
            ->where('tt_broker_change_trans_report.plan_option_flag','N')
            ->where('tt_broker_change_trans_report.bu_type_flag','N')
            ->where('tt_broker_change_trans_report.divi_mismatch_flag','N')
            ->whereRaw($rawInnerQuery)
            ->groupBy('tt_broker_change_trans_report.trans_no')
            ->groupBy('tt_broker_change_trans_report.trxn_type_flag')
            ->groupBy('tt_broker_change_trans_report.trxn_nature_code')
            ->groupBy('tt_broker_change_trans_report.trans_desc')
            ->groupBy('tt_broker_change_trans_report.kf_trans_type')
            ->orderBy('tt_broker_change_trans_report.trans_date','ASC')
            ->get();
        return $broker_data;
    }

    public static function ConsolidationInQuery($rnt_id,$folio_no,$isin_no,$product_code,$valuation_as_on)
    {
        $rawQuery='';
        $queryString='td_mutual_fund_trans.folio_no';
        $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
        $queryString='td_mutual_fund_trans.product_code';
        $rawQuery.=Helper::WhereRawQuery($product_code,$rawQuery,$queryString);
        if ($rnt_id==2) {
            $queryString='td_mutual_fund_trans.isin_no';
            $rawQuery.=Helper::WhereRawQuery($isin_no,$rawQuery,$queryString);
        } 
        $condition=(strlen($rawQuery) > 0)? " AND ":" ";
        $queryString='td_mutual_fund_trans.trans_date';
        $rawQuery.=$condition.$queryString." <= '".$valuation_as_on."'";
        // return $rawQuery;
        // DB::enableQueryLog();
        $all_data=MutualFundTransaction::select('rnt_id','folio_no','product_code','isin_no','trans_date','trxn_type','trxn_type_flag','trxn_nature','amount','stamp_duty','tds',
            'units','pur_price')
            ->selectRaw('sum(units) as tot_units')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND date=trans_date) as nifty50')
            ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND date=trans_date) as sensex')
            ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_type')
            ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_subtype')
            ->where('td_mutual_fund_trans.delete_flag','N')
            ->where('td_mutual_fund_trans.amc_flag','N')
            ->where('td_mutual_fund_trans.scheme_flag','N')
            ->where('td_mutual_fund_trans.plan_option_flag','N')
            ->where('td_mutual_fund_trans.bu_type_flag','N')
            ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
            ->whereRaw($rawQuery)
            ->groupBy('td_mutual_fund_trans.trans_no')
            ->groupBy('td_mutual_fund_trans.trxn_type_flag')
            ->groupBy('td_mutual_fund_trans.trxn_nature_code')
            ->groupBy('td_mutual_fund_trans.trans_desc')
            ->groupBy('td_mutual_fund_trans.kf_trans_type')
            ->groupBy('td_mutual_fund_trans.trans_flag')
            ->orderBy('td_mutual_fund_trans.trans_date','asc')
            ->get();
        return $all_data;
    }



    public static function aum_calculate($foliotrans){
        $purchase_data=[];
        $redemption_data=[];
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];
        $all_amt_arr=[];
        $all_date_arr=[];
        $return_data=[];
        /*******************************************Start CAMS Broker Change Data**********************************************************/
        $get_rejection_data=[];
        // if ($foliotrans[0]['rnt_id']==1 && $foliotrans[0]['transaction_type']=='Transfer In' && $foliotrans[0]['transaction_subtype']=='Transfer In') {
            $final_foliotrans=[];
            foreach ($foliotrans as $key => $foliotrans_value) {
                if ($foliotrans_value->transaction_type=="Transfer In" && $foliotrans_value->transaction_subtype=="Transfer In") {
                    // return $foliotrans_value;
                    // $broker_data=[];
                    $broker_data=TransHelper::aum_getBrokerData($foliotrans_value);
                    // return $broker_data;
                    if (count($broker_data)> 0) {
                        foreach ($broker_data as $key => $broker_data_value) {
                            if ($broker_data_value->amount < 0) {
                                $broker_data_value->transaction_type=$broker_data_value->transaction_type." Rejection";
                                $broker_data_value->transaction_subtype=$broker_data_value->transaction_subtype." Rejection";
                            }
                            if( strpos($broker_data_value->transaction_subtype, 'Rejection' ) == false) {
                                array_push($final_foliotrans,$broker_data_value);
                            }else {
                                array_push($get_rejection_data,$broker_data_value);
                            }
                            // array_push($final_foliotrans,$broker_data_value);
                        }
                    }else {
                        array_push($final_foliotrans,$foliotrans_value);
                    }
                }else {
                    if ($foliotrans_value->rnt_id==1 && $foliotrans_value->amount < 0) {
                        $foliotrans_value->transaction_type=$foliotrans_value->transaction_type." Rejection";
                        $foliotrans_value->transaction_subtype=$foliotrans_value->transaction_subtype." Rejection";
                    }
                    if( strpos($foliotrans_value->transaction_subtype, 'Rejection' ) == false) {
                        array_push($final_foliotrans,$foliotrans_value);
                    }else {
                        array_push($get_rejection_data,$foliotrans_value);
                    }
                    // array_push($final_foliotrans,$foliotrans_value);
                }
            }
            $foliotrans=$final_foliotrans;
        // }
        // return $foliotrans;
        // return $get_rejection_data;
        /*******************************************End CAMS Broker Change Data**********************************************************/
        // **************************Start Rejection Amount Delete*************************************
        foreach ($get_rejection_data as $key_0001 => $value_0001) {
            $amount=str_replace("-","",$value_0001->amount) ;
            $trans_date=$value_0001->trans_date;
            $get_final_success_data=[];
            foreach ($foliotrans as $key_002 => $value_002) {
                if ($value_002->trans_date==$trans_date && $value_002->amount==$amount) {
                    $amount=0;
                }else {
                    array_push($get_final_success_data,$value_002);
                }
            }
            $foliotrans=$get_final_success_data;
        }
        // **************************End Rejection Amount Delete*************************************
        // return $foliotrans;
        if (count($foliotrans)==0) {
            $foliotrans=$get_rejection_data;
        }
        $return_data['inv_since']=date('Y-m-d',strtotime($foliotrans[0]['trans_date']));
        $return_data['pur_nav']=$foliotrans[0]['pur_price'];
        /*************************************start transaction_type_subtype modify**********************************************************/
        // if ($foliotrans[0]['transaction_type']=="Purchase" && $foliotrans[0]['transaction_subtype']=="Fresh Purchase") {
        //     if ((isset($foliotrans[1]['transaction_type']) && $foliotrans[1]['transaction_type']=="SIP Purchase") && (isset($foliotrans[1]['transaction_subtype']) && $foliotrans[1]['transaction_subtype']=="SIP Purchase Installment")) {
        //         $return_data['transaction_type']=$foliotrans[1]['transaction_type'];
        //         $return_data['transaction_subtype']=$foliotrans[1]['transaction_subtype'];
        //     }else {
        //         $return_data['transaction_type']=$foliotrans[0]['transaction_type'];
        //         $return_data['transaction_subtype']=$foliotrans[0]['transaction_subtype'];
        //     }
        // }else {
        //     $return_data['transaction_type']=$foliotrans[0]['transaction_type'];
        //     $return_data['transaction_subtype']=$foliotrans[0]['transaction_subtype'];
        // }
        /*************************************end transaction_type_subtype modify**********************************************************/

        foreach ($foliotrans as $key => $value) {
            if(strpos($value->transaction_subtype, 'Purchase' )!== false || strpos($value->transaction_subtype, 'Switch In' )!== false 
                || strpos($value->transaction_subtype, 'Dividend Reinvestment')!== false || strpos($value->transaction_subtype, 'STP In')!== false) {
                if ($key > 0) {
                    $value->cumml_units=number_format((float)($value->tot_units + $foliotrans[($key-1)]->cumml_units) , 4, '.', '') ;
                }else {
                    $value->cumml_units=$value->tot_units;
                }
                array_push($purchase_data,$value);
                // array_push($purchase_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,-$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }elseif (strpos($value->transaction_subtype, 'Redemption' )!== false || strpos($value->transaction_subtype, 'Switch Out' )!== false 
                || strpos($value->transaction_subtype, 'Transfer Out')!== false || strpos($value->transaction_subtype, 'SWP')!== false
                || strpos($value->transaction_subtype, 'STP Out')!== false) {
                $value->cumml_units=0;
                array_push($redemption_data,$value);
                // array_push($redemption_amt_arr,$value->tot_amount);
                // array_push($all_amt_arr,$value->tot_amount);
                // array_push($all_date_arr,$value->trans_date);
            }
        }
        // return $purchase_data;

        // *********************for pledging condition*****************
        $purchase_data_recheck=[];
        foreach ($purchase_data as $key_001 => $value_001) {
            if ($key_001 > 0) {
                $value_001->cumml_units=number_format((float)($value_001->tot_units + $purchase_data[($key_001-1)]->cumml_units) , 4, '.', '');
            }else {
                $value_001->cumml_units=number_format((float)$value_001->tot_units, 4, '.', '');
            }
            array_push($purchase_data_recheck,$value_001);
        }
        // return $purchase_data_recheck;
        $purchase_data=$purchase_data_recheck;
        // *********************for pledging condition*****************

        $idcw_reinv=0;
        $idcw_paid=0;
        $inv_cost=0;
        if (count($redemption_data) > 0) {
            /*******************************************start purchase and redemption case******************************************/
            foreach ($redemption_data as $redemption_key => $redemption_value) {
                $rdm_tot_units=number_format((float)$redemption_value->tot_units, 4, '.', '');
                $deduct_unit_array=[];
                $flag='Y';
                foreach ($purchase_data as $purchase_key => $purchase_value) {
                    if ($purchase_value['cumml_units'] >= 0) {
                        $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
                        $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
                        if ($purchase_value['cumml_units'] >= 0 ) {
                            $calculation_cumml_unit=isset($purchase_data[($purchase_key - 1)]['cumml_units'])?$purchase_data[($purchase_key - 1)]['cumml_units']:0;
                            if ($calculation_cumml_unit < 0) {
                                $set_units=$purchase_value['cumml_units'];
                                $purchase_value['cumml_units']=0;
                                array_push($deduct_unit_array,$purchase_value);
                                $rdm_tot_units=0;
                                $newarr=[];
                                $newarr['id']=$purchase_value['id'];
                                $newarr['trans_date']=$purchase_value['trans_date'];
                                $newarr['pur_price']=$purchase_value['pur_price'];
                                $newarr['sensex']=$purchase_value['sensex'];
                                $newarr['nifty50']=$purchase_value['nifty50'];
                                $newarr['curr_nav']=$purchase_value['curr_nav'];
                                $newarr['days']=$purchase_value['days'];
                                $newarr['trans_mode']=$purchase_value['trans_mode'];
                                $newarr['transaction_type']="Remaining";
                                $newarr['transaction_subtype']="Remaining";
                                $newarr['tot_units']=$set_units;
                                $newarr['cumml_units']=$set_units;
                                $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                                array_push($deduct_unit_array,$newarr);
                                $flag='N';
                            }else {
                                if ($flag=='Y') {
                                    $set_units=$purchase_value['cumml_units'];
                                    $purchase_value['cumml_units']=0;
                                    array_push($deduct_unit_array,$purchase_value);
                                    $rdm_tot_units=0;
                                    $newarr=[];
                                    $newarr['id']=$purchase_value['id'];
                                    $newarr['trans_date']=$purchase_value['trans_date'];
                                    $newarr['pur_price']=$purchase_value['pur_price'];
                                    $newarr['sensex']=$purchase_value['sensex'];
                                    $newarr['nifty50']=$purchase_value['nifty50'];
                                    $newarr['curr_nav']=$purchase_value['curr_nav'];
                                    $newarr['days']=$purchase_value['days'];
                                    $newarr['trans_mode']=$purchase_value['trans_mode'];
                                    $newarr['transaction_type']="Remaining";
                                    $newarr['transaction_subtype']="Remaining";
                                    $newarr['tot_units']=$set_units;
                                    $newarr['cumml_units']=$set_units;
                                    $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                                    array_push($deduct_unit_array,$newarr);
                                    $flag='N';
                                }else{
                                    $purchase_value['cumml_units']=number_format((float)$purchase_value['tot_units'], 4, '.', '') + number_format((float)$deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'], 4, '.', '') ;
                                    $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                    array_push($deduct_unit_array,$purchase_value);
                                }
                            }
                        }else {
                            $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                            array_push($deduct_unit_array,$purchase_value);
                            // return $deduct_unit_array;
                        }
                    }else {
                        $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                        array_push($deduct_unit_array,$purchase_value);
                    }
                }
                // return  $deduct_unit_array;
                $purchase_data=$deduct_unit_array;
            }
            // return $purchase_data;
            /*******************************************end purchase and redemption case******************************************/
            // $final_array=array_merge($deduct_unit_array,$purchase_data);
            // return $final_array;
            // $final_data_arr=[];
            $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                if ($value['cumml_units'] > 0) {
                    if (strpos($value['transaction_subtype'], 'Dividend Reinvestment')!== false) {
                        $idcw_reinv +=number_format((float)$value['tot_amount'], 2, '.', '');
                    }
                    $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
                }
            }
        }else {
            // $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
            }
        }
        // return $all_amt_arr;
        // return $all_date_arr;
        $return_data['inv_cost']=$inv_cost;
        $return_data['tot_units']=(count($purchase_data) > 0)?$purchase_data[(count($purchase_data) - 1)]['cumml_units']:0;
        // $return_data['all_amt_arr']=$all_amt_arr;
        // $return_data['all_date_arr']=$all_date_arr;
        $return_data['idcw_reinv']=$idcw_reinv;
        $return_data['idcw_paid']=$idcw_paid;
        return $return_data;
    }

    public static function aum_getBrokerData($foliotrans_value)
    {
        $rawInnerQuery='';
        $queryString='tt_broker_change_trans_report.folio_no';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->folio_no,$rawInnerQuery,$queryString);
        $queryString='tt_broker_change_trans_report.product_code';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->product_code,$rawInnerQuery,$queryString);
        // return $rawInnerQuery;

        $broker_data=BrokerChangeTransReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_broker_change_trans_report.product_code')
            ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            ->select('tt_broker_change_trans_report.rnt_id','tt_broker_change_trans_report.folio_no','tt_broker_change_trans_report.product_code',
            'tt_broker_change_trans_report.isin_no','tt_broker_change_trans_report.trans_date','tt_broker_change_trans_report.amount',
            'tt_broker_change_trans_report.stamp_duty','tt_broker_change_trans_report.tds','tt_broker_change_trans_report.units','tt_broker_change_trans_report.pur_price',
            'tt_broker_change_trans_report.trans_no',
            'md_scheme.scheme_name as scheme_name')
            ->selectRaw('sum(units) as tot_units')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('IF(tt_broker_change_trans_report.tds!="",sum(tds),0.00)as tot_tds')
            ->selectRaw('(SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as transaction_type')
            ->selectRaw('(SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as transaction_subtype')
            ->where('tt_broker_change_trans_report.delete_flag','N')
            ->where('tt_broker_change_trans_report.amc_flag','N')
            ->where('tt_broker_change_trans_report.scheme_flag','N')
            ->where('tt_broker_change_trans_report.plan_option_flag','N')
            ->where('tt_broker_change_trans_report.bu_type_flag','N')
            ->where('tt_broker_change_trans_report.divi_mismatch_flag','N')
            ->whereRaw($rawInnerQuery)
            ->groupBy('tt_broker_change_trans_report.trans_no')
            ->groupBy('tt_broker_change_trans_report.trxn_type_flag')
            ->groupBy('tt_broker_change_trans_report.trxn_nature_code')
            ->groupBy('tt_broker_change_trans_report.trans_desc')
            ->groupBy('tt_broker_change_trans_report.kf_trans_type')
            ->orderBy('tt_broker_change_trans_report.trans_date','ASC')
            ->get();
        return $broker_data;
    }
}