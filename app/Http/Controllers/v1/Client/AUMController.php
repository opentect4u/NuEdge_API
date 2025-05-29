<?php
namespace App\Http\Controllers\V1\Client;

use App\Helpers\AumHelper;
use App\Helpers\Helper;
use App\Helpers\TransHelper;
use App\Http\Controllers\Controller;
use App\Models\AumReport;
use App\Models\Client;
use App\Models\ClientFamily;
use App\Models\MFTransTypeSubType;
use App\Models\MutualFundTransaction;
use App\Models\MutualFundTransactionMerge;
use App\Models\SchemeISIN;
use App\Models\Branch;
use App\Models\CurrAumReport;
use DB;
use Illuminate\Http\Request;
use Session;
use carbon\Carbon;

class AUMController extends Controller
{

    /*********************aum report as ************************** */
    public function search(Request $request)
    {
        try {
            // return $request;
            $date = $request->date;
            // $arn_no=$request->arn_no;
            $amc_id     = json_decode($request->amc_id);
            $cat_id     = json_decode($request->cat_id);
            $sub_cat_id = json_decode($request->sub_cat_id);
            $scheme_id  = json_decode($request->scheme_id);
            // return $amc_id;
            // $date=date('Y-m-d');
            // $date='2025-01-12';
            $rnt_id = $request->rnt_id;

            if ($date || $rnt_id || ! empty($amc_id) || ! empty($cat_id) || ! empty($sub_cat_id) || ! empty($scheme_id)) {
                $rawQuery       = '';
                $rawQueryBroker = '';
                if ($date) {
                    $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                    $queryString = 'td_mutual_fund_trans_aum.trans_date';
                    $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
                }
                if ($rnt_id) {
                    $queryString = 'td_mutual_fund_trans_aum.rnt_id';
                    $rawQuery .= Helper::WhereRawQuery($rnt_id, $rawQuery, $queryString);
                }

                $queryString = 'td_mutual_fund_trans_aum.amc_id';
                $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.category_id';
                $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.subcategory_id';
                $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.scheme_id';
                $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

                // $queryString = 'md_scheme.amc_id';
                // $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.category_id';
                // $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.subcategory_id';
                // $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme_isin.scheme_id';
                // $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

            }

            // WHERE trans_date <= '2025-01-31'
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
                ->get();

            // $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
            //     ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
            //     ->leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
            //     ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
            //     ->whereRaw($rawQuery)
            //     ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
            //     ->get();
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $string_version_product_code = implode(',', $all_trans_product);
                $res_array                   = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data = [];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
                // return $value_group_amc_data;
                if ($value_group_amc_data->inv_cost > 0) {
                    $product_code = $value_group_amc_data->product_code;
                    $new          = '';
                    if (count($res_array) > 0) {
                        foreach ($res_array as $val_nav) {
                            if ($val_nav->product_code == $product_code) {
                                $new = $val_nav;
                            }
                        }
                    }
                    $value_group_amc_data->new        = $new;
                    $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                    $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                    $value_group_amc_data->idcw_reinv = 0;
                    $value_group_amc_data->idcw_paid  = 0;
                    $value_group_amc_data->idcwr      = 0;
                    $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                    $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                    $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                    array_push($final_data, $value_group_amc_data);
                }
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray();
            usort($final_data, function ($a, $b) {
                return $a['amc_name'] <=> $b['amc_name'];
                // return $a->amc_name <=> $b->amc_name;
            });
            // return $final_data;

        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    public function aumByClient(Request $request)
    {
        try {
            // return $request;
            $date         = $request->date;
            $product_code = $request->product_code;
            $family_head_id = $request->family_head_id;
            // $arn_no=$request->arn_no;
            $amc_id     = json_decode($request->amc_id);
            $cat_id     = json_decode($request->cat_id);
            $sub_cat_id = json_decode($request->sub_cat_id);
            $scheme_id  = json_decode($request->scheme_id);

            $client_name = $request->client_name;
            $pan_no = $request->pan_no;
            $view_type = $request->view_type;
            $family_members_pan=json_decode($request->family_members_pan);
            $family_members_name=json_decode($request->family_members_name);
            // return $amc_id;
            // $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $client_name || $view_type || $family_members_pan || $family_members_name || $pan_no || $product_code || $family_head_id || ! empty($amc_id) || ! empty($cat_id) || ! empty($sub_cat_id) || ! empty($scheme_id)) {
                $rawQuery       = '';
                $rawQueryBroker = '';
                if ($date) {
                    $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                    $queryString = 'td_mutual_fund_trans_aum.trans_date';
                    $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
                }
                if ($product_code) {
                    $queryString = 'td_mutual_fund_trans_aum.product_code';
                    $rawQuery .= Helper::WhereRawQuery($product_code, $rawQuery, $queryString);
                }
                // if ($pan_no && $client_name) {
                //     $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                //     $rawQuery .= $condition_v.' td_mutual_fund_trans_aum.first_client_pan="' . $pan_no . '" ';
                //     // $rawQuery .= $condition_v.' (td_mutual_fund_trans_aum.first_client_pan="' . $pan_no . '" AND td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client_name . '%")';
                // }else{
                //     $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                //     $rawQuery .= ' OR td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client_name . '%"';
                // }
                if ($view_type=='F') {
                    $queryString='td_mutual_fund_trans_aum.first_client_pan';
                    $condition=(strlen($rawQuery) > 0)? " AND (":" (";
                    $row_name_string=  "'" .implode("','", $family_members_pan). "'";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    $queryString='td_mutual_fund_trans_aum.first_client_name';
                    $condition1=(strlen($rawQuery) > 0)? " OR ":" ";
                    $row_name_string1=  "'" .implode("','", $family_members_name). "'";
                    $rawQuery.=$condition1.$queryString." IN (".$row_name_string1."))";
                }else {
                    if ($pan_no) {
                        $queryString='td_mutual_fund_trans_aum.first_client_pan';
                        $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                    }else if ($client_name) {
                        $queryString='td_mutual_fund_trans_aum.first_client_name';
                        $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                    }
                }
                $queryString = 'td_mutual_fund_trans_aum.amc_id';
                $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.category_id';
                $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.subcategory_id';
                $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.scheme_id';
                $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);
            // return $rawQuery;

                if ($family_head_id) {
                    // return  $request;
                    $all_family_data = ClientFamily::with(['client' => function ($query) {
                        $query->select('id', 'client_code', 'client_name', 'pan');
                    }])
                        ->select('client_id', 'family_id', 'type')
                        ->where('client_id', $family_head_id)
                        ->get();
                    // return $all_family_data;
                    $rawQuery .=' AND (';
                    // foreach ($all_family_data as $client_key => $client) {
                    //     if (isset($client->client)) {
                    //         if ($client->client->pan) {
                    //             $condition_v = ($client_key > 0) ? " OR " : " ";
                    //             $rawQuery .= $condition_v.' (td_mutual_fund_trans_aum.first_client_pan="' . $client->client->pan . '" AND td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client->client->client_name . '%")';
                    //         } else {
                    //             $rawQuery .= ' OR td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client->client->client_name . '%"';
                    //         }
                    //     }
                    // }
                    foreach ($all_family_data as $client_key => $client) {
                        if (isset($client->client)) {
                            if ($client->client->pan) {
                                $condition_v = ($client_key > 0) ? " OR " : " ";
                                $rawQuery .= $condition_v.' (td_mutual_fund_trans_aum.first_client_pan="' . $client->client->pan . '" AND td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client->client->client_name . '%")';
                            } else {
                                $rawQuery .= ' OR td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client->client->client_name . '%"';
                            }
                        }
                    }
                    $rawQuery .= ')';
                }
            }
            
            // WHERE trans_date <= '2025-01-31'
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
                ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
                ->leftJoin('md_client', 'md_client.pan', '=', 'td_mutual_fund_trans_aum.first_client_pan')
            // ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost')
                // ->selectRaw('td_mutual_fund_trans_aum.*,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost')
                ->selectRaw('md_client.id as client_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,total_unit AS tot_units,total_inv_cost as inv_cost')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.folio_no', 'td_mutual_fund_trans_aum.product_code')
                ->get();
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                // return count($all_trans_product).' - '.count($all_trans_product_unique);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                // return $string_version_product_code;
                // return 'SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code);
                $res_array = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $all_data;
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data = [];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
                // return $value_group_amc_data;
                
                $product_code = $value_group_amc_data->product_code;
                $new          = '';
                if (count($res_array) > 0) {
                    foreach ($res_array as $val_nav) {
                        if ($val_nav->product_code == $product_code) {
                            $new = $val_nav;
                        }
                    }
                }
                $value_group_amc_data->new        = $new;
                $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                $value_group_amc_data->idcw_reinv = 0;
                $value_group_amc_data->idcw_paid  = 0;
                $value_group_amc_data->idcwr      = 0;
                $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                array_push($final_data, $value_group_amc_data);
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray();
            // usort($final_data, function($a, $b) {
            //     return $a['first_client_name'] <=> $b['first_client_name'];
            //     // return $a->amc_name <=> $b->amc_name;
            // });
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    public function aumByFamily(Request $request)
    {
        try {
            // return $request;
            $date         = $request->date;
            $product_code = $request->product_code;
            // $arn_no=$request->arn_no;
            $amc_id     = json_decode($request->amc_id);
            $cat_id     = json_decode($request->cat_id);
            $sub_cat_id = json_decode($request->sub_cat_id);
            $scheme_id  = json_decode($request->scheme_id);
            // return $amc_id;
            $date = date('Y-m-d');
            // $date='2025-01-12';
            $rawQuery       = '';
            if ($date || $product_code || ! empty($amc_id) || ! empty($cat_id) || ! empty($sub_cat_id) || ! empty($scheme_id)) {
                $rawQueryBroker = '';
                if ($date) {
                    $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                    $queryString = 'td_mutual_fund_trans_aum.trans_date';
                    $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
                }
                if ($product_code) {
                    $queryString = 'td_mutual_fund_trans_aum.product_code';
                    $rawQuery .= Helper::WhereRawQuery($product_code, $rawQuery, $queryString);
                }

                $queryString = 'td_mutual_fund_trans_aum.amc_id';
                $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.category_id';
                $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.subcategory_id';
                $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                $queryString = 'td_mutual_fund_trans_aum.scheme_id';
                $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);
            }
            // return $rawQuery;
            $all_family_data = ClientFamily::with(['client' => function ($query) {
                $query->select('id', 'client_code', 'client_name', 'pan');
            }])
                ->select('client_id', 'family_id', 'type')
                ->get();

            // return $all_family_data;
            if(count($all_family_data) > 0){
                $rawQuery .= ' AND (';
                foreach ($all_family_data as $client_key => $client) {
                    if (isset($client->client)) {
                        if ($client->client->pan) {
                            $condition = ($client_key > 0) ? " OR " : " ";
                            $rawQuery .= $condition.' (td_mutual_fund_trans_aum.first_client_pan="' . $client->client->pan . '" AND td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client->client->client_name . '%")';
                        } else {
                            $rawQuery .= ' OR td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client->client->client_name . '%"';
                        }
                    }
                }
                $rawQuery .= ')';
            }
            // return $rawQuery;
            // DB::enableQueryLog();
            $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
                ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,td_mutual_fund_trans_aum.total_unit AS tot_units,td_mutual_fund_trans_aum.total_inv_cost as inv_cost')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.folio_no','td_mutual_fund_trans_aum.product_code')
                ->get();
            // dd(DB::getQueryLog());

            // return $all_data;
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            $res_array = [];
            if (count($all_data) > 0) {
                $all_trans_product_unique    = array_unique($all_trans_product);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                $res_array                   = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }

            $final_data = [];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
                // return $value_group_amc_data;
                $product_code = $value_group_amc_data->product_code;
                $new          = '';
                if (count($res_array) > 0) {
                    foreach ($res_array as $val_nav) {
                        if ($val_nav->product_code == $product_code) {
                            $new = $val_nav;
                        }
                    }
                }
                $value_group_amc_data->new        = $new;
                $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                $value_group_amc_data->idcw_reinv = 0;
                $value_group_amc_data->idcw_paid  = 0;
                $value_group_amc_data->idcwr      = 0;
                $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                array_push($final_data, $value_group_amc_data);
            }
            // return $final_data;
            $final_final_data=[];
            foreach ($all_family_data as $key => $family_data) {
                // return $family_data;
                $new='';
                if (isset($family_data->client)) {
                    $filterByName = $family_data->client->client_name;
                    if ($family_data->client->pan) {
                        $filterByPan = $family_data->client->pan;
                        $new = array_filter($final_data, function ($var) use ($filterByPan,$filterByName) {
                            return ($var['first_client_pan'] == $filterByPan && stripos($var['first_client_name'], $filterByName) !== FALSE);
                        });
                    }else {
                        $new = array_filter($final_data, function ($var) use ($filterByPan,$filterByName) {
                            return (stripos($var['first_client_name'], $filterByName) !== FALSE);
                        });
                    }
                }
                $family_data['trans_data']=$new;
                array_push($final_final_data,$family_data);
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_final_data);
    }


