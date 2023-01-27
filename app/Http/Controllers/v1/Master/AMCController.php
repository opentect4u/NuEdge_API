<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\AMC;
use Validator;

class AMCController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $product_id=$request->product_id;
            if ($search!='') {
                $data=AMC::where('amc_name','like', '%' . $search . '%')->get();      
            }elseif ($product_id!='') {
                $data=AMC::where('product_id',$product_id)->get();      
            } else {
                $data=AMC::orderBy('updated_at','DESC')->get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'rnt_id' =>'required',
            'product_id' =>'required',
            'amc_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=AMC::find($request->id);
                $data->rnt_id=$request->rnt_id;
                $data->product_id=$request->product_id;
                $data->amc_name=$request->amc_name;
                $data->website=$request->website;
                $data->ofc_addr=$request->ofc_addr;
                $data->cus_care_no=$request->cus_care_no;
                $data->cus_care_email=$request->cus_care_email;
                $data->l1_name=$request->l1_name;
                $data->l1_contact_no=$request->l1_contact_no;
                $data->l1_email=$request->l1_email;
                $data->l2_name=$request->l2_name;
                $data->l2_contact_no=$request->l2_contact_no;
                $data->l2_email=$request->l2_email;
                $data->l3_name=$request->l3_name;
                $data->l3_contact_no=$request->l3_contact_no;
                $data->l3_email=$request->l3_email;
                $data->l4_name=$request->l4_name;
                $data->l4_contact_no=$request->l4_contact_no;
                $data->l4_email=$request->l4_email;
                $data->l5_name=$request->l5_name;
                $data->l5_contact_no=$request->l5_contact_no;
                $data->l5_email=$request->l5_email;
                $data->l6_name=$request->l6_name;
                $data->l6_contact_no=$request->l6_contact_no;
                $data->l6_email=$request->l6_email;
                $data->l7_name=$request->l7_name;
                $data->l7_contact_no=$request->l7_contact_no;
                $data->l7_email=$request->l7_email;
                $data->save();
            }else{
                $data=AMC::create(array(
                    'rnt_id'=>$request->rnt_id,
                    'product_id'=>$request->product_id,
                    'amc_name'=>$request->amc_name,
                    'website'=>$request->website,
                    'ofc_addr'=>$request->ofc_addr,
                    'cus_care_no'=>$request->cus_care_no,
                    'cus_care_email'=>$request->cus_care_email,
                    'l1_name'=>$request->l1_name,
                    'l1_contact_no'=>$request->l1_contact_no,
                    'l1_email'=>$request->l1_email,
                    'l2_name'=>$request->l2_name,
                    'l2_contact_no'=>$request->l2_contact_no,
                    'l2_email'=>$request->l2_email,
                    'l3_name'=>$request->l3_name,
                    'l3_contact_no'=>$request->l3_contact_no,
                    'l3_email'=>$request->l3_email,
                    'l4_name'=>$request->l4_name,
                    'l4_contact_no'=>$request->l4_contact_no,
                    'l4_email'=>$request->l4_email,
                    'l5_name'=>$request->l5_name,
                    'l5_contact_no'=>$request->l5_contact_no,
                    'l5_email'=>$request->l5_email,
                    'l6_name'=>$request->l6_name,
                    'l6_contact_no'=>$request->l6_contact_no,
                    'l6_email'=>$request->l6_email,
                    'l7_name'=>$request->l7_name,
                    'l7_contact_no'=>$request->l7_contact_no,
                    'l7_email'=>$request->l7_email,
                    // 'created_by'=>'',
                ));      
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

  
}