<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{AMC,Scheme};
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
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                if ($amc_id!='' && $rnt_id!='' && $gstin!='') {
                    if ($column_name=='rnt_name') {
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.id',$amc_id)
                            ->where('md_amc.rnt_id',$rnt_id)
                            ->where('md_amc.gstin','like', '%' . $gstin . '%')
                            ->orderBy('md_rnt.'.$column_name,$sort_by)
                            ->paginate($paginate);      
                    }else{
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.id',$amc_id)
                            ->where('md_amc.rnt_id',$rnt_id)
                            ->where('md_amc.gstin','like', '%' . $gstin . '%')
                            ->orderBy('md_amc.'.$column_name,$sort_by)
                            ->paginate($paginate); 
                    }
                }elseif ($rnt_id!='' && $gstin!='') {
                    if ($column_name=='rnt_name') {
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.gstin','like', '%' . $gstin . '%')
                            ->where('md_amc.rnt_id',$rnt_id)
                            ->orderBy('md_rnt.'.$column_name,$sort_by)
                            ->paginate($paginate);     
                    }else{
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.gstin','like', '%' . $gstin . '%')
                            ->where('md_amc.rnt_id',$rnt_id)
                            ->orderBy('md_amc.'.$column_name,$sort_by)
                            ->paginate($paginate); 
                    }
                }elseif ($amc_id!='' && $rnt_id!='') {
                    if ($column_name=='rnt_name') {
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.amc_id',$amc_id)
                            ->where('md_amc.rnt_id',$rnt_id)
                            ->orderBy('md_rnt.'.$column_name,$sort_by)
                            ->paginate($paginate);      
                    }else{
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.amc_id',$amc_id)
                            ->where('md_amc.rnt_id',$rnt_id)
                            ->orderBy('md_amc.'.$column_name,$sort_by)
                            ->paginate($paginate);     
                    }
                }elseif ($amc_id!='' && $gstin!='') {
                    if ($column_name=='rnt_name') {
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.amc_id',$amc_id)
                            ->where('md_amc.gstin','like', '%' . $gstin . '%')
                            ->orderBy('md_rnt.'.$column_name,$sort_by)
                            ->paginate($paginate);    
                    }else{
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.amc_id',$amc_id)
                            ->where('md_amc.gstin','like', '%' . $gstin . '%')
                            ->orderBy('md_amc.'.$column_name,$sort_by)
                            ->paginate($paginate);    
                    }
                } elseif ($amc_id!='') {
                    if ($column_name=='rnt_name') {
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.id',$amc_id)
                            ->orderBy('md_rnt.'.$column_name,$sort_by)
                            ->paginate($paginate);      
                    }else{
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.id',$amc_id)
                            ->orderBy('md_amc.'.$column_name,$sort_by)
                            ->paginate($paginate);
                    }
                }elseif ($gstin!='') {
                    if ($column_name=='rnt_name') {
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.gstin','like', '%' . $gstin . '%')
                            ->orderBy('md_rnt.'.$column_name,$sort_by)
                            ->paginate($paginate);    
                    }else{
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.gstin','like', '%' . $gstin . '%')
                            ->orderBy('md_amc.'.$column_name,$sort_by)
                            ->paginate($paginate);  
                    }
                } elseif ($rnt_id!='') {
                    if ($column_name=='rnt_name') {
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.rnt_id',$rnt_id)
                            ->orderBy('md_rnt.'.$column_name,$sort_by)
                            ->paginate($paginate);    
                    }else{
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->where('md_amc.rnt_id',$rnt_id)
                            ->orderBy('md_amc.'.$column_name,$sort_by)
                            ->paginate($paginate); 
                    }
                }else {
                    if ($column_name=='rnt_name') {
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->orderBy('md_rnt.'.$column_name,$sort_by)
                            ->paginate($paginate);  
                    }else{
                        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->orderBy('md_amc.'.$column_name,$sort_by)
                            ->paginate($paginate);  
                    } 
                }  
            }elseif ($amc_id!='' && $rnt_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.id',$amc_id)
                        ->where('md_amc.rnt_id',$rnt_id)
                        ->where('md_amc.gstin','like', '%' . $gstin . '%')
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);      
            }elseif ($rnt_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.gstin','like', '%' . $gstin . '%')
                        ->where('md_amc.rnt_id',$rnt_id)
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);      
            }elseif ($amc_id!='' && $rnt_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.amc_id',$amc_id)
                        ->where('md_amc.rnt_id',$rnt_id)
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);      
            }elseif ($amc_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.amc_id',$amc_id)
                        ->where('md_amc.gstin','like', '%' . $gstin . '%')
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);    
            } elseif ($amc_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.id',$amc_id)
                        // ->where('md_amc.rnt_id',$rnt_id)
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);      
            }elseif ($gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.gstin','like', '%' . $gstin . '%')
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);    
            } elseif ($rnt_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.rnt_id',$rnt_id)
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);      
            }else {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
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
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();      
            }elseif ($rnt_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();      
            }elseif ($amc_id!='' && $rnt_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.amc_id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();      
            }elseif ($amc_id!='' && $gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.amc_id',$amc_id)
                    ->where('md_amc.gstin','like', '%' . $gstin . '%')
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();    
            } elseif ($amc_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        // ->where('md_amc.id',$amc_id)
                    ->where('md_amc.rnt_id',$rnt_id)
                    ->orderBy('md_amc.updated_at','DESC')
                    ->get();      
            }elseif ($gstin!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.gstin','like', '%' . $gstin . '%')
                ->orderBy('md_amc.updated_at','DESC')
                ->get();    
            } elseif ($rnt_id!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->where('md_amc.id',$amc_id)
                ->orderBy('md_amc.updated_at','DESC')
                ->get();      
            }else {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
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
                $data=AMC::where('delete_flag','N')->where('amc_name','like', '%' . $search . '%')->get();      
            } elseif ($product_id!='') {
                $data=AMC::where('delete_flag','N')->where('product_id',$product_id)->get();      
            } elseif ($rnt_id!='') {
                $data=AMC::where('delete_flag','N')->where('rnt_id',$rnt_id)->paginate($paginate);      
            } elseif ($id!='') {
                $data=AMC::where('delete_flag','N')->where('id',$id)->get();  
            } elseif ($paginate!='') {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_amc.delete_flag','N')
                    ->orderBy('md_amc.updated_at','DESC')
                    ->paginate($paginate);      
                // $data=AMC::where('md_amc.delete_flag','N')->orderBy('updated_at','DESC')->paginate($paginate);      
            } else {
                $data=AMC::where('delete_flag','N')->orderBy('updated_at','DESC')->get();      
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
                $data->distributor_care_no=$request->distributor_care_no;
                $data->distributor_care_email=$request->distributor_care_email;
                $data->save();
            }else{
                $is_has=AMC::where('amc_name',$request->amc_name)->where('delete_flag','N')->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
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
                        'distributor_care_no'=>$request->distributor_care_no,
                        'distributor_care_email'=>$request->distributor_care_email,
                        'delete_flag'=>'N',
                        // 'created_by'=>'',
                    ));  
                }    
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
            $is_has=Scheme::where('amc_id',$id)->get();
            // return $is_has;
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=AMC::find($id);
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
            // return $data ;

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if (str_replace(" ","_",$value[0])!="AMC_Full_Name" && str_replace(" ","_",$value[1])!="AMC_Short_Name" && str_replace(" ","_",$value[2])!="R&T_Id") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
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

                    $is_has=AMC::where('amc_name',$value[0])->get();
                    if (count($is_has) < 0) {
                        AMC::create(array(
                            'product_id'=>base64_decode($request->product_id),
                            'rnt_id'=>$request->rnt_id,
                            'amc_name'=>$value[0],
                            'amc_short_name'=>$value[1],
                            'gstin'=>$value[2],
                            'website'=>$value[3],
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
                            'l1_name'=>$value[32],
                            'l1_contact_no'=>$value[33],
                            'l1_email'=>$value[34],
                            'l2_name'=>$value[35],
                            'l2_contact_no'=>$value[36],
                            'l2_email'=>$value[37],
                            'l3_name'=>$value[38],
                            'l3_contact_no'=>$value[39],
                            'l3_email'=>$value[40],
                            'l4_name'=>$value[41],
                            'l4_contact_no'=>$value[42],
                            'l4_email'=>$value[43],
                            'l5_name'=>$value[44],
                            'l5_contact_no'=>$value[45],
                            'l5_email'=>$value[46],
                            'l6_name'=>$value[47],
                            'l6_contact_no'=>$value[48],
                            'l6_email'=>$value[49],
                            'delete_flag'=>'N',
                        ));
                    }
                }
               
            }



            // return $data[0][0];
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                // Excel::import(new AMCImport,$request->file);
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