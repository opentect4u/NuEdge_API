<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Scheme,MutualFund,FormReceived,AMC,Category,SubCategory,SchemeOtherForm,SchemeISIN};
use Validator;
use Excel;
use App\Imports\SchemeImport;

class SchemeISINController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $scheme_name=$request->scheme_name;
            $amc_name=$request->amc_name;
            $cat_id=$request->cat_id;
            $subcat_id=$request->subcat_id;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $scheme_type=$request->scheme_type;
            // return $request;
            if ($paginate=='A') {
                $paginate=999999999;
            }

            

        } catch (\Throwable $th) {
            // throw $th;
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
                foreach ($isin_dtls as $key => $value) {
                    // return $value->row_id;
                    if ($value->row_id==0) {
                        SchemeISIN::create(array(
                            'scheme_id'=>$value->scheme_id,
                            'plan_id'=>$value->plan_id,
                            'option_id'=>$value->option_id,
                            'isin_no'=>$value->isin_no,
                            // 'created_by'=>'',
                        ));
                    }else {
                        $data=SchemeISIN::find($value->row_id);
                        $data->scheme_id=$value->scheme_id;
                        $data->plan_id=$value->plan_id;
                        $data->option_id=$value->option_id;
                        $data->isin_no=$value->isin_no;
                        $data->save();
                    }    
                }
          
            $data=[];  
        } catch (\Throwable $th) {
            //throw $th;
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
            $data->deleted_by=1;
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
            $amc_id=$request->amc_id;
            $category_id=$request->category_id;
            $subcategory_id=$request->subcategory_id;
            $product_id=$request->product_id;
            // $path = $request->file('file')->getRealPath();
            // $data = array_map('str_getcsv', file($path));
            // return $data;
            $datas = Excel::toArray([],  $request->file('file'));
            // return $datas[0];
            $data=$datas[0];

            if ($scheme_type=='O') {
                
            }else {
                // return 'hii';
               
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
            // throw $th;
            //return $value;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }

    


}
