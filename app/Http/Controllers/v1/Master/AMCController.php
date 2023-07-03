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
            // return $request;
            $paginate=$request->paginate;
            $rnt_id=json_decode($request->rnt_id);
            $amc_id=json_decode($request->amc_id);
            $order=$request->order;
            $field=$request->field;
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

                if ($amc_id || $rnt_id ) {
                    $rawQuery='';
                    if (!empty($rnt_id)) {
                        $rnt_id_string= implode(',', $rnt_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_amc.rnt_id IN (".$rnt_id_string.")";
                        }else {
                            $rawQuery.=" md_amc.rnt_id IN (".$rnt_id_string.")";
                        }
                    }
                    if (!empty($amc_id)) {
                        $amc_id_string= implode(',', $amc_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_amc.id IN (".$amc_id_string.")";
                        }else {
                            $rawQuery.=" md_amc.id IN (".$amc_id_string.")";
                        }
                    }
                    $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->whereRaw($rawQuery)
                            ->orderByRaw($rawOrderBy)
                            ->paginate($paginate);      
                } else {
                    $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->orderByRaw($rawOrderBy)
                            ->paginate($paginate);    
                }  
            }elseif ($amc_id || $rnt_id ) {
                $rawQuery='';
                if (!empty($rnt_id)) {
                    $rnt_id_string= implode(',', $rnt_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_amc.rnt_id IN (".$rnt_id_string.")";
                    }else {
                        $rawQuery.=" md_amc.rnt_id IN (".$rnt_id_string.")";
                    }
                }
                if (!empty($amc_id)) {
                    $amc_id_string= implode(',', $amc_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_amc.id IN (".$amc_id_string.")";
                    }else {
                        $rawQuery.=" md_amc.id IN (".$amc_id_string.")";
                    }
                }
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);      
            } else {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->orderBy('md_amc.updated_at','DESC')
                        ->paginate($paginate);    
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
            $rnt_id=json_decode($request->rnt_id);
            $amc_id=json_decode($request->amc_id);
            $order=$request->order;
            $field=$request->field;
            
            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }

                if ($amc_id || $rnt_id ) {
                    $rawQuery='';
                    if (!empty($rnt_id)) {
                        $rnt_id_string= implode(',', $rnt_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_amc.rnt_id IN (".$rnt_id_string.")";
                        }else {
                            $rawQuery.=" md_amc.rnt_id IN (".$rnt_id_string.")";
                        }
                    }
                    if (!empty($amc_id)) {
                        $amc_id_string= implode(',', $amc_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_amc.id IN (".$amc_id_string.")";
                        }else {
                            $rawQuery.=" md_amc.id IN (".$amc_id_string.")";
                        }
                    }
                    $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->whereRaw($rawQuery)
                            ->orderByRaw($rawOrderBy)
                            ->get();      
                } else {
                    $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                            ->where('md_amc.delete_flag','N')
                            ->orderByRaw($rawOrderBy)
                            ->get();    
                }  
            }elseif ($amc_id || $rnt_id ) {
                $rawQuery='';
                if (!empty($rnt_id)) {
                    $rnt_id_string= implode(',', $rnt_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_amc.rnt_id IN (".$rnt_id_string.")";
                    }else {
                        $rawQuery.=" md_amc.rnt_id IN (".$rnt_id_string.")";
                    }
                }
                if (!empty($amc_id)) {
                    $amc_id_string= implode(',', $amc_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_amc.id IN (".$amc_id_string.")";
                    }else {
                        $rawQuery.=" md_amc.id IN (".$amc_id_string.")";
                    }
                }
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderBy('md_amc.updated_at','DESC')
                        ->get();      
            } else {
                $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
                        ->where('md_amc.delete_flag','N')
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
                $data=AMC::where('delete_flag','N')->orderBy('amc_short_name','ASC')->get();  
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
            // return $request;
            if ($request->id > 0) {
                $data=AMC::find($request->id);

                $logo=$request->logo;
                if ($logo) {
                    $logo_path_extension=$logo->getClientOriginalExtension();
                    $logo_name=microtime(true).".".$logo_path_extension;
                    $logo->move(public_path('amc-logo/'),$logo_name);

                    if($data->logo!=null){
                        $filecv = public_path('amc-logo/') . $data->logo;
                        if (file_exists($filecv) != null) {
                            unlink($filecv);
                        }
                    } 
                }else {
                    $logo_name=$data->logo;
                }

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
                $data->logo=$logo_name;
                $data->save();
            }else{
                $is_has=AMC::where('amc_name',$request->amc_name)->where('delete_flag','N')->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $logo=$request->logo;
                    if ($logo) {
                        $logo_path_extension=$logo->getClientOriginalExtension();
                        $logo_name=microtime(true).".".$logo_path_extension;
                        $logo->move(public_path('amc-logo/'),$logo_name);
                    }

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
                        'logo'=>$logo_name,
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
            // $path = $request->file('file')->getRealPath();
            // $data = array_map('str_getcsv', file($path));
            // return $data ;
            $datas = Excel::toArray([],  $request->file('file'));
            // return $data[0];
            $data=$datas[0];

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if (str_replace(" ","_",$value[0])!="AMC_Full_Name" && str_replace(" ","_",$value[1])!="AMC_Short_Name" && str_replace(" ","_",$value[2])!="R&T_Id") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value[0];
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
                        // return $is_has[0]->id;
                        // return Helper::WarningResponse(parent::ALREADY_EXIST);
                        AMC::whereId($is_has[0]->id)->update(array(
                            'product_id'=>base64_decode($request->product_id),
                            'rnt_id'=>$request->rnt_id,
                            'amc_name'=>$value[0],
                            'amc_short_name'=>$value[1],
                            'gstin'=>$value[2],
                            'website'=>$value[3],
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