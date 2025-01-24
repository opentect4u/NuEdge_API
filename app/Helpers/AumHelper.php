<?php
namespace App\Helpers;
use App\Http\Controllers\Controller;
use DB;
use App\Models\{
    BrokerChangeTransReport,
    Client,
    MutualFundTransaction
};

class AumHelper{

    public static function calculate($foliotrans,$curr_nav,$valuation_as_on){
        $purchase_data=[];
        $redemption_data=[];
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];
        $all_amt_arr=[];
        $all_date_arr=[];
        $return_data=[];
        /*******************************************Start CAMS Broker Change Data**********************************************************/
        $get_rejection_data=[];
        $final_foliotrans=[];
        foreach ($foliotrans as $key => $foliotrans_value) {
            if( strpos($foliotrans_value['transaction_subtype'], 'Rejection' ) == false) {
                array_push($final_foliotrans,$foliotrans_value);
            }else {
                array_push($get_rejection_data,$foliotrans_value);
            }
        }
        $foliotrans=$final_foliotrans;
        // return $foliotrans;
        // return $get_rejection_data;
        /*******************************************End CAMS Broker Change Data**********************************************************/
        // **************************Start Rejection Amount Delete*************************************
        foreach ($get_rejection_data as $key_0001 => $value_0001) {
            $amount=str_replace("-","",$value_0001['amount']) ;
            $trans_date=$value_0001['trans_date'];
            $get_final_success_data=[];
            foreach ($foliotrans as $key_002 => $value_002) {
                if ($value_002['trans_date']==$trans_date && $value_002['amount']==$amount) {
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
        // return $foliotrans;
        // $return_data['inv_since']=date('Y-m-d',strtotime($foliotrans[0]['trans_date']));
        
        foreach ($foliotrans as $key => $value) {
            if(strpos($value['transaction_subtype'], 'Purchase' )!== false || strpos($value['transaction_subtype'], 'Switch In' )!== false 
                || strpos($value['transaction_subtype'], 'Dividend Reinvestment')!== false || strpos($value['transaction_subtype'], 'STP In')!== false
                || strpos($value['transaction_subtype'], 'Transfer In')!== false) {
                if ($key > 0) {
                    $value['cumml_units']=number_format((float)($value['tot_units'] + $foliotrans[($key-1)]['tot_units']) , 4, '.', '') ;
                } else {
                    $value['cumml_units']=number_format((float)$value['tot_units'], 4, '.', '');
                }
                array_push($purchase_data,$value);
                // array_push($all_amt_arr,-$value->tot_amount);
                // array_push($all_date_arr,$value->trans_date);
            }elseif (strpos($value['transaction_subtype'], 'Redemption' )!== false || strpos($value['transaction_subtype'], 'Switch Out' )!== false 
                || strpos($value['transaction_subtype'], 'Transfer Out')!== false || strpos($value['transaction_subtype'], 'SWP')!== false
                || strpos($value['transaction_subtype'], 'STP Out')!== false) {
                $value['cumml_units']=0;
                array_push($redemption_data,$value);
                // array_push($all_amt_arr,$value->tot_amount);
                // array_push($all_date_arr,$value->trans_date);
            }
        }
        // return $purchase_data;

        // *********************for pledging condition*****************
        $purchase_data_recheck=[];
        foreach ($purchase_data as $key_001 => $value_001) {
            if ($key_001 > 0) {
                $value_001['cumml_units']=number_format((float)($value_001['tot_units'] + $purchase_data[($key_001-1)]['cumml_units']) , 4, '.', '');
            }else {
                $value_001['cumml_units']=number_format((float)$value_001['tot_units'], 4, '.', '');
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
                $rdm_tot_units=number_format((float)$redemption_value['tot_units'], 4, '.', '');
                $deduct_unit_array=[];
                $flag='Y';
                foreach ($purchase_data as $purchase_key => $purchase_value) {
                    if ($purchase_value['cumml_units'] >= 0) {
                        $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
                        // $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
                        $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
                        if ($purchase_value['cumml_units'] >= 0 ) {
                            $calculation_cumml_unit=isset($purchase_data[($purchase_key - 1)]['cumml_units'])?$purchase_data[($purchase_key - 1)]['cumml_units']:0;
                            if ($calculation_cumml_unit < 0) {
                                $set_units=$purchase_value['cumml_units'];
                                $purchase_value['cumml_units']=0;
                                array_push($deduct_unit_array,$purchase_value);
                                $rdm_tot_units=0;
                                $newarr=[];
                                $newarr['trans_date']=$purchase_value['trans_date'];
                                $newarr['pur_price']=$purchase_value['pur_price'];
                                $newarr['curr_nav']=$curr_nav;
                                $newarr['transaction_type']="Remaining";
                                $newarr['transaction_subtype']="Remaining";
                                // $newarr['stamp_duty']=$purchase_value['stamp_duty'];
                                // $newarr['tot_stamp_duty']=$purchase_value['tot_stamp_duty'];
                                // $newarr['tot_tds']=$purchase_value['tot_tds'];
                                $newarr['prev_transaction_type']=$purchase_value['transaction_type'];
                                $newarr['prev_transaction_subtype']=$purchase_value['transaction_type'];
                                $newarr['tot_units']=number_format((float)$set_units, 4, '.', '');
                                $newarr['cumml_units']=number_format((float)$set_units, 4, '.', '');
                                $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                array_push($deduct_unit_array,$newarr);
                                $flag='N';
                            }else {
                                if ($flag=='Y') {
                                    $set_units=$purchase_value['cumml_units'];
                                    $purchase_value['cumml_units']=0;
                                    array_push($deduct_unit_array,$purchase_value);
                                    $rdm_tot_units=0;
                                    $newarr=[];
                                    $newarr['trans_date']=$purchase_value['trans_date'];
                                    $newarr['pur_price']=$purchase_value['pur_price'];
                                    $newarr['curr_nav']=$curr_nav;
                                    $newarr['transaction_type']="Remaining";
                                    $newarr['transaction_subtype']="Remaining";
                                    // $newarr['stamp_duty']=$purchase_value['stamp_duty'];
                                    // $newarr['tot_stamp_duty']=$purchase_value['tot_stamp_duty'];
                                    // $newarr['tot_tds']=$purchase_value['tot_tds'];
                                    $newarr['prev_transaction_type']=$purchase_value['transaction_type'];
                                    $newarr['prev_transaction_subtype']=$purchase_value['transaction_type'];
                                    $newarr['tot_units']=number_format((float)$set_units, 4, '.', '');
                                    $newarr['cumml_units']=number_format((float)$set_units, 4, '.', '');
                                    $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    array_push($deduct_unit_array,$newarr);
                                    $flag='N';
                                }else{
                                    // $purchase_value['cumml_units']=number_format((float)($purchase_value->tot_units + $deduct_unit_array[(count($deduct_unit_array)-1)]->cumml_units ), 4, '.', '');
                                    $purchase_value['cumml_units']=number_format((float)($purchase_value['tot_units'] + $deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'] ), 4, '.', '');
                                    // $purchase_value['cumml_units']=number_format((float)($purchase_value->tot_units + $deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'] ), 4, '.', '');
                                    // $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $curr_nav), 2, '.', '');
                                    array_push($deduct_unit_array,$purchase_value);
                                }
                            }
                        }else {
                            // $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $curr_nav), 2, '.', '');
                            // $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $curr_nav), 2, '.', '');
                            array_push($deduct_unit_array,$purchase_value);
                            // return $deduct_unit_array;
                        }
                    }else {
                        // $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $curr_nav), 2, '.', '');
                        array_push($deduct_unit_array,$purchase_value);
                    }
                }
                // return  $deduct_unit_array;
                $purchase_data=$deduct_unit_array;
            }
            // return $purchase_data;
            /*******************************************end purchase and redemption case******************************************/
            $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                // return $value;
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
                if (strpos($value['transaction_subtype'], 'Dividend Reinvestment')!== false) {
                    $idcw_reinv +=number_format((float)$value['tot_amount'], 2, '.', '');
                }
                $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
            }
        }
        // return $all_amt_arr;
        // return $all_date_arr;
        $return_data['inv_cost']=number_format((float)$inv_cost, 2, '.', '');
        $return_data['tot_units']=(count($purchase_data) > 0)?$purchase_data[(count($purchase_data) - 1)]['cumml_units']:0;
        // $return_data['all_amt_arr']=$all_amt_arr;
        // $return_data['all_date_arr']=$all_date_arr;
        $return_data['idcw_reinv']=$idcw_reinv;
        $return_data['idcw_paid']=$idcw_paid;
        // $return_data['cal_purchase_data']=$cal_purchase_data;
        return $return_data;
    }



    public static function calculate_old($foliotrans,$curr_nav,$valuation_as_on){
        $purchase_data=[];
        $redemption_data=[];
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];
        $all_amt_arr=[];
        $all_date_arr=[];
        $return_data=[];
        /*******************************************Start CAMS Broker Change Data**********************************************************/
        $get_rejection_data=[];
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
                if( strpos($foliotrans_value->transaction_subtype, 'Rejection' ) == false) {
                    array_push($final_foliotrans,$foliotrans_value);
                }else {
                    array_push($get_rejection_data,$foliotrans_value);
                }
            }
        }

        $foliotrans=$final_foliotrans;
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
        // return $foliotrans;
        // $return_data['inv_since']=date('Y-m-d',strtotime($foliotrans[0]['trans_date']));
        
        foreach ($foliotrans as $key => $value) {
            if(strpos($value->transaction_subtype, 'Purchase' )!== false || strpos($value->transaction_subtype, 'Switch In' )!== false 
                || strpos($value->transaction_subtype, 'Dividend Reinvestment')!== false || strpos($value->transaction_subtype, 'STP In')!== false) {
                if ($key > 0) {
                    $value->cumml_units=number_format((float)($value->tot_units + $foliotrans[($key-1)]->tot_units) , 4, '.', '') ;
                }else {
                    $value->cumml_units=number_format((float)$value->tot_units, 4, '.', '');
                }
                array_push($purchase_data,$value);
                // array_push($all_amt_arr,-$value->tot_amount);
                // array_push($all_date_arr,$value->trans_date);
            }elseif (strpos($value->transaction_subtype, 'Redemption' )!== false || strpos($value->transaction_subtype, 'Switch Out' )!== false 
                || strpos($value->transaction_subtype, 'Transfer Out')!== false || strpos($value->transaction_subtype, 'SWP')!== false
                || strpos($value->transaction_subtype, 'STP Out')!== false) {
                $value->cumml_units=0;
                array_push($redemption_data,$value);
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
                    if ($purchase_value->cumml_units >= 0) {
                        $purchase_cumml_units=number_format((float)$purchase_value->cumml_units, 4, '.', '');
                        // $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
                        $purchase_value->cumml_units=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
                        if ($purchase_value->cumml_units >= 0 ) {
                            $calculation_cumml_unit=isset($purchase_data[($purchase_key - 1)]->cumml_units)?$purchase_data[($purchase_key - 1)]->cumml_units:0;
                            if ($calculation_cumml_unit < 0) {
                                $set_units=$purchase_value->cumml_units;
                                $purchase_value->cumml_units=0;
                                array_push($deduct_unit_array,$purchase_value);
                                $rdm_tot_units=0;
                                $newarr=[];
                                $newarr['trans_date']=$purchase_value->trans_date;
                                $newarr['pur_price']=$purchase_value->pur_price;
                                $newarr['curr_nav']=$curr_nav;
                                $newarr['transaction_type']="Remaining";
                                $newarr['transaction_subtype']="Remaining";
                                $newarr['stamp_duty']=$purchase_value->stamp_duty;
                                $newarr['tot_stamp_duty']=$purchase_value->tot_stamp_duty;
                                $newarr['tot_tds']=$purchase_value->tot_tds;
                                $newarr['prev_transaction_type']=$purchase_value->transaction_type;
                                $newarr['prev_transaction_subtype']=$purchase_value->transaction_type;
                                $newarr['tot_units']=number_format((float)$set_units, 4, '.', '');
                                $newarr['cumml_units']=number_format((float)$set_units, 4, '.', '');
                                $newarr['tot_amount']= number_format((float)($set_units * $purchase_value->pur_price), 2, '.', '');
                                $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value->pur_price), 2, '.', '');
                                $newarr['gross_amount']=number_format((float)($set_units * $purchase_value->pur_price), 2, '.', '');
                                $newarr['curr_val']=number_format((float)($set_units * $curr_nav), 2, '.', '');
                                array_push($deduct_unit_array,$newarr);
                                $flag='N';
                            }else {
                                if ($flag=='Y') {
                                    $set_units=$purchase_value->cumml_units;
                                    $purchase_value->cumml_units=0;
                                    array_push($deduct_unit_array,$purchase_value);
                                    $rdm_tot_units=0;
                                    $newarr=[];
                                    $newarr['trans_date']=$purchase_value->trans_date;
                                    $newarr['pur_price']=$purchase_value->pur_price;
                                    $newarr['curr_nav']=$curr_nav;
                                    $newarr['transaction_type']="Remaining";
                                    $newarr['transaction_subtype']="Remaining";
                                    $newarr['stamp_duty']=$purchase_value->stamp_duty;
                                    $newarr['tot_stamp_duty']=$purchase_value->tot_stamp_duty;
                                    $newarr['tot_tds']=$purchase_value->tot_tds;
                                    $newarr['prev_transaction_type']=$purchase_value->transaction_type;
                                    $newarr['prev_transaction_subtype']=$purchase_value->transaction_type;
                                    $newarr['tot_units']=number_format((float)$set_units, 4, '.', '');
                                    $newarr['cumml_units']=number_format((float)$set_units, 4, '.', '');
                                    $newarr['tot_amount']= number_format((float)($set_units * $purchase_value->pur_price), 2, '.', '');
                                    $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value->pur_price), 2, '.', '');
                                    $newarr['gross_amount']=number_format((float)($set_units * $purchase_value->pur_price), 2, '.', '');
                                    $newarr['curr_val']=number_format((float)($set_units * $curr_nav), 2, '.', '');
                                    array_push($deduct_unit_array,$newarr);
                                    $flag='N';
                                }else{
                                    $purchase_value->cumml_units=number_format((float)($purchase_value->tot_units + $deduct_unit_array[(count($deduct_unit_array)-1)]->cumml_units ), 4, '.', '');
                                    $purchase_value->curr_val=number_format((float)($purchase_value->tot_units * $curr_nav), 2, '.', '');
                                    array_push($deduct_unit_array,$purchase_value);
                                }
                            }
                        }else {
                            $purchase_value->curr_val=number_format((float)($purchase_value->tot_units * $curr_nav), 2, '.', '');
                            array_push($deduct_unit_array,$purchase_value);
                            // return $deduct_unit_array;
                        }
                    }else {
                        $purchase_value->curr_val=number_format((float)($purchase_value->tot_units * $curr_nav), 2, '.', '');
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
            // $purchase_data = array_filter($purchase_data, function ($var) {
            //     return ($var->cumml_units > 0);
            // });
            $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                // return $value;
                if ($value->cumml_units > 0) {
                    if (strpos($value->transaction_subtype, 'Dividend Reinvestment')!== false) {
                        $idcw_reinv +=number_format((float)$value->tot_amount, 2, '.', '');
                    }
                    $inv_cost +=number_format((float)$value->tot_amount, 2, '.', '');
                }
            }

            $cal_purchase_data=[];
            // foreach ($purchase_data as $key => $value) {
            //     if ($value['cumml_units'] > 0) {
            //         $value['transaction_type']=isset($value["prev_transaction_type"])?$value["prev_transaction_type"]:$value["transaction_type"];
            //         $value['transaction_subtype']=isset($value["prev_transaction_subtype"])?$value["prev_transaction_subtype"]:$value["transaction_subtype"];
            //         $value['curr_nav']=$curr_nav;
                    
            //         $value['tot_amount']= number_format((float)($value['tot_units'] * $value['pur_price']), 2, '.', '');
            //         if (isset($value["prev_transaction_type"])) {
            //             $value['gross_amount']=number_format((float)($value['tot_amount']), 2, '.', '');
            //         }else {
            //             $value['gross_amount']=number_format((float)($value['tot_amount'] + $value['stamp_duty']), 2, '.', '');
            //         }
            //         $value['tot_gross_amount']=number_format((float)($value['tot_units'] * $value['pur_price']), 2, '.', '');
            //         $value['curr_val']=number_format((float)($value['tot_units'] * $value['curr_nav']), 2, '.', '');
            //         $value['gain_loss']=number_format((float)($value['curr_val'] - $value['tot_amount']), 2, '.', '');
            //         if ($value['gain_loss']==0 || $value['tot_amount']==0) {
            //             $value['ret_abs']=0;
            //         }else {
            //             $value['ret_abs']=number_format((float)(($value['gain_loss'] / $value['tot_amount']) * 100), 2, '.', '');
            //         }
            //         $now = strtotime($valuation_as_on); // or your date as well
            //         $your_date = strtotime(date('Y-m-d',strtotime($value['trans_date'])));
            //         $datediff = $now - $your_date;
            //         $days=round($datediff / (60 * 60 * 24));
            //         $value['days']=$days;
            //         array_push($cal_purchase_data,$value);
            //     }
            // }
        }else {
            // $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                if (strpos($value->transaction_subtype, 'Dividend Reinvestment')!== false) {
                    $idcw_reinv +=number_format((float)$value->tot_amount, 2, '.', '');
                }
                $inv_cost +=number_format((float)$value->tot_amount, 2, '.', '');
            }

            $cal_purchase_data=[];
            // foreach ($purchase_data as $key => $value) {
            //     $value['curr_nav']=$curr_nav;
            //     // $value['tot_amount']= number_format((float)($value['tot_units'] * $value['pur_price']), 2, '.', '');
            //     $value['tot_gross_amount']=number_format((float)($value['tot_units'] * $value['pur_price']), 2, '.', '');
            //     $value['gross_amount']=number_format((float)($value['tot_amount'] + $value['stamp_duty']), 2, '.', '');
            //     $value['curr_val']=number_format((float)($value['tot_units'] * $value['curr_nav']), 2, '.', '');
            //     $value['gain_loss']=number_format((float)($value['curr_val'] - $value['tot_amount']), 2, '.', '');
            //     if ($value['gain_loss']==0 || $value['tot_amount']==0) {
            //         $value['ret_abs']=0;
            //     }else {
            //         $value['ret_abs']=number_format((float)(($value['gain_loss'] / $value['tot_amount']) * 100), 2, '.', '');
            //     }
            //     $now = strtotime($valuation_as_on); // or your date as well
            //     $your_date = strtotime(date('Y-m-d',strtotime($value['trans_date'])));
            //     $datediff = $now - $your_date;
            //     $days=round($datediff / (60 * 60 * 24));
            //     $value['days']=$days;
            //     array_push($cal_purchase_data,$value);
            // }
        }
        // return $all_amt_arr;
        // return $all_date_arr;
        $return_data['inv_cost']=$inv_cost;
        $return_data['tot_units']=(count($purchase_data) > 0)?$purchase_data[(count($purchase_data) - 1)]->cumml_units:0;
        // $return_data['all_amt_arr']=$all_amt_arr;
        // $return_data['all_date_arr']=$all_date_arr;
        $return_data['idcw_reinv']=$idcw_reinv;
        $return_data['idcw_paid']=$idcw_paid;
        // $return_data['cal_purchase_data']=$cal_purchase_data;
        return $return_data;
    }
}