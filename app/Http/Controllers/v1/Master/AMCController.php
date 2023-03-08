<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\AMC;
use Validator;
use Excel;
use App\Imports\AMCImport;

class AMCController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {  
            $rnt_id=$request->rnt_id;
            $amc_id=$request->amc_id;
            $gstin=$request->gstin;
            $contact_per=$request->contact_per;
            $contact_per_mobile=$request->contact_per_mobile;
            $contact_per_email=$request->contact_per_email;
            $contact_per=$request->contact_per;
            $contact_per_mobile=$request->contact_per_mobile;
            $contact_per_email=$request->contact_per_email;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($amc_id!='' && $rnt_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->orderBy('md_amc.updated_at','DESC')
                    ->paginate($paginate);      
            }elseif ($rnt_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->paginate($paginate);      
            }elseif ($amc_id!='' && $rnt_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.amc_id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->paginate($paginate);      
            }elseif ($amc_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.amc_id',$amc_id)
                    ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->orderBy('md_amc.updated_at','DESC')
                    ->paginate($paginate);    
            } elseif ($amc_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    // ->where('md_amc.id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->paginate($paginate);      
            }elseif ($gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                ->where('md_amc.gstin','like', '%' . $gstin . '%')
                ->orderBy('md_amc.updated_at','DESC')
                ->paginate($paginate);    
            } elseif ($rnt_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                ->where('md_amc.id',$amc_id)
                // ->where('md_amc.rnt_id',$rnt_id)
                ->orderBy('md_amc.updated_at','DESC')
                ->paginate($paginate);      
            }else {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    // ->where('md_amc.id',$amc_id)
                    // ->orWhere('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->paginate($paginate);    
            }  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function export(Request $request)
    {
        try {
            $rnt_id=$request->rnt_id;
            $amc_id=$request->amc_id;
            $gstin=$request->gstin;
            if ($amc_id!='' && $rnt_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();      
            }elseif ($rnt_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();      
            }elseif ($amc_id!='' && $rnt_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.amc_id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();      
            }elseif ($amc_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.amc_id',$amc_id)
                    ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();    
            } elseif ($amc_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    // ->where('md_amc.id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();      
            }elseif ($gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                ->where('md_amc.gstin','like', '%' . $gstin . '%')
                ->orderBy('md_amc.updated_at','DESC')
                ->get();    
            } elseif ($rnt_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                ->where('md_amc.id',$amc_id)
                ->orderBy('md_amc.updated_at','DESC')
                ->get();      
            }else {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    // ->where('md_amc.id',$amc_id)
                    // ->orWhere('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();    
            }  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $product_id=$request->product_id;
            $rnt_id=$request->rnt_id;
            $id=$request->id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=AMC::where('amc_name','like', '%' . $search . '%')->get();      
            } elseif ($product_id!='') {
                $data=AMC::where('product_id',$product_id)->get();      
            } elseif ($rnt_id!='') {
                $data=AMC::where('rnt_id',$rnt_id)->paginate($paginate);      
            } elseif ($id!='') {
                $data=AMC::where('id',$id)->get();  
            } elseif ($paginate!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_amc.updated_at','DESC')
                    ->paginate($paginate);      
                // $data=AMC::orderBy('updated_at','DESC')->paginate($paginate);      
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
                $data->gstin=$request->gstin;
                $data->website=$request->website;
                $data->head_ofc_addr=$request->head_ofc_addr;
                $data->head_ofc_contact_per=$request->head_ofc_contact_per;
                $data->head_contact_per_mob=$request->head_contact_per_mob;
                $data->head_contact_per_email=$request->head_contact_per_email;
                $data->local_ofc_addr=$request->local_ofc_addr;
                $data->local_ofc_contact_per=$request->local_ofc_contact_per;
                $data->local_contact_per_mob=$request->local_contact_per_mob;
                $data->local_contact_per_email=$request->local_contact_per_email;
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
                $data->amc_short_name=$request->amc_short_name;
                $data->login_url=$request->login_url;
                $data->login_id=$request->login_id;
                $data->login_pass=$request->login_pass;
                $data->security_qus_ans=$request->security_qus_ans;
                $data->cus_care_whatsapp_no=$request->cus_care_whatsapp_no;
                $data->save();
            }else{
                $data=AMC::create(array(
                    'rnt_id'=>$request->rnt_id,
                    'product_id'=>$request->product_id,
                    'amc_name'=>$request->amc_name,
                    'gstin'=>$request->gstin,
                    'website'=>$request->website,
                    'head_ofc_addr'=>$request->head_ofc_addr,
                    'head_ofc_contact_per'=>$request->head_ofc_contact_per,
                    'head_contact_per_mob'=>$request->head_contact_per_mob,
                    'head_contact_per_email'=>$request->head_contact_per_email,
                    'local_ofc_addr'=>$request->local_ofc_addr,
                    'local_ofc_contact_per'=>$request->local_ofc_contact_per,
                    'local_contact_per_mob'=>$request->local_contact_per_mob,
                    'local_contact_per_email'=>$request->local_contact_per_email,
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
                    'amc_short_name'=>$request->amc_short_name,
                    'login_url'=>$request->login_url,
                    'login_id'=>$request->login_id,
                    'login_pass'=>$request->login_pass,
                    'security_qus_ans'=>$request->security_qus_ans,
                    'cus_care_whatsapp_no'=>$request->cus_care_whatsapp_no,
                    'delete_flag'=>'N',
                    // 'created_by'=>'',
                ));      
            }    
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function import(Request $request)
    {
        try {
            // return $request;
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            return $data ;
            // return $data[0][0];
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                Excel::import(new AMCImport,$request->file);
                // Excel::import(new AMCImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
  
}