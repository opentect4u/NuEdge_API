<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{RNT,AMC};
use Validator;
use Excel;
use App\Imports\RNTImport;

class RNTController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $rnt_id=$request->rnt_id;
            $contact_person=$request->contact_person;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                $data=RNT::where('delete_flag','N')
                    ->orWhere('id',$rnt_id)
                    ->orWhere('head_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orWhere('local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy($column_name,$sort_by)
                    ->paginate($paginate);   
            }elseif ($rnt_id && $contact_person) {
                $data=RNT::where('delete_flag','N')
                    ->where('id',$rnt_id)
                    ->where('head_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orWhere('local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('updated_at','DESC')->paginate($paginate);      
            } elseif ($rnt_id) {
                $data=RNT::where('delete_flag','N')
                ->where('id',$rnt_id)->orderBy('updated_at','DESC')->paginate($paginate);      
            } elseif ($contact_person) {
                // return $contact_person;
                $data=RNT::where('delete_flag','N')
                    ->where('head_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orWhere('local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);      
            } else {
                $data=RNT::where('delete_flag','N')->orderBy('updated_at','DESC')->paginate($paginate);      
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
            $rnt_id=$request->rnt_id;
            $contact_person=$request->contact_person;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($sort_by && $column_name) {
                $data=RNT::where('delete_flag','N')
                    ->orWhere('id',$rnt_id)
                    ->orWhere('head_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orWhere('local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy($column_name,$sort_by)
                    ->get();   
            }elseif ($rnt_id && $contact_person) {
                $data=RNT::where('delete_flag','N')
                    ->where('id',$rnt_id)
                    ->where('head_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orWhere('local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('updated_at','DESC')->get();      
            } elseif ($rnt_id) {
                $data=RNT::where('delete_flag','N')
                ->where('id',$rnt_id)->orderBy('updated_at','DESC')->get();      
            } elseif ($contact_person) {
                // return $contact_person;
                $data=RNT::where('delete_flag','N')
                    ->where('head_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orWhere('local_ofc_contact_per','like', '%' . $contact_person . '%')
                    ->orderBy('updated_at','DESC')
                    ->get();      
            } else {
                $data=RNT::where('delete_flag','N')->orderBy('updated_at','DESC')->get();      
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
                $data=RNT::where('rnt_name','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=RNT::where('id',$id)->get();      
            }else if ($paginate!='') {
                $data=RNT::orderBy('updated_at','DESC')->paginate($paginate);      
            } else {
                $data=RNT::orderBy('updated_at','DESC')->get();      
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
            'rnt_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=RNT::find($request->id);
                $data->rnt_name=$request->rnt_name;
                $data->rnt_full_name=$request->rnt_full_name;
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
                $is_has=RNT::where('rnt_name',$request->rnt_name)->get();
                // return $is_has;
                if (count($is_has)>0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=RNT::create(array(
                        'rnt_name'=>$request->rnt_name,
                        'rnt_full_name'=>$request->rnt_full_name,
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

    public function import(Request $request)
    {
        try {
            // return $request;
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            // return $data;

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if (str_replace(" ","_",$value[0])=="R&T_Full_Name" && $value[2]=="Website") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value[0];
                    RNT::create(array(
                        'rnt_full_name'=>$value[0],
                        'rnt_name'=>$value[1],
                        'website'=>$value[2],
                        'head_ofc_contact_per'=>$value[5],
                        'head_contact_per_mob'=>$value[6],
                        'head_contact_per_email'=>$value[7],
                        'head_ofc_addr'=>$value[8],
                        'local_ofc_contact_per'=>$value[9],
                        'local_contact_per_mob'=>$value[10],
                        'local_contact_per_email'=>$value[11],
                        'local_ofc_addr'=>$value[12],
                        'cus_care_no'=>$value[3],
                        'cus_care_email'=>$value[4],
                        'login_url'=>$value[13],
                        'login_id'=>$value[14],
                        'login_pass'=>$value[15],
                        'security_qus_ans'=>$value[18],
                        'gstin'=>$value[17],
                        'cus_care_whatsapp_no'=>$value[16],
                        'delete_flag'=>'N',
                    ));
                }
               
            }
            // return gettype($data[0][0]) ;
            // if ($data[0][0] == "R&T Full Name") {
            // if ($data[0][0] == 'rnt_name' && $data[0][1] == 'website' && $data[0][2] == 'ofc_addr' && $data[0][3] == 'cus_care_no' && $data[0][4] == 'cus_care_email') {
                // return "hii";
                // Excel::import(new RNTImport,$request->file);
                // Excel::import(new RNTImport,request()->file('file'));
            // }else {
            //     // return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
            $data1=[];
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            $is_has=AMC::where('rnt_id',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=RNT::find($id);
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
    
}
