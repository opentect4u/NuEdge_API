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
                if ($rnt_id && $contact_person) {
                    $data=RNT::where('delete_flag','N')
                        ->where('id',$rnt_id)
                        ->where('head_ofc_contact_per','like', '%' . $contact_person . '%')
                        ->orWhere('local_ofc_contact_per','like', '%' . $contact_person . '%')
                        ->orderBy($column_name,$sort_by)
                        ->paginate($paginate);      
                }elseif ($rnt_id) {
                    $data=RNT::where('delete_flag','N')
                        ->where('id',$rnt_id)
                        ->orderBy($column_name,$sort_by)
                        ->paginate($paginate);  
                }elseif ($contact_person) {
                    $data=RNT::where('delete_flag','N')
                        ->where('head_ofc_contact_per','like', '%' . $contact_person . '%')
                        ->orWhere('local_ofc_contact_per','like', '%' . $contact_person . '%')
                        ->orderBy($column_name,$sort_by)
                        ->paginate($paginate);   
                } else {
                    $data=RNT::where('delete_flag','N')
                        ->orderBy($column_name,$sort_by)
                        ->paginate($paginate);  
                }
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
                $data=RNT::where('delete_flag','N')->where('rnt_name','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=RNT::where('delete_flag','N')->where('id',$id)->get();      
            }else if ($paginate!='') {
                $data=RNT::where('delete_flag','N')->orderBy('updated_at','DESC')->paginate($paginate);      
            } else {
                $data=RNT::where('delete_flag','N')->orderBy('updated_at','DESC')->get();      
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
                $data->distributor_care_no=$request->distributor_care_no;
                $data->distributor_care_email=$request->distributor_care_email;
                $data->save();
            }else{
                $is_has=RNT::where('rnt_name',$request->rnt_name)->where('delete_flag','N')->get();
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
                        'distributor_care_no'=>$request->distributor_care_no,
                        'distributor_care_email'=>$request->distributor_care_email,
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
                    if (str_replace(" ","_",$value[0])!="R&T_Full_Name" && $value[2]!="Website") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    $totarray=array();
                    if ($value[20]!='' && $value[21]!='') {
                        $setdata['id']=0;
                        $setdata['sec_qus']=$value[20];
                        $setdata['sec_ans']=$value[21];
                        array_push($totarray,$setdata);
                    }
                    if ($value[22]!='' && $value[23]!='') {
                        $setdata['id']=1;
                        $setdata['sec_qus']=$value[22];
                        $setdata['sec_ans']=$value[23];
                        array_push($totarray,$setdata);
                    }

                    if ($value[24]!='' && $value[25]!='') {
                        $setdata['id']=2;
                        $setdata['sec_qus']=$value[24];
                        $setdata['sec_ans']=$value[25];
                        array_push($totarray,$setdata);
                    }

                    if ($value[26]!='' && $value[27]!='') {
                        $setdata['id']=3;
                        $setdata['sec_qus']=$value[26];
                        $setdata['sec_ans']=$value[27];
                        array_push($totarray,$setdata);
                    }

                    if ($value[28]!='' && $value[29]!='') {
                        $setdata['id']=4;
                        $setdata['sec_qus']=$value[28];
                        $setdata['sec_ans']=$value[29];
                        array_push($totarray,$setdata);
                    }

                    if ($value[30]!='' && $value[31]!='') {
                        $setdata['id']=5;
                        $setdata['sec_qus']=$value[30];
                        $setdata['sec_ans']=$value[31];
                        array_push($totarray,$setdata);
                    }

                    if ($value[32]!='' && $value[33]!='') {
                        $setdata['id']=6;
                        $setdata['sec_qus']=$value[32];
                        $setdata['sec_ans']=$value[33];
                        array_push($totarray,$setdata);
                    }

                    // return $totarray;
                    // return $value[0];
                    $is_has=RNT::where('rnt_name',$value[1])->get();
                    if (count($is_has) < 0) {
                        RNT::create(array(
                            'rnt_full_name'=>$value[0],
                            'rnt_name'=>$value[1],
                            'website'=>$value[2],
                            'gstin'=>$value[3],
                            'cus_care_whatsapp_no'=>$value[4],
                            'cus_care_no'=>$value[5],
                            'cus_care_email'=>$value[6],
                            'distributor_care_no'=>$value[7],
                            'distributor_care_email'=>$value[8],
                            'head_ofc_contact_per'=>$value[9],
                            'head_contact_per_mob'=>$value[10],
                            'head_contact_per_email'=>$value[11],
                            'head_ofc_addr'=>$value[12],
                            'local_ofc_contact_per'=>$value[13],
                            'local_contact_per_mob'=>$value[14],
                            'local_contact_per_email'=>$value[15],
                            'local_ofc_addr'=>$value[16],
                            'login_url'=>$value[17],
                            'login_id'=>$value[18],
                            'login_pass'=>$value[19],
                            'security_qus_ans'=>json_encode($totarray),
                            'l1_name'=>$value[34],
                            'l1_contact_no'=>$value[35],
                            'l1_email'=>$value[36],
                            'l2_name'=>$value[37],
                            'l2_contact_no'=>$value[38],
                            'l2_email'=>$value[39],
                            'l3_name'=>$value[40],
                            'l3_contact_no'=>$value[41],
                            'l3_email'=>$value[42],
                            'l4_name'=>$value[43],
                            'l4_contact_no'=>$value[43],
                            'l4_email'=>$value[45],
                            'l5_name'=>$value[46],
                            'l5_contact_no'=>$value[47],
                            'l5_email'=>$value[48],
                            'l6_name'=>$value[49],
                            'l6_contact_no'=>$value[50],
                            'l6_email'=>$value[51],
                            'delete_flag'=>'N',
                        ));
                    }else {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
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
            if (count($is_has) > 0) {
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
