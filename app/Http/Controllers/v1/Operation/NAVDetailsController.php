<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MFTransTypeSubType,
    NAVDetails
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class NAVDetailsController extends Controller
{
    public function search(Request $request)
    {
        try {
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $subcat_id=json_decode($request->subcat_id);
            $scheme_id=json_decode($request->scheme_id);
            $periods=$request->date_periods;
            $date_range=$request->date_range;
            $plan_type=$request->plan_type;

            $rawQuery='';
            $rawQuery1='';

            if (!empty($amc_id) || !empty($cat_id) || !empty($subcat_id) || !empty($scheme_id)) {
                $queryString3='s.amc_id';
                $rawQuery1.=Helper::WhereRawQuery($amc_id,$rawQuery1,$queryString3);
                $queryString3='s.category_id';
                $rawQuery1.=Helper::WhereRawQuery($cat_id,$rawQuery1,$queryString3);
                $queryString3='s.subcategory_id';
                $rawQuery1.=Helper::WhereRawQuery($subcat_id,$rawQuery1,$queryString3);
                $queryString3='s.id';
                $rawQuery1.=Helper::WhereRawQuery($scheme_id,$rawQuery1,$queryString3);
            }
            // return $rawQuery1;
            // $queryString='td_nav_details.nav_date';
            // if ($date_periods=='D') {
            //     $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
            //     $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;  
            //     $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
            // }elseif ($date_periods=='M') {
            //     $f_date="01-".str_replace('/','-',explode("-",$date_range)[0]);
            //     $t_date=date('d')."-".str_replace('/','-',explode("-",$date_range)[1]);
            //     $from_date=Carbon::parse(str_replace(' ','',$f_date))->format('Y-m-d');
            //     $to_date=Carbon::parse(str_replace(' ','',$t_date))->format('Y-m-d');
            //     // return  $t_date;
            //     $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
            //     $row_name_string=  "'" .implode("','", $benchmark). "'";
            // }elseif ($date_periods=='Y') {
            //     $f_date="01-01-".str_replace('/','-',explode("-",$date_range)[0]);
            //     $t_date=date('d-m')."-".str_replace('/','-',explode("-",$date_range)[1]);
            //     $from_date=Carbon::parse(str_replace(' ','',$f_date))->format('Y-m-d');
            //     $to_date=Carbon::parse(str_replace(' ','',$t_date))->format('Y-m-d');
            //     // return  $t_date;
            //     $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
            //     $row_name_string=  "'" .implode("','", $benchmark). "'";
            // }


            $modify_rawQuery1=($rawQuery1=='')?'':' AND '.$rawQuery1;
            // return $modify_rawQuery1;
            $queryString='n.nav_date';
            switch ($periods) {
                case 'D':
                    $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                    $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;  
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    // return $rawQuery;
                    $my_data=DB::select('SELECT n.*,s.scheme_name,c.cat_name,sc.subcategory_name as subcat_name,a.amc_short_name as amc_name,a1.amc_short_name as amc_short_name,
                        p.plan_name as plan_name,o.opt_name as option_name
                        FROM td_nav_details AS n
                        LEFT JOIN md_scheme_isin AS si ON n.product_code=si.product_code
                        LEFT JOIN md_plan AS p ON si.plan_id=p.id
                        LEFT JOIN md_option AS o ON si.option_id=o.id
                        LEFT JOIN md_scheme AS s ON si.scheme_id=s.id
                        LEFT JOIN md_amc AS a ON s.amc_id=a.id
                        LEFT JOIN md_amc AS a1 ON n.amc_code=a1.amc_code
                        LEFT JOIN md_category AS c ON s.category_id=c.id
                        LEFT JOIN md_subcategory AS sc ON s.subcategory_id=sc.id
                        WHERE n.amc_flag="N" 
                        AND n.scheme_flag="N" 
                        AND si.plan_id='.$plan_type.'
                        AND '.$rawQuery.$modify_rawQuery1.'
                        ORDER BY n.product_code,n.nav_date DESC');
                    // return $my_data;

                    break;
                case 'W':
                    $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                    $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;  
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    
                    $my_data=DB::select('SELECT n.*,s.scheme_name,c.cat_name,sc.subcategory_name as subcat_name,a.amc_short_name as amc_name,a1.amc_short_name as amc_short_name,
                        p.plan_name as plan_name,o.opt_name as option_name
                        FROM td_nav_details AS n
                        LEFT JOIN md_scheme_isin AS si ON n.product_code=si.product_code
                        LEFT JOIN md_plan AS p ON si.plan_id=p.id
                        LEFT JOIN md_option AS o ON si.option_id=o.id
                        LEFT JOIN md_scheme AS s ON si.scheme_id=s.id
                        LEFT JOIN md_amc AS a ON s.amc_id=a.id
                        LEFT JOIN md_amc AS a1 ON n.amc_code=a1.amc_code
                        LEFT JOIN md_category AS c ON s.category_id=c.id
                        LEFT JOIN md_subcategory AS sc ON s.subcategory_id=sc.id
                        JOIN (
                                SELECT MAX(t.nav_date) AS mydate
                                FROM td_nav_details AS t
                                GROUP BY YEAR(t.nav_date), MONTH(t.nav_date), WEEK(t.nav_date)
                            ) AS x ON n.nav_date=x.mydate
                        WHERE n.amc_flag="N" 
                        AND n.scheme_flag="N" 
                        AND si.plan_id='.$plan_type.'
                        AND '.$rawQuery.$modify_rawQuery1.'
                        ORDER BY n.product_code,n.nav_date DESC');
                    // return $my_data;

                    break;
                case 'F':
                    $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                    $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;  
                    // return $to_date;
                    $date_array=[];

                    $startTime = strtotime( $from_date);
                    $endTime = strtotime( $to_date );
                    $mydiffer=14;
                    // Loop between timestamps, 24 hours at a time 86400
                    // for ( $i = $startTime; $i <= $endTime; $i = ($i + (86400 * $mydiffer)) ) {
                    //     $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                    //     array_push($date_array,$thisDate);
                    // }
                    for ( $i = $endTime; $i >= $startTime; $i = ($i - (86400 * $mydiffer)) ) {
                        $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                        array_push($date_array,$thisDate);
                    }
                    // return $date_array;
                    // return $from_date;
                    $queryString='r.date';
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    $row_name_string=  "'" .implode("','", $benchmark). "'";
                    $row_date_array_string=  "'" .implode("','", $date_array). "'";
                    // return $row_date_array_string;
                    
                    // DB::enableQueryLog();
                    // $my_data=DB::select('SELECT r.*,e.ex_name as exchange_name,b.benchmark as benchmark_name
                    //     FROM td_benchmark_scheme AS r
                    //         LEFT JOIN md_exchange AS e ON r.ex_id=e.id
                    //         LEFT JOIN md_benchmark AS b ON r.benchmark=b.id
                    //         where r.benchmark IN ('.$row_name_string.') AND r.date IN ('.$row_date_array_string.') order BY r.benchmark,r.date DESC');
                    // dd(DB::getQueryLog());
                    $my_data=[];
                    break;
                case 'M':
                    $f_date="01-".str_replace('/','-',explode("-",$date_range)[0]);
                    $t_date=date('d')."-".str_replace('/','-',explode("-",$date_range)[1]);
                    $from_date=Carbon::parse(str_replace(' ','',$f_date))->format('Y-m-d');
                    $to_date=Carbon::parse(str_replace(' ','',$t_date))->format('Y-m-d');
                    // return  $t_date;
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    $my_data=DB::select('SELECT n.*,s.scheme_name,c.cat_name,sc.subcategory_name as subcat_name,a.amc_short_name as amc_name,a1.amc_short_name as amc_short_name,
                        p.plan_name as plan_name,o.opt_name as option_name
                        FROM td_nav_details AS n
                        LEFT JOIN md_scheme_isin AS si ON n.product_code=si.product_code
                        LEFT JOIN md_plan AS p ON si.plan_id=p.id
                        LEFT JOIN md_option AS o ON si.option_id=o.id
                        LEFT JOIN md_scheme AS s ON si.scheme_id=s.id
                        LEFT JOIN md_amc AS a ON s.amc_id=a.id
                        LEFT JOIN md_amc AS a1 ON n.amc_code=a1.amc_code
                        LEFT JOIN md_category AS c ON s.category_id=c.id
                        LEFT JOIN md_subcategory AS sc ON s.subcategory_id=sc.id
                        JOIN (
                                SELECT MAX(t.nav_date) AS mydate
                                FROM td_nav_details AS t
                                GROUP BY YEAR(t.nav_date), MONTH(t.nav_date)
                            ) AS x ON n.nav_date=x.mydate
                        WHERE n.amc_flag="N" 
                        AND n.scheme_flag="N" 
                        AND si.plan_id='.$plan_type.'
                        AND '.$rawQuery.$modify_rawQuery1.'
                        ORDER BY n.product_code,n.nav_date DESC');
                    // return $my_data;
                    break;
                case 'H':
                    $f_date="01-01-".str_replace('/','-',explode("-",$date_range)[0]);
                    $t_date=date('d-m')."-".str_replace('/','-',explode("-",$date_range)[1]);
                    $from_date=Carbon::parse(str_replace(' ','',$f_date))->format('Y-m-d');
                    $to_date=Carbon::parse(str_replace(' ','',$t_date))->format('Y-m-d');
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    $row_name_string=  "'" .implode("','", $benchmark). "'";
                    $my_data=DB::select('SELECT n.*,s.scheme_name,c.cat_name,sc.subcategory_name as subcat_name,a.amc_short_name as amc_name,a1.amc_short_name as amc_short_name,
                        p.plan_name as plan_name,o.opt_name as option_name
                        FROM td_nav_details AS n
                        LEFT JOIN md_scheme_isin AS si ON n.product_code=si.product_code
                        LEFT JOIN md_plan AS p ON si.plan_id=p.id
                        LEFT JOIN md_option AS o ON si.option_id=o.id
                        LEFT JOIN md_scheme AS s ON si.scheme_id=s.id
                        LEFT JOIN md_amc AS a ON s.amc_id=a.id
                        LEFT JOIN md_amc AS a1 ON n.amc_code=a1.amc_code
                        LEFT JOIN md_category AS c ON s.category_id=c.id
                        LEFT JOIN md_subcategory AS sc ON s.subcategory_id=sc.id
                        JOIN (
                                SELECT MAX(t.nav_date) AS mydate
                                FROM td_nav_details AS t
                                GROUP BY YEAR(t.nav_date), CEIL(MONTH(t.nav_date) / 6)
                            ) AS x ON n.nav_date=x.mydate
                        WHERE n.amc_flag="N" 
                        AND n.scheme_flag="N" 
                        AND si.plan_id='.$plan_type.'
                        AND '.$rawQuery.$modify_rawQuery1.'
                        ORDER BY n.product_code,n.nav_date DESC');
                    // return $my_data;
                    break;
                case 'Y':
                    $f_date="01-01-".str_replace('/','-',explode("-",$date_range)[0]);
                    $t_date=date('d-m')."-".str_replace('/','-',explode("-",$date_range)[1]);
                    $from_date=Carbon::parse(str_replace(' ','',$f_date))->format('Y-m-d');
                    $to_date=Carbon::parse(str_replace(' ','',$t_date))->format('Y-m-d');
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    $my_data=DB::select('SELECT n.*,s.scheme_name,c.cat_name,sc.subcategory_name as subcat_name,a.amc_short_name as amc_name,a1.amc_short_name as amc_short_name,
                        p.plan_name as plan_name,o.opt_name as option_name
                        FROM td_nav_details AS n
                        LEFT JOIN md_scheme_isin AS si ON n.product_code=si.product_code
                        LEFT JOIN md_plan AS p ON si.plan_id=p.id
                        LEFT JOIN md_option AS o ON si.option_id=o.id
                        LEFT JOIN md_scheme AS s ON si.scheme_id=s.id
                        LEFT JOIN md_amc AS a ON s.amc_id=a.id
                        LEFT JOIN md_amc AS a1 ON n.amc_code=a1.amc_code
                        LEFT JOIN md_category AS c ON s.category_id=c.id
                        LEFT JOIN md_subcategory AS sc ON s.subcategory_id=sc.id
                        JOIN (
                                SELECT MAX(t.nav_date) AS mydate
                                FROM td_nav_details AS t
                                GROUP BY YEAR(t.nav_date)
                            ) AS x ON n.nav_date=x.mydate
                        WHERE n.amc_flag="N" 
                        AND n.scheme_flag="N" 
                        AND si.plan_id='.$plan_type.'
                        AND '.$rawQuery.$modify_rawQuery1.'
                        ORDER BY n.product_code,n.nav_date DESC');
                    // return $my_data;
                    break;
                default:
                    break;
            }

            $data=[];
            foreach ($my_data as $key => $value) {
                // return $value;
                // return $key;
                $nav_price=$value->nav;
                $old_nav_price=0;
                $change_nav=0;
                $change_percentage_format=0.00;
                if (isset($my_data[$key+1]->nav) && $my_data[$key+1]->nav && isset($my_data[$key+1]->product_code) &&  $my_data[$key+1]->product_code==$value->product_code) {
                    $old_nav_price=$my_data[$key+1]->nav;
                    $change_nav=$nav_price-$old_nav_price;
                    $change_percentage=(($change_nav/$old_nav_price)*100);
                    $change_percentage_format=number_format((float)$change_percentage, 2, '.', '');
                    // $change_percentage_format=number_format((float)round($change_percentage, 0, PHP_ROUND_HALF_UP), 2, '.', '');
                }
                $value->change_nav=number_format((float)$change_nav, 2, '.', '');
                $value->change_percentage=$change_percentage_format;
                array_push($data,$value);
            }

            // if ($periods && !empty($amc_id) && !empty($scheme_id)) {
                
            //     DB::enableQueryLog();
                
            //     $data=NAVDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_nav_details.product_code')
            //         ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
            //         ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
            //         ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            //         ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
            //         ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
            //         ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
            //         ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_nav_details.amc_code')
            //         ->select('td_nav_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
            //         'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name')
            //         ->where('td_nav_details.amc_flag','N')
            //         ->where('td_nav_details.scheme_flag','N')
            //         ->where('md_scheme_isin.plan_id',$plan_type)
            //         ->whereRaw($rawQuery)
            //         ->orderBy('td_nav_details.nav_date','desc')
            //         ->get();
            // }else if ($periods) {
            //     DB::enableQueryLog();
            //     $data=NAVDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_nav_details.product_code')
            //         ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
            //         ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
            //         ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            //         ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
            //         ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
            //         ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
            //         ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_nav_details.amc_code')
            //         ->select('td_nav_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
            //         'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name')
            //         ->where('td_nav_details.amc_flag','N')
            //         ->where('td_nav_details.scheme_flag','N')
            //         ->where('md_scheme_isin.plan_id',$plan_type)
            //         ->whereRaw($rawQuery)
            //         ->orderBy('td_nav_details.nav_date','desc')
            //         // ->take(100)
            //         ->get();
                    
            //     dd(DB::getQueryLog());
                
            // }else {
            //     $data=NAVDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_nav_details.product_code')
            //         ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
            //         ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
            //         ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            //         ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
            //         ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
            //         ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
            //         ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_nav_details.amc_code')
            //         ->select('td_nav_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
            //         'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name')
            //         ->where('td_nav_details.amc_flag','N')
            //         ->where('td_nav_details.scheme_flag','N')
            //         ->where('md_scheme_isin.plan_id',$plan_type)
            //         ->orderBy('td_nav_details.nav_date','desc')
            //         ->take(100)
            //         ->get();
            // }
            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
