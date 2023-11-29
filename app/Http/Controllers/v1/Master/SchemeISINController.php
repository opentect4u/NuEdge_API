<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    Scheme,
    MutualFund,
    FormReceived,
    AMC,
    Category,
    SubCategory,
    SchemeOtherForm,
    SchemeISIN,
    Plan,
    Option,
    MutualFundTransaction,
    SipStpTransaction
};
use Validator;
use Excel;
use App\Imports\SchemeImport;

class SchemeISINController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $order=$request->order;
            $field=$request->field;

            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            $search_scheme_id=$request->search_scheme_id;
            $plan_id=json_decode($request->plan_id);
            $opt_id=json_decode($request->opt_id);
            // return $request;
            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                if ($amc_id || $cat_id || $sub_cat_id || $scheme_id || $search_scheme_id || $plan_id || $opt_id) {
                    $rawQuery='';
                    if (!empty($amc_id)) {
                        $amc_id_string= implode(',', $amc_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_scheme.amc_id IN (".$amc_id_string.")";
                        }else {
                            $rawQuery.=" md_scheme.amc_id IN (".$amc_id_string.")";
                        }
                    }
                    if (!empty($cat_id)) {
                        $cat_id_string= implode(',', $cat_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_scheme.category_id IN (".$cat_id_string.")";
                        }else {
                            $rawQuery.=" md_scheme.category_id IN (".$cat_id_string.")";
                        }
                    }
                    if (!empty($sub_cat_id)) {
                        $sub_cat_id_string= implode(',', $sub_cat_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_scheme.subcategory_id IN (".$sub_cat_id_string.")";
                        }else {
                            $rawQuery.=" md_scheme.subcategory_id IN (".$sub_cat_id_string.")";
                        }
                    }
                    if ($search_scheme_id) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_scheme_isin.scheme_id=".$search_scheme_id;
                        }else {
                            $rawQuery.=" md_scheme_isin.scheme_id=".$search_scheme_id;
                        }
                    }
                    if (!empty($plan_id)) {
                        $plan_id_string= implode(',', $plan_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_scheme_isin.plan_id IN (".$plan_id_string.")";
                        }else {
                            $rawQuery.=" md_scheme_isin.plan_id IN (".$plan_id_string.")";
                        }
                    }
                    if (!empty($opt_id)) {
                        $opt_id_string= implode(',', $opt_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_scheme_isin.option_id IN (".$opt_id_string.")";
                        }else {
                            $rawQuery.=" md_scheme_isin.option_id IN (".$opt_id_string.")";
                        }
                    }
                    $data=SchemeISIN::leftjoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                        ->leftjoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->leftjoin('md_category','md_category.id','=','md_scheme.category_id')
                        ->leftjoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->leftjoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                        ->leftjoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                        ->select('md_scheme_isin.*','md_scheme.scheme_name as scheme_name','md_amc.amc_short_name as amc_short_name','md_amc.id as amc_id','md_category.cat_name as cat_name',
                        'md_subcategory.subcategory_name as subcategory_name','md_plan.plan_name as plan_name','md_option.opt_name as opt_name')
                        ->where('md_scheme_isin.delete_flag','N')
                        ->whereRaw($rawQuery)
                        ->paginate($paginate); 
                }else {
                    $data=SchemeISIN::leftjoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                        ->leftjoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->leftjoin('md_category','md_category.id','=','md_scheme.category_id')
                        ->leftjoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->leftjoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                        ->leftjoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                        ->select('md_scheme_isin.*','md_scheme.scheme_name as scheme_name','md_amc.amc_short_name as amc_short_name','md_amc.id as amc_id','md_category.cat_name as cat_name',
                        'md_subcategory.subcategory_name as subcategory_name','md_plan.plan_name as plan_name','md_option.opt_name as opt_name')
                        ->where('md_scheme_isin.delete_flag','N')
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate); 
                }
            }elseif ($amc_id || $cat_id || $sub_cat_id || $scheme_id || $search_scheme_id || $plan_id || $opt_id) {
                $rawQuery='';
                if (!empty($amc_id)) {
                    $amc_id_string= implode(',', $amc_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_scheme.amc_id IN (".$amc_id_string.")";
                    }else {
                        $rawQuery.=" md_scheme.amc_id IN (".$amc_id_string.")";
                    }
                }
                if (!empty($cat_id)) {
                    $cat_id_string= implode(',', $cat_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_scheme.category_id IN (".$cat_id_string.")";
                    }else {
                        $rawQuery.=" md_scheme.category_id IN (".$cat_id_string.")";
                    }
                }
                if (!empty($sub_cat_id)) {
                    $sub_cat_id_string= implode(',', $sub_cat_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_scheme.subcategory_id IN (".$sub_cat_id_string.")";
                    }else {
                        $rawQuery.=" md_scheme.subcategory_id IN (".$sub_cat_id_string.")";
                    }
                }
                if ($search_scheme_id) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_scheme_isin.scheme_id=".$search_scheme_id;
                    }else {
                        $rawQuery.=" md_scheme_isin.scheme_id=".$search_scheme_id;
                    }
                }
                if (!empty($plan_id)) {
                    $plan_id_string= implode(',', $plan_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_scheme_isin.plan_id IN (".$plan_id_string.")";
                    }else {
                        $rawQuery.=" md_scheme_isin.plan_id IN (".$plan_id_string.")";
                    }
                }
                if (!empty($opt_id)) {
                    $opt_id_string= implode(',', $opt_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_scheme_isin.option_id IN (".$opt_id_string.")";
                    }else {
                        $rawQuery.=" md_scheme_isin.option_id IN (".$opt_id_string.")";
                    }
                }
                // return $rawQuery;
                $data=SchemeISIN::leftjoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftjoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftjoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftjoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftjoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftjoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->select('md_scheme_isin.*','md_scheme.scheme_name as scheme_name','md_amc.amc_short_name as amc_short_name','md_amc.id as amc_id','md_category.cat_name as cat_name',
                    'md_subcategory.subcategory_name as subcategory_name','md_plan.plan_name as plan_name','md_option.opt_name as opt_name')
                    ->where('md_scheme_isin.delete_flag','N')
                    ->whereRaw($rawQuery)
                    ->paginate($paginate); 
            }else {
                $data=SchemeISIN::leftjoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftjoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftjoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftjoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftjoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftjoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->select('md_scheme_isin.*','md_scheme.scheme_name as scheme_name','md_amc.amc_short_name as amc_short_name','md_amc.id as amc_id','md_category.cat_name as cat_name',
                    'md_subcategory.subcategory_name as subcategory_name','md_plan.plan_name as plan_name','md_option.opt_name as opt_name')
                    ->where('md_scheme_isin.delete_flag','N')
                    ->orderBy('md_scheme_isin.created_at','desc')
                    ->paginate($paginate); 
            }

        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $paginate=$request->paginate;
            // return $request;
            
           
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function index(Request $request)
    {
        try {  
            $scheme_id=$request->scheme_id;

            $data=SchemeISIN::leftjoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->select('md_scheme_isin.*','md_scheme.scheme_name as scheme_name')
                ->where('md_scheme_isin.delete_flag','N')
                ->where('md_scheme_isin.scheme_id',$scheme_id)
                ->get(); 

        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'isin_dtls' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $isin_dtls=json_decode($request->isin_dtls);
            // return $isin_dtls;

            // $request->swp_date
            // $request->stp_date
            // return $request->sip_date;
            // return json_decode($request->sip_date);
            // 'sip_date'=>json_encode($request->sip_date),
            
                // return $isin_dtls;
            $data=[];  
            foreach ($isin_dtls as $key => $value) {
                // return $value->row_id;
                if ($value->row_id==0) {
                    $dt=SchemeISIN::create(array(
                        'scheme_id'=>$value->scheme_id,
                        'plan_id'=>$value->plan_id,
                        'option_id'=>$value->option_id,
                        'isin_no'=>$value->isin_no,
                        'product_code'=>$value->product_code,
                        'created_by'=>Helper::modifyUser($request->user()),
                    ));
                }else {
                    $dt=SchemeISIN::find($value->row_id);
                    $dt->scheme_id=$value->scheme_id;
                    $dt->plan_id=$value->plan_id;
                    $dt->option_id=$value->option_id;
                    $dt->isin_no=$value->isin_no;
                    $dt->product_code=$value->product_code;
                    $dt->updated_by=Helper::modifyUser($request->user());
                    $dt->save();
                }  
                $sc_data=SchemeISIN::leftjoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftjoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftjoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftjoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftjoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftjoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->select('md_scheme_isin.*','md_scheme.scheme_name as scheme_name','md_amc.amc_short_name as amc_short_name','md_amc.id as amc_id','md_category.cat_name as cat_name',
                    'md_subcategory.subcategory_name as subcategory_name','md_plan.plan_name as plan_name','md_option.opt_name as opt_name')
                    ->where('md_scheme_isin.delete_flag','N')
                    ->where('md_scheme_isin.id',$dt->id)
                    ->first(); 
                array_push($data,$sc_data);

                // Start insert and update scheme ISIN, update table td_mutual_fund_trans 

                $trans_up_data=MutualFundTransaction::leftjoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->select('td_mutual_fund_trans.*','md_amc.amc_short_name as amc_short_name','md_amc.rnt_id as rnt_id')
                    ->where('td_mutual_fund_trans.product_code',$value->product_code)
                    ->get();
                // return $trans_up_data;
                if (count($trans_up_data)>0) {
                    foreach ($trans_up_data as $key => $update_data) {
                        if ($update_data->rnt_id==1) {
                            $rnt_up_data=MutualFundTransaction::find($update_data->id);
                            $rnt_up_data->scheme_flag='N';
                            $rnt_up_data->plan_option_flag='N';
                            // $rnt_up_data->updated_by=Helper::modifyUser($request->user());
                            $rnt_up_data->save();
                        }elseif ($update_data->rnt_id==2) {
                            $rnt_up_data2=MutualFundTransaction::find($update_data->id);
                            $rnt_up_data2->scheme_flag='N';
                            // $rnt_up_data2->updated_by=Helper::modifyUser($request->user());
                            $rnt_up_data2->save();
                        }
                    }
                }
                $trans_up_data2=MutualFundTransaction::leftjoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->select('td_mutual_fund_trans.*','md_amc.amc_short_name as amc_short_name','md_amc.rnt_id as rnt_id')
                    ->where('td_mutual_fund_trans.product_code',$value->product_code)
                    ->where('td_mutual_fund_trans.isin_no',$value->isin_no)
                    ->get();
                // return $trans_up_data2;
                foreach ($trans_up_data2 as $key => $update_data2) {
                    if ($update_data2->rnt_id==2) {
                        $rnt2_up_data2=MutualFundTransaction::find($update_data2->id);
                        $rnt2_up_data2->plan_option_flag='N';
                        // $rnt2_up_data2->updated_by=Helper::modifyUser($request->user());
                        $rnt2_up_data2->save();
                    }
                }

                SipStpTransaction::where('product_code',$value->product_code)->update([
                        'scheme_flag'=>'N',
                    ]);
                // End insert and update scheme ISIN, update table td_mutual_fund_trans 
            }
          
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            
            $data=SchemeISIN::find($id);
            $data->delete_flag='Y';
            $data->deleted_date=date('Y-m-d H:i:s');
            $data->deleted_by=Helper::modifyUser($request->user());
            $data->save();
            
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DELETE_FAIL_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function import(Request $request)
    {
        try {
            // return $request;
            $scheme_type=$request->scheme_type;
            // $path = $request->file('file')->getRealPath();
            // $data = array_map('str_getcsv', file($path));
            // return $data;
            $datas = Excel::toArray([],  $request->file('file'));
            $data=$datas[0];
            // return $datas[0];

            foreach ($data as $key => $value) {
                // return $value;
                if ($key==0) {
                    if (str_replace(" ","_",$value[0])!="Scheme_Name" && $value[1]!="Option" && $value[2]!="Plan" && $value[3]!="ISIN" && str_replace(" ","_",$value[4])!="Product_Code") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    $scheme_id=Scheme::where('scheme_name',$value[0])->value('id');
                    $option_id=Option::where('opt_name',$value[1])->value('id');
                    $plan_id=Plan::where('plan_name',$value[2])->value('id');

                    $is_has=SchemeISIN::where('scheme_id',$scheme_id)
                        ->where('plan_id',$plan_id)->where('option_id',$option_id)
                        ->get();
                    if (count($is_has) > 0) {
                        $data=SchemeISIN::find($is_has[0]->id);
                        $data->scheme_id=$scheme_id;
                        $data->plan_id=$plan_id;
                        $data->option_id=$option_id;
                        $data->isin_no=$value[3];
                        $data->product_code=$value[4];
                        $data->save();
                    }else {
                        SchemeISIN::create(array(
                            'scheme_id'=>$scheme_id,
                            'plan_id'=>$plan_id,
                            'option_id'=>$option_id,
                            'isin_no'=>$value[3],
                            'product_code'=>$value[4],
                        ));
                    }
                }
            }

            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                // Excel::import(new SchemeImport,$request->file);
                // Excel::import(new SchemeImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        } catch (\Throwable $th) {
            throw $th;
            //return $value;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }

    


}
