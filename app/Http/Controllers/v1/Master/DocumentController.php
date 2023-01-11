<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Document,Client};
use Validator;

class DocumentController extends Controller
{
    public function search(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=Client::orWhere('client_name','like', '%' . $search . '%')
                    ->orWhere('client_code','like', '%' . $search . '%')
                    ->orWhere('pan','like', '%' . $search . '%')
                    ->orWhere('mobile','like', '%' . $search . '%')
                    ->orWhere('email','like', '%' . $search . '%')
                    ->get();      
            }else{
                $data=Client::get();      
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
            if ($search!='') {
                $data=Client::join('md_documents','md_documents.client_id','=','md_client.id')
                ->select('md_client.*')
                ->where('md_client.client_code', $search)
                ->get();    
            }else {
                $data=Document::get();      
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
            'client_id' =>'required',
            'doc_type_id' =>'required',
            'doc_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Document::find($request->id);

                $doc_name='';
                if ($request->hasFile('doc_name')) {
                    $cv_path = $request->file('doc_name');
                    $cv_path_extension=$cv_path->getClientOriginalExtension();
                    $doc_name=date('YmdHis').'_'.$request->client_id.".".$cv_path_extension;
                    $cv_path->move(public_path('client-doc/'),$doc_name);

                    if($data->doc_name!=null){
                    $filecv = public_path('client-doc/') . $data->doc_name;
                    if (file_exists($filecv) != null) {
                            unlink($filecv);
                        }
                    } 
                }else{
                    $doc_name=$data->doc_name;
                }



                $data->client_id=$request->client_id;
                $data->doc_type_id=$request->doc_type_id;
                $data->doc_name=$doc_name;
                $data->save();
            }else{
                $doc_name='';
                if ($request->hasFile('doc_name')) {
                    $cv_path = $request->file('doc_name');
                    $cv_path_extension=$cv_path->getClientOriginalExtension();
                    $doc_name=date('YmdHis').'_'.$request->client_id.".".$cv_path_extension;
                    $cv_path->move(public_path('client-doc/'),$doc_name);
                }

                $data=Document::create(array(
                    'client_id'=>$request->client_id,
                    'doc_type_id'=>$request->doc_type_id,
                    'doc_name'=>$doc_name,
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