    public function aumBranch(Request $request)
    {
        try {
            // return $request;
            $date = $request->date;
            // $arn_no=$request->arn_no;
            $amc_id     = json_decode($request->amc_id);
            $cat_id     = json_decode($request->cat_id);
            $sub_cat_id = json_decode($request->sub_cat_id);
            $scheme_id  = json_decode($request->scheme_id);
            // return $amc_id;
            $date=date('Y-m-d');
            // $date='2025-01-12';
            $brn_cd = $request->brn_cd;
            $brn_cd =1;
            if ($date || $brn_cd ) {
                $rawQuery       = '';
                $rawQueryBroker = '';
                if ($date) {
                    $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                    $queryString = 'td_mutual_fund_trans_aum.trans_date';
                    $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
                }
                // if ($brn_cd) {
                //     $queryString = 'td_mutual_fund_trans_aum.rnt_id';
                //     $rawQuery .= Helper::WhereRawQuery($rnt_id, $rawQuery, $queryString);
                // }

                // $queryString = 'md_scheme.amc_id';
                // $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.category_id';
                // $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.subcategory_id';
                // $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme_isin.scheme_id';
                // $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

                // $queryString = 'md_scheme.amc_id';
                // $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.category_id';
                // $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.subcategory_id';
                // $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme_isin.scheme_id';
                // $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

            }

            // WHERE trans_date <= '2025-01-31'
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                ->selectRaw('(SELECT euin_no FROM tt_folio_details_reports WHERE folio_no=td_mutual_fund_trans_aum.folio_no limit 1)as euin_no')
                ->selectRaw('(SELECT branch_id FROM md_employee WHERE euin_no=(SELECT euin_no FROM tt_folio_details_reports WHERE folio_no=td_mutual_fund_trans_aum.folio_no limit 1) limit 1)as branch_id')
                // ->selectRaw('(SELECT brn_name FROM md_branch WHERE id=(SELECT branch_id FROM md_employee WHERE euin_no=(SELECT euin_no FROM tt_folio_details_reports WHERE folio_no=td_mutual_fund_trans_aum.folio_no limit 1) limit 1) limit 1)as branch_name')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
                ->get();

            // $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
            //     ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
            //     ->leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
            //     ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
            //     ->whereRaw($rawQuery)
            //     ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
            //     ->get();
            // return $all_data;
            $all_branch=Branch::select('id','brn_name')->get();
            // return $all_branch;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $string_version_product_code = implode(',', $all_trans_product);
                $res_array                   = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data = [];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
                // return $value_group_amc_data;
                if ($value_group_amc_data->inv_cost > 0) {
                    $product_code = $value_group_amc_data->product_code;
                    $branch_id = $value_group_amc_data->branch_id;
                    // return $branch_id;
                    $branch_name='';
                    if (count($all_branch) > 0) {
                        foreach ($all_branch as $val_branch) {
                            if ($val_branch->id == $branch_id) {
                                $branch_name = $val_branch->brn_name;
                            }
                        }
                    }
                    $value_group_amc_data->branch_name=$branch_name;
                    // return $value_group_amc_data;
                    $new          = '';
                    if (count($res_array) > 0) {
                        foreach ($res_array as $val_nav) {
                            if ($val_nav->product_code == $product_code) {
                                $new = $val_nav;
                            }
                        }
                    }
                    $value_group_amc_data->new        = $new;
                    $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                    $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                    $value_group_amc_data->idcw_reinv = 0;
                    $value_group_amc_data->idcw_paid  = 0;
                    $value_group_amc_data->idcwr      = 0;
                    $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                    $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                    $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                    array_push($final_data, $value_group_amc_data);
                }
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray();
            usort($final_data, function ($a, $b) {
                return $a['amc_name'] <=> $b['amc_name'];
                // return $a->amc_name <=> $b->amc_name;
            });
            // return $final_data;

        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    public function aumSegment(Request $request)
    {
        try {
            // return $request;
            $date = $request->date;
            // $arn_no=$request->arn_no;
            $amc_id     = json_decode($request->amc_id);
            $cat_id     = json_decode($request->cat_id);
            $sub_cat_id = json_decode($request->sub_cat_id);
            $scheme_id  = json_decode($request->scheme_id);
            // return $amc_id;
            $date=date('Y-m-d');
            // $date='2025-01-12';
            $brn_cd = $request->brn_cd;
            $brn_cd =1;
            if ($date || $brn_cd ) {
                $rawQuery       = '';
                $rawQueryBroker = '';
                if ($date) {
                    $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                    $queryString = 'td_mutual_fund_trans_aum.trans_date';
                    $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
                }
                // if ($brn_cd) {
                //     $queryString = 'td_mutual_fund_trans_aum.rnt_id';
                //     $rawQuery .= Helper::WhereRawQuery($rnt_id, $rawQuery, $queryString);
                // }

                // $queryString = 'md_scheme.amc_id';
                // $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.category_id';
                // $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.subcategory_id';
                // $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme_isin.scheme_id';
                // $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

                // $queryString = 'md_scheme.amc_id';
                // $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.category_id';
                // $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.subcategory_id';
                // $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme_isin.scheme_id';
                // $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

            }

            // WHERE trans_date <= '2025-01-31'
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                ->selectRaw('(SELECT euin_no FROM tt_folio_details_reports WHERE folio_no=td_mutual_fund_trans_aum.folio_no limit 1)as euin_no')
                ->selectRaw('(SELECT bu_type_id FROM md_employee WHERE euin_no=(SELECT euin_no FROM tt_folio_details_reports WHERE folio_no=td_mutual_fund_trans_aum.folio_no limit 1) limit 1)as bu_type_id')
                // ->selectRaw('(SELECT brn_name FROM md_branch WHERE id=(SELECT branch_id FROM md_employee WHERE euin_no=(SELECT euin_no FROM tt_folio_details_reports WHERE folio_no=td_mutual_fund_trans_aum.folio_no limit 1) limit 1) limit 1)as branch_name')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
                ->get();

            // $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
            //     ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
            //     ->leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
            //     ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
            //     ->whereRaw($rawQuery)
            //     ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
            //     ->get();
            // return $all_data;
            // $business_type=DB::table('md_business_type')->select('id','brn_name')->groupBy('bu_code')->get();
            // return $business_type;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $string_version_product_code = implode(',', $all_trans_product);
                $res_array                   = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data = [];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
                // return $value_group_amc_data;
                if ($value_group_amc_data->inv_cost > 0) {
                    $product_code = $value_group_amc_data->product_code;
                    // return $value_group_amc_data;
                    $new          = '';
                    if (count($res_array) > 0) {
                        foreach ($res_array as $val_nav) {
                            if ($val_nav->product_code == $product_code) {
                                $new = $val_nav;
                            }
                        }
                    }
                    $value_group_amc_data->new        = $new;
                    $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                    $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                    $value_group_amc_data->idcw_reinv = 0;
                    $value_group_amc_data->idcw_paid  = 0;
                    $value_group_amc_data->idcwr      = 0;
                    $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                    $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                    $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                    array_push($final_data, $value_group_amc_data);
                }
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray();
            usort($final_data, function ($a, $b) {
                return $a['amc_name'] <=> $b['amc_name'];
                // return $a->amc_name <=> $b->amc_name;
            });
            // return $final_data;

        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    public function aumCityType(Request $request)
    {
        try {
            // return $request;
            $date         = $request->date;
            $city_type_id = $request->city_type_id;
            // $arn_no=$request->arn_no;
            // return $amc_id;
            $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $city_type_id) {
                $rawQuery       = '';
                $rawQueryBroker = '';
                if ($date) {
                    $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                    $queryString = 'td_mutual_fund_trans_aum.trans_date';
                    $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
                }
                if ($city_type_id) {
                    $queryString = 'md_pincode.city_type_id';
                    $rawQuery .= Helper::WhereRawQuery($city_type_id, $rawQuery, $queryString);
                }
            }
            
            // WHERE trans_date <= '2025-01-31'
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
                ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
                ->leftJoin('tt_folio_details_reports', 'tt_folio_details_reports.folio_no', '=', 'td_mutual_fund_trans_aum.folio_no')
                ->leftJoin('md_pincode', 'md_pincode.pincode', '=', 'tt_folio_details_reports.pincode')
                ->leftJoin('md_city_type', 'md_city_type.id', '=', 'md_pincode.city_type_id')
            // ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost')
                // ->selectRaw('td_mutual_fund_trans_aum.*,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost')
                ->selectRaw('td_mutual_fund_trans_aum.*,total_unit AS tot_units,total_inv_cost as inv_cost')
                ->selectRaw('md_pincode.city_type_id,md_city_type.name as city_type_name')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.folio_no', 'td_mutual_fund_trans_aum.product_code')
                ->get();
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                // return count($all_trans_product).' - '.count($all_trans_product_unique);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                // return $string_version_product_code;
                // return 'SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code);
                $res_array = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $all_data;
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data = [];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
                // return $value_group_amc_data;
                
                $product_code = $value_group_amc_data->product_code;
                $new          = '';
                if (count($res_array) > 0) {
                    foreach ($res_array as $val_nav) {
                        if ($val_nav->product_code == $product_code) {
                            $new = $val_nav;
                        }
                    }
                }
                $value_group_amc_data->new        = $new;
                $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                $value_group_amc_data->idcw_reinv = 0;
                $value_group_amc_data->idcw_paid  = 0;
                $value_group_amc_data->idcwr      = 0;
                $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                array_push($final_data, $value_group_amc_data);
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray();
            // usort($final_data, function($a, $b) {
            //     return $a['first_client_name'] <=> $b['first_client_name'];
            //     // return $a->amc_name <=> $b->amc_name;
            // });
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    /*******************AUM Growth Report************************/
    public function aumGrowth(Request $request)
    {
        try {
            // return $request;
            $fin_year = $request->fin_year;
            $month_for = $request->month_for;
            // $fin_year="2025-2026";
            // $fin_year="2024-2025";
            // $fin_year="Last 5 Year";
            $rawQuery = '';
            switch ($fin_year) {
                case 'Last 5 Year':
                    $startDates=[];
                    for ($i=0; $i <= 5; $i++) { 
                        $getFinYear=explode('-',Helper::getFinYear());
                        $startYear=($getFinYear[0]-$i).'-03-01';
                        $date = date("Y-m-t", strtotime($startYear));
                        array_push($startDates,$date);
                        $queryString = 'td_mutual_fund_curr_aum.trans_date';
                        $condition_v = (strlen($rawQuery) > 0) ? " OR " : " ";
                        $rawQuery .= $condition_v ."(". $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_curr_aum WHERE trans_date <='" . $date . "'))";
                        
                    }
                    break;
                case 'Month':
                    $endYear=date("Y-m-d", strtotime("- 1 day"));
                    // strtotime($endYear) > strtotime(date('Y-m-d')) ? date("Y-m-d", strtotime("- 1 day")) : date("Y-m-t",strtotime($endYear));
                    // $start_date = date("Y-m-t", strtotime("-1 month", strtotime($startYear)));

                    $startDates=[];
                    $time=strtotime($endYear);
                    // $upto=strtotime($startYear);
                    // $startDates .=$endYear." - ";
                    array_push($startDates,$endYear);
                    for ($i=1; $i <= $month_for; $i++) { 
                        $date = date("Y-m-t", strtotime("-".$i." month", $time));
                        // return $date;
                        // if(strtotime($start_date) <= strtotime($date)) {
                        //     array_push($startDates,$date);
                        //     // $startDates .=$date." - ";
                        // }else {
                        //     break;
                        // }
                        array_push($startDates,$date);
                        $queryString = 'td_mutual_fund_curr_aum.trans_date';
                        $condition_v = (strlen($rawQuery) > 0) ? " OR " : " ";
                        $rawQuery .= $condition_v ."(". $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_curr_aum WHERE trans_date <='" . $date . "'))";
                        

                    }
                    break;
                case 'YTD':
                    $year= date('Y') - 2018;
                    $startDates=[];
                    for ($i=0; $i <= $year; $i++) { 
                        $getFinYear=explode('-',Helper::getFinYear());
                        $startYear=($getFinYear[0]-$i).'-03-01';
                        $date = date("Y-m-t", strtotime($startYear));
                        // return $date;
                        // array_push($startDates,$date);
                        $queryString = 'td_mutual_fund_curr_aum.trans_date';
                        $condition_v = (strlen($rawQuery) > 0) ? " OR " : " ";
                        $rawQuery .= $condition_v ."(". $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_curr_aum WHERE trans_date <='" . $date . "'))";
                        
                    }
                    break;
                default:
                    $years=explode("-",$fin_year);
                    $start = Carbon::create($years[0],3,1); // April 1, 2024
                    $end = Carbon::create($years[1],3,30);  // March 31, 2025
                    // return $start;
                    $dates = [];
                    $current = $start->copy();
                    while ($current <= $end) {
                        $dates[] = $current->copy()->endOfMonth()->toDateString(); // Last date of the month
                        $current->addMonth(); // Move to the next month
                    }
                    foreach ($dates as $date) {
                        // array_push($startDates,$date);
                        $queryString = 'td_mutual_fund_curr_aum.trans_date';
                        $condition_v = (strlen($rawQuery) > 0) ? " OR " : " ";
                        $rawQuery .= $condition_v ."(". $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_curr_aum WHERE trans_date <='" . $date . "'))";
                        
                    }
                    break;
            }
            // return $startDates;
            // return $rawQuery;

            // $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
            //         $queryString = 'td_mutual_fund_trans_aum.trans_date';
            //         $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";

            // $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
            //     ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
            //     ->whereIn('td_mutual_fund_trans_aum.trans_date', $startDates)
            //     ->groupBy('td_mutual_fund_trans_aum.trans_date','td_mutual_fund_trans_aum.folio_no', 'td_mutual_fund_trans_aum.product_code')
            //     ->get();
            // // return $all_data;
            // $all_trans_product = [];
            // foreach ($all_data as $value_product_code) {
            //     $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $value_product_code->trans_date . "') AND product_code='" . $value_product_code->product_code . "')";
            //     array_push($all_trans_product, $f_trans_product);
            // }
            // // return $all_trans_product;
            // $res_array = [];
            // if (count($all_data) > 0) {
            //     $all_trans_product_unique = array_unique($all_trans_product);
            //     $string_version_product_code = implode(',', $all_trans_product_unique);
            //     $res_array = DB::connection('mysql_nav')
            //         ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            // }
            // // return  $res_array;
            // $final_data = [];
            // foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
            //     // return $value_group_amc_data;
            //     $product_code = $value_group_amc_data->product_code;
            //     $trans_date = $value_group_amc_data->trans_date;
            //     $new          = '';
            //     if (count($res_array) > 0) {
            //         foreach ($res_array as $val_nav) {
            //             if ($val_nav->product_code == $product_code && $val_nav->nav_date >= $trans_date) {
            //                 $new = $val_nav;
            //             }
            //         }
            //     }
            //     // return  $new;
            //     $value_group_amc_data->new        = $new;
            //     $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
            //     $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
            //     $value_group_amc_data->idcw_reinv = 0;
            //     $value_group_amc_data->idcw_paid  = 0;
            //     $value_group_amc_data->idcwr      = 0;
            //     $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
            //     $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
            //     $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
            //     array_push($final_data, $value_group_amc_data);
            // }
            // // return  $final_data;
            // $final_data1=[];
            // foreach ($final_data as $key => $final_value) {
            //     $final_data1[$final_value->trans_date][]=$final_value;
            // }
            // // return $final_data1;
            // $final_final_data=[];
            // foreach ($final_data1 as $key_final_data1 => $final_data1_value) {
            //     // return $key_final_data1;
            //     $set_data=[];
            //     $amount=0;
            //     foreach ($final_data1_value as $key => $final_data1_value_value) {
            //         // return $final_data1_value_value;
            //         if($final_data1_value_value->curr_aum > 0) {
            //             // return $final_data1_value_value;
            //             $amount +=$final_data1_value_value->curr_aum;
            //         }
            //     }
            //     $set_data['date']=$key_final_data1;
            //     $set_data['aum']=$amount;
            //     array_push($final_final_data,$set_data);
            // }
            $data=CurrAumReport::select('trans_date','curr_aum')->whereRaw($rawQuery)->get();

            $final_final_data=[];
            foreach ($data as $key => $value) {
                $final_final_data[$value->trans_date]=$value->curr_aum;
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_final_data);
    }

    public function aumTopClient(Request $request)
    {
        try {
            // return $request;
            $date         = $request->date;
            $product_code = $request->product_code;
            $family_head_id = $request->family_head_id;
            // $arn_no=$request->arn_no;
            $amc_id     = json_decode($request->amc_id);
            $cat_id     = json_decode($request->cat_id);
            $sub_cat_id = json_decode($request->sub_cat_id);
            $scheme_id  = json_decode($request->scheme_id);
            // return $amc_id;
            // $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $product_code || $family_head_id || ! empty($amc_id) || ! empty($cat_id) || ! empty($sub_cat_id) || ! empty($scheme_id)) {
                $rawQuery       = '';
                $rawQueryBroker = '';
                if ($date) {
                    $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                    $queryString = 'td_mutual_fund_trans_aum.trans_date';
                    $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
                }
                if ($product_code) {
                    $queryString = 'td_mutual_fund_trans_aum.product_code';
                    $rawQuery .= Helper::WhereRawQuery($product_code, $rawQuery, $queryString);
                }
                // $queryString = 'md_scheme.amc_id';
                // $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.category_id';
                // $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme.subcategory_id';
                // $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                // $queryString = 'md_scheme_isin.scheme_id';
                // $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

                if ($family_head_id) {
                    // return  $request;
                    $all_family_data = ClientFamily::with(['client' => function ($query) {
                        $query->select('id', 'client_code', 'client_name', 'pan');
                    }])
                        ->select('client_id', 'family_id', 'type')
                        ->where('client_id', $family_head_id)
                        ->get();
                    // return $all_family_data;
                    if(count($all_family_data) > 0) {
                        $rawQuery .=' AND (';
                        foreach ($all_family_data as $client_key => $client) {
                            if (isset($client->client)) {
                                if ($client->client->pan) {
                                    $condition_v = ($client_key > 0) ? " OR " : " ";
                                    $rawQuery .= $condition_v.' (td_mutual_fund_trans_aum.first_client_pan="' . $client->client->pan . '" AND td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client->client->client_name . '%")';
                                } else {
                                    $rawQuery .= ' OR td_mutual_fund_trans_aum.first_client_name LIKE "%' . $client->client->client_name . '%"';
                                }
                            }
                        }
                        $rawQuery .= ')';
                    }
                }
            }
            
            // WHERE trans_date <= '2025-01-31'
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
                ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
            // ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost')
                // ->selectRaw('td_mutual_fund_trans_aum.*,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost')
                ->selectRaw('td_mutual_fund_trans_aum.*,total_unit AS tot_units,total_inv_cost as inv_cost')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.folio_no', 'td_mutual_fund_trans_aum.product_code')
                ->get();
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                // return count($all_trans_product).' - '.count($all_trans_product_unique);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                // return $string_version_product_code;
                // return 'SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code);
                $res_array = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $all_data;
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data = [];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
                // return $value_group_amc_data;
                
                $product_code = $value_group_amc_data->product_code;
                $new          = '';
                if (count($res_array) > 0) {
                    foreach ($res_array as $val_nav) {
                        if ($val_nav->product_code == $product_code) {
                            $new = $val_nav;
                        }
                    }
                }
                $value_group_amc_data->new        = $new;
                $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                $value_group_amc_data->idcw_reinv = 0;
                $value_group_amc_data->idcw_paid  = 0;
                $value_group_amc_data->idcwr      = 0;
                $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                array_push($final_data, $value_group_amc_data);
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray();
            // usort($final_data, function($a, $b) {
            //     return $a['first_client_name'] <=> $b['first_client_name'];
            //     // return $a->amc_name <=> $b->amc_name;
            // });
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    public function aumGrowth1(Request $request)
    {
        try {
            // return $request;
            $fin_year = $request->fin_year;
            // $fin_year="2025-2026";
            $fin_year="2024-2025";
            $fin_year="Last 5 Year";
            $startDates=[];
            if ($fin_year=="Last 5 Year") {
                for ($i=1; $i <= 5; $i++) { 
                    $getFinYear=explode('-',Helper::getFinYear());
                    // return  $getFinYear;
                    $date=($getFinYear[1]-$i)."-03-01";
                    $date_format=date("Y-m-t", strtotime($date));
                    // return $date_format;
                    array_push($startDates,$date_format);
                }
                // return $startDates;
                // $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                // $queryString = 'td_mutual_fund_trans_aum.trans_date';
                // $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
            }else {
                $years=explode("-",$fin_year);
                // return $years;
                $startYear=$years[0].'-04-01';
                $endYear=$years[1].'-03-30';
                $endYear=strtotime($endYear) > strtotime(date('Y-m-d')) ? date("Y-m-d", strtotime("- 1 day")) : date("Y-m-t",strtotime($endYear));
                $start_date = date("Y-m-t", strtotime("-1 month", strtotime($startYear)));
                // return $startYear.' - '.$endYear;
                // return $start_date;
                $time=strtotime($endYear);
                // $upto=strtotime($startYear);
                // $startDates .=$endYear." - ";
                array_push($startDates,$endYear);
                for ($i=1; $i <= 12; $i++) { 
                    $date = date("Y-m-t", strtotime("-".$i." month", $time));
                    // return $date;
                    if(strtotime($start_date) <= strtotime($date)) {
                        array_push($startDates,$date);
                        // $startDates .=$date." - ";
                    }else {
                        break;
                    }
                }
            }
            // return $startDates;
            $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                // ->whereRaw($rawQuery)
                ->whereIn('td_mutual_fund_trans_aum.trans_date', $startDates)
                ->groupBy('td_mutual_fund_trans_aum.trans_date','td_mutual_fund_trans_aum.folio_no', 'td_mutual_fund_trans_aum.product_code')
                ->get();
            // return $all_data;
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $value_product_code->trans_date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                // return count($all_trans_product).' - '.count($all_trans_product_unique);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                // return $string_version_product_code;
                // return 'SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code);
                $res_array = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return  $res_array;
            $final_data = [];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
                // return $value_group_amc_data;
                $product_code = $value_group_amc_data->product_code;
                $trans_date = $value_group_amc_data->trans_date;
                $new          = '';
                if (count($res_array) > 0) {
                    foreach ($res_array as $val_nav) {
                        if ($val_nav->product_code == $product_code && $val_nav->nav_date >= $trans_date) {
                            $new = $val_nav;
                        }
                    }
                }
                // return  $new;
                $value_group_amc_data->new        = $new;
                $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                $value_group_amc_data->idcw_reinv = 0;
                $value_group_amc_data->idcw_paid  = 0;
                $value_group_amc_data->idcwr      = 0;
                $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                array_push($final_data, $value_group_amc_data);
            }
            // return  $final_data;
            $final_data1=[];
            foreach ($final_data as $key => $final_value) {
                $final_data1[$final_value->trans_date][]=$final_value;
            }
            // return $final_data1;
            $final_final_data=[];
            foreach ($final_data1 as $key_final_data1 => $final_data1_value) {
                // return $key_final_data1;
                $set_data=[];
                $amount=0;
                foreach ($final_data1_value as $key => $final_data1_value_value) {
                    // return $final_data1_value_value;
                    if($final_data1_value_value->curr_aum > 0) {
                        // return $final_data1_value_value;
                        $amount +=$final_data1_value_value->curr_aum;
                    }
                }
                $set_data['date']=$key_final_data1;
                $set_data['aum']=$amount;
                array_push($final_final_data,$set_data);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_final_data);
    }

    public function aumtest(){
        $string1="Dear Suman Mitra,\nGreetings from NuEdge Corporate Private Limited\nYour below query is Re In-Process.\nQuery Id- QRY-MF-1261.\nQuery Details- sg0.co/AOB47d .\nExpected Close Date- 01-05-2025.\nNow you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.\nWe sincerely regret the inconvenience caused by the delay.\n\nRegards,\nNuEdge Corporate Private Limited.\nAMFI- Registered Mutual Fund Distributor\nMutual Fund investments are subject to market risks, read all scheme related documents carefully.";

        
        

        

        
        $array1=explode("-",$string1);
        // return $array1;
        // return $array1[4];
        // return "https://".trim(str_replace(".\nActual Close Date"," ",$array1[4]));
        $query_url="https://".trim(str_replace(".\nExpected Close Date"," ",$array1[5]));
        return $query_url;
        // $feedback_url="https://".trim(str_replace("\n\nRegards,\nNuEdge Corporate Private Limited.\nAMFI","",$array1[10]));
        // return $feedback_url;
    }
}