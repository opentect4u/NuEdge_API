<?php

namespace App\Http\Controllers\v1\FDMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{FDCompany,InsProduct};
use Validator;

class CompanyController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            // return 'hii';
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            
            $contact_person=$request->contact_person;
            $comp_type=json_decode($request->comp_type);
            $comp_name=json_decode($request->comp_name);
            
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                if ($column_name='comp_type') {
                    $data=$data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->orderBy('md_fd_type_of_company.comp_type',$sort_by)
                    ->paginate($paginate); 
                }else {
                    $data=$data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->orderBy('md_fd_company.'.$column_name,$sort_by)
                    ->paginate($paginate); 
                }
            }elseif ($contact_person && !empty($comp_type) && !empty($comp_name)) {
                $setarray=[];
                foreach ($comp_name as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                $arr_comp_type=[];
                foreach ($comp_type as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.id',$setarray)
                    ->whereIn('md_fd_company.comp_type_id',$arr_comp_type)
                    ->where('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif ($contact_person && !empty($comp_type)) {
                $arr_comp_type=[];
                foreach ($comp_type as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.comp_type_id',$arr_comp_type)
                    ->where('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif (!empty($comp_type) && !empty($comp_name)) {
                $setarray=[];
                foreach ($comp_name as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                $arr_comp_type=[];
                foreach ($comp_type as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.id',$setarray)
                    ->whereIn('md_fd_company.comp_type_id',$arr_comp_type)
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif ($contact_person && !empty($comp_name)) {
                $setarray=[];
                foreach ($comp_name as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.id',$setarray)
                    ->where('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->paginate($paginate);  
            } elseif ($contact_person) {
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->where('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif (!empty($comp_type)) {
                $arr_comp_type=[];
                foreach ($comp_type as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.comp_type_id',$arr_comp_type)
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif (!empty($comp_name)) {
                $setarray=[];
                foreach ($comp_name as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                // return $setarray;
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.id',$setarray)
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->paginate($paginate);  
            } else {
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->orderBy('md_fd_company.updated_at','DESC')
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
            // return 'hii';
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            
            $contact_person=$request->contact_person;
            $comp_type=json_decode($request->comp_type);
            $comp_name=json_decode($request->comp_name);
           
            if ($sort_by && $column_name) {
                if ($column_name='comp_type') {
                    $data=$data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->orderBy('md_fd_type_of_company.comp_type',$sort_by)
                    ->get(); 
                }else {
                    $data=$data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->orderBy('md_fd_company.'.$column_name,$sort_by)
                    ->get(); 
                }
            }elseif ($contact_person && !empty($comp_type) && !empty($comp_name)) {
                $setarray=[];
                foreach ($comp_name as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                $arr_comp_type=[];
                foreach ($comp_type as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.id',$setarray)
                    ->whereIn('md_fd_company.comp_type_id',$arr_comp_type)
                    ->where('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->get();  
            }elseif ($contact_person && !empty($comp_type)) {
                $arr_comp_type=[];
                foreach ($comp_type as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.comp_type_id',$arr_comp_type)
                    ->where('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->get();  
            }elseif (!empty($comp_type) && !empty($comp_name)) {
                $setarray=[];
                foreach ($comp_name as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                $arr_comp_type=[];
                foreach ($comp_type as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.id',$setarray)
                    ->whereIn('md_fd_company.comp_type_id',$arr_comp_type)
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->get();  
            }elseif ($contact_person && !empty($comp_name)) {
                $setarray=[];
                foreach ($comp_name as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.id',$setarray)
                    ->where('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->get();  
            } elseif ($contact_person) {
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->where('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_fd_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->get();  
            }elseif (!empty($comp_type)) {
                $arr_comp_type=[];
                foreach ($comp_type as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.comp_type_id',$arr_comp_type)
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->get();  
            }elseif (!empty($comp_name)) {
                $setarray=[];
                foreach ($comp_name as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                // return $setarray;
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->whereIn('md_fd_company.id',$setarray)
                    ->orderBy('md_fd_company.updated_at','DESC')
                    ->get();  
            } else {
                $data=FDCompany::leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_company.comp_type_id')
                    ->select('md_fd_company.*','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_company.delete_flag','N')
                    ->orderBy('md_fd_company.updated_at','DESC')
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
            $id=$request->id;
            $comp_type_id=$request->comp_type_id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=FDCompany::where('type','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=FDCompany::where('id',$id)->get();      
            }else if ($comp_type_id!='') {
                $data=FDCompany::where('comp_type_id',$comp_type_id)->get();      
            }elseif ($paginate!='') {
                $data=FDCompany::where('delete_flag','N')->paginate($paginate);      
            } else {
                $data=FDCompany::where('delete_flag','N')->get();      
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
            'comp_type_id' =>'required',
            'comp_short_name' =>'required',
            'comp_full_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=FDCompany::find($request->id);
                $data->comp_type_id=$request->comp_type_id;
                $data->comp_short_name=$request->comp_short_name;
                $data->comp_full_name=$request->comp_full_name;
                $data->login_url=$request->login_url;
                $data->login_id=$request->login_id;
                $data->login_pass=$request->login_pass;
                $data->security_qus_ans=$request->sec_qus_ans;
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
                $data->cus_care_whatsapp_no=$request->cus_care_whatsapp_no;
                $data->distributor_care_no=$request->distributor_care_no;
                $data->distributor_care_email=$request->distributor_care_email;
                $data->l1_name=$request->l1_name;
                $data->l1_contact_no=$request->l1_contact_no;
                $data->l1_email=$request->l1_email;
                $data->l2_name=$request->l2_name;
                $data->l2_contact_no=$request->l2_contact_no;
                $data->l2_email=$request->l2_email;
                $data->l3_name=$request->l3_name;
                $data->l3_contact_no=$request->l3_contact_no;
                $data->l3_contact_no=$request->l3_contact_no;
                $data->l4_name=$request->l4_name;
                $data->l4_contact_no=$request->l4_contact_no;
                $data->l4_email=$request->l4_email;
                $data->l5_name=$request->l5_name;
                $data->l5_contact_no=$request->l5_contact_no;
                $data->l5_email=$request->l5_email;
                $data->l6_name=$request->l6_name;
                $data->l6_contact_no=$request->l6_contact_no;
                $data->l6_email=$request->l6_email;
                $data->save();
            }else{
                $is_has=FDCompany::where('comp_short_name',$request->comp_short_name)->where('comp_full_name',$request->comp_full_name)->get();
                // return $is_has;
                if (count($is_has)>0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=FDCompany::create(array(
                        'comp_type_id'=>$request->comp_type_id,
                        'comp_short_name'=>$request->comp_short_name,
                        'comp_full_name'=>$request->comp_full_name,
                        'login_url'=>$request->login_url,
                        'login_id'=>$request->login_id,
                        'login_pass'=>$request->login_pass,
                        'security_qus_ans'=>$request->sec_qus_ans,
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
                        'cus_care_whatsapp_no'=>$request->cus_care_whatsapp_no,
                        'distributor_care_no'=>$request->distributor_care_no,
                        'distributor_care_email'=>$request->distributor_care_email,
                        'l1_name'=>$request->l1_name,
                        'l1_contact_no'=>$request->l1_contact_no,
                        'l1_email'=>$request->l1_email,
                        'l2_name'=>$request->l2_name,
                        'l2_contact_no'=>$request->l2_contact_no,
                        'l2_email'=>$request->l2_email,
                        'l3_name'=>$request->l3_name,
                        'l3_contact_no'=>$request->l3_contact_no,
                        'l3_contact_no'=>$request->l3_contact_no,
                        'l4_name'=>$request->l4_name,
                        'l4_contact_no'=>$request->l4_contact_no,
                        'l4_email'=>$request->l4_email,
                        'l5_name'=>$request->l5_name,
                        'l5_contact_no'=>$request->l5_contact_no,
                        'l5_email'=>$request->l5_email,
                        'l6_name'=>$request->l6_name,
                        'l6_contact_no'=>$request->l6_contact_no,
                        'l6_email'=>$request->l6_email,
                        // 'created_by'=>'',
                    ));
                }      
            }    
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
            $is_has=InsProduct::where('company_id',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=FDCompany::find($id);
                $data->delete_flag='Y';
                $data->deleted_date=date('Y-m-d H:i:s');
                $data->deleted_by=1;
                $data->save();
            }
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
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            // return $data;

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if (str_replace(" ","_",$value[0])!="Company_Full_Name" && str_replace(" ","_",$value[1])!="Company_Short_Name" && $value[0]!="Website") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    $totarray=array();
                    if ($value[18]!='' && $value[19]!='') {
                        $setdata['id']=0;
                        $setdata['sec_qus']=$value[18];
                        $setdata['sec_ans']=$value[19];
                        array_push($totarray,$setdata);
                    }
                    if ($value[20]!='' && $value[21]!='') {
                        $setdata['id']=1;
                        $setdata['sec_qus']=$value[20];
                        $setdata['sec_ans']=$value[21];
                        array_push($totarray,$setdata);
                    }

                    if ($value[22]!='' && $value[23]!='') {
                        $setdata['id']=2;
                        $setdata['sec_qus']=$value[22];
                        $setdata['sec_ans']=$value[23];
                        array_push($totarray,$setdata);
                    }

                    if ($value[24]!='' && $value[25]!='') {
                        $setdata['id']=3;
                        $setdata['sec_qus']=$value[24];
                        $setdata['sec_ans']=$value[25];
                        array_push($totarray,$setdata);
                    }

                    if ($value[26]!='' && $value[27]!='') {
                        $setdata['id']=4;
                        $setdata['sec_qus']=$value[26];
                        $setdata['sec_ans']=$value[27];
                        array_push($totarray,$setdata);
                    }

                    if ($value[28]!='' && $value[29]!='') {
                        $setdata['id']=5;
                        $setdata['sec_qus']=$value[28];
                        $setdata['sec_ans']=$value[29];
                        array_push($totarray,$setdata);
                    }

                    if ($value[30]!='' && $value[31]!='') {
                        $setdata['id']=6;
                        $setdata['sec_qus']=$value[30];
                        $setdata['sec_ans']=$value[31];
                        array_push($totarray,$setdata);
                    }

                    $is_has=FDCompany::where('comp_short_name',$value[1])->get();
                    if (count($is_has) < 0) {
                        FDCompany::create(array(
                            'comp_type_id'=>$request->comp_type_id,
                            'comp_full_name'=>$value[0],
                            'comp_short_name'=>$value[1],
                            'website'=>$value[2],
                            'gstin'=>$value[3],
                            'cus_care_whatsapp_no'=>$value[4],
                            'cus_care_no'=>$value[5],
                            'cus_care_email'=>$value[6],
                            'head_ofc_contact_per'=>$value[7],
                            'head_contact_per_mob'=>$value[8],
                            'head_contact_per_email'=>$value[9],
                            'head_ofc_addr'=>$value[10],
                            'local_ofc_contact_per'=>$value[11],
                            'local_contact_per_mob'=>$value[12],
                            'local_contact_per_email'=>$value[13],
                            'local_ofc_addr'=>$value[14],
                            'login_url'=>$value[15],
                            'login_id'=>$value[16],
                            'login_pass'=>$value[17],
                            'security_qus_ans'=>json_encode($totarray),
                            'delete_flag'=>'N',
                            // 'created_by'=>'',
                        ));    
                    }

                }
               
            }
            $data1=[];
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
}
