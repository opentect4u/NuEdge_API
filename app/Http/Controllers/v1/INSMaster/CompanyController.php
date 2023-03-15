<?php

namespace App\Http\Controllers\v1\INSMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsCompany,InsProduct};
use Validator;

class CompanyController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $ins_type_id=$request->ins_type_id;
            $name=$request->name;
            $contact_person=$request->contact_person;
            
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                if ($column_name='ins_type') {
                    $data=$data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->orderBy('md_ins_type.type',$sort_by)
                    ->paginate($paginate); 
                }else {
                    $data=$data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->orderBy('md_ins_company.'.$column_name,$sort_by)
                    ->paginate($paginate); 
                }
            }elseif ($contact_person) {
                $data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->OrWhere('md_ins_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_ins_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_ins_company.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif ($name) {
                $data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->OrWhere('md_ins_company.comp_short_name','like', '%' . $name . '%')
                    ->OrWhere('md_ins_company.comp_full_name','like', '%' . $name . '%')
                    ->orderBy('md_ins_company.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif ($ins_type_id) {
                $data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->where('md_ins_company.ins_type_id',$ins_type_id)
                    ->orderBy('md_ins_company.updated_at','DESC')
                    ->paginate($paginate);  
            } else {
                $data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->orderBy('md_ins_company.updated_at','DESC')
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
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $ins_type_id=$request->ins_type_id;
            $name=$request->name;
            $contact_person=$request->contact_person;
            
            if ($sort_by && $column_name) {
                if ($column_name='ins_type') {
                    $data=$data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->orderBy('md_ins_type.type',$sort_by)
                    ->get(); 
                }else {
                    $data=$data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->orderBy('md_ins_company.'.$column_name,$sort_by)
                    ->get(); 
                }
            }elseif ($contact_person) {
                $data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->OrWhere('md_ins_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->OrWhere('md_ins_company.local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('md_ins_company.updated_at','DESC')
                    ->get();  
            }elseif ($name) {
                $data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->OrWhere('md_ins_company.comp_short_name','like', '%' . $name . '%')
                    ->OrWhere('md_ins_company.comp_full_name','like', '%' . $name . '%')
                    ->orderBy('md_ins_company.updated_at','DESC')
                    ->get();  
            }elseif ($ins_type_id) {
                $data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->where('md_ins_company.ins_type_id',$ins_type_id)
                    ->orderBy('md_ins_company.updated_at','DESC')
                    ->get();  
            } else {
                $data=InsCompany::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_company.ins_type_id')
                    ->select('md_ins_company.*','md_ins_type.type as ins_type')
                    ->where('md_ins_company.delete_flag','N')
                    ->orderBy('md_ins_company.updated_at','DESC')
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
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=InsCompany::where('type','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=InsCompany::where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=InsCompany::paginate($paginate);      
            } else {
                $data=InsCompany::get();      
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
            'ins_type_id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=InsCompany::find($request->id);
                $data->ins_type_id=$request->ins_type_id;
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
                $data->save();
            }else{
                $is_has=InsCompany::where('comp_short_name',$request->comp_short_name)->where('comp_full_name',$request->comp_full_name)->get();
                // return $is_has;
                if (count($is_has)>0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=InsCompany::create(array(
                        'ins_type_id'=>$request->ins_type_id,
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
                $data=InsCompany::find($id);
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
            return $data;

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0]=="Plan") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    InsCompany::create(array(
                        'type'=>$value[0],
                        // 'created_by'=>'',
                    ));    
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
