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
use DB;
use Illuminate\Http\Request;
use Session;

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

                $queryString = 'md_scheme.amc_id';
                $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                $queryString = 'md_scheme.category_id';
                $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                $queryString = 'md_scheme.subcategory_id';
                $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                $queryString = 'md_scheme_isin.scheme_id';
                $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

            }

            // WHERE trans_date <= '2025-01-31'
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
                ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
                ->leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
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
                $queryString = 'md_scheme.amc_id';
                $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                $queryString = 'md_scheme.category_id';
                $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                $queryString = 'md_scheme.subcategory_id';
                $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                $queryString = 'md_scheme_isin.scheme_id';
                $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);

                if ($family_head_id) {
                    // return  $request;
                    $all_family_data = ClientFamily::with(['client' => function ($query) {
                        $query->select('id', 'client_code', 'client_name', 'pan');
                    }])
                        ->select('client_id', 'family_id', 'type')
                        ->where('client_id', $family_head_id)
                        ->get();
                    // return $all_family_data;
                    $rawQuery .=' AND ';
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

                $queryString = 'md_scheme.amc_id';
                $rawQuery .= Helper::WhereRawQuery($amc_id, $rawQuery, $queryString);
                $queryString = 'md_scheme.category_id';
                $rawQuery .= Helper::WhereRawQuery($cat_id, $rawQuery, $queryString);
                $queryString = 'md_scheme.subcategory_id';
                $rawQuery .= Helper::WhereRawQuery($sub_cat_id, $rawQuery, $queryString);
                $queryString = 'md_scheme_isin.scheme_id';
                $rawQuery .= Helper::WhereRawQuery($scheme_id, $rawQuery, $queryString);
            }
            // return $rawQuery;
            $all_family_data = ClientFamily::with(['client' => function ($query) {
                $query->select('id', 'client_code', 'client_name', 'pan');
            }])
                ->select('client_id', 'family_id', 'type')
                ->get();

            // return $all_family_data;
            $rawQuery .= ' AND ';
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
}