<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompBank;
use Validator;

class BankController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $cm_profile_id=$request->cm_profile_id;
            if ($search!='') {
                $data=CompBank::where('bank_name','like', '%' . $search . '%')->get();      
            }elseif ($cm_profile_id) {
                $data=CompBank::leftJoin('md_cm_profile','md_cm_profile.id','=','md_cm_bank.cm_profile_id')
                    ->select('md_cm_bank.*','md_cm_profile.type_of_comp as type_of_comp','md_cm_profile.name as cm_profile_name','md_cm_profile.establishment_name as establishment_name')
                    ->where('md_cm_bank.cm_profile_id',$cm_profile_id)
                    ->get();      
            } else {
                $data=CompBank::leftJoin('md_cm_profile','md_cm_profile.id','=','md_cm_bank.cm_profile_id')
                    ->select('md_cm_bank.*','md_cm_profile.type_of_comp as type_of_comp','md_cm_profile.name as cm_profile_name','md_cm_profile.establishment_name as establishment_name')
                    ->where('md_cm_bank.cm_profile_id',$cm_profile_id)
                    ->get();  
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        // $validator = Validator::make(request()->all(),[
        //     'product_name' =>'required',
        // ]);
    
        // if($validator->fails()) {
        //     $errors = $validator->errors();
        //     return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        // }
        try {
            // return $request;
            $all_details=json_decode($request->bank_dtls);
            // return $all_details;
            $data=[];    
            foreach ($all_details as $key => $value) {
                // return $request->upload_chq[$key];
                $file=$request->upload_chq[$key];
                if ($value->id >0) {
                    $dt=CompBank::find($value->id);

                    if ($file) {
                        $cv_path_extension=$file->getClientOriginalExtension();
                        $upload_chq_name=(microtime(true) *10000).".".$cv_path_extension;
                        $file->move(public_path('company/bank-chq/'),$upload_chq_name);

                        if($dt->upload_chq!=null){
                            $filelogo = public_path('company/license/') . $dt->upload_chq;
                            if (file_exists($filelogo) != null) {
                                unlink($filelogo);
                            }
                        } 
                    }else {
                        $upload_chq_name=$dt->upload_chq;
                    }
                    $dt->cm_profile_id=$value->cm_profile_id;
                    $dt->acc_no=$value->acc_no;
                    $dt->bank_name=$value->bank_name;
                    $dt->ifsc=$value->ifsc;
                    $dt->micr=$value->micr;
                    $dt->branch_name=$value->branch_name;
                    $dt->branch_add=$value->branch_add;
                    $dt->upload_chq=$upload_chq_name;
                    // $dt->updated_by=Helper::modifyUser($request->user());
                    $dt->save();
                }else {
                    $upload_chq_name='';
                    if ($file) {
                        $cv_path_extension=$file->getClientOriginalExtension();
                        $upload_chq_name=(microtime(true) * 10000).".".$cv_path_extension;
                        $file->move(public_path('company/bank-chq/'),$upload_chq_name);
                    }

                    $dt=CompBank::create(array(
                        'cm_profile_id'=>$value->cm_profile_id,
                        'acc_no'=>$value->acc_no,
                        'bank_name'=>$value->bank_name,
                        'ifsc'=>$value->ifsc,
                        'micr'=>$value->micr,
                        'branch_name'=>$value->branch_name,
                        'branch_add'=>$value->branch_add,
                        'upload_chq'=>isset($upload_chq_name)?$upload_chq_name:'',
                        // 'created_by'=>Helper::modifyUser($request->user()),
                    ));    
                }
                $data1=CompBank::leftJoin('md_cm_profile','md_cm_profile.id','=','md_cm_bank.cm_profile_id')
                    ->select('md_cm_bank.*','md_cm_profile.type_of_comp as type_of_comp','md_cm_profile.name as cm_profile_name','md_cm_profile.establishment_name as establishment_name')
                    ->where('md_cm_bank.id',$dt->id)
                    ->first();    
                array_push($data,$data1);
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
