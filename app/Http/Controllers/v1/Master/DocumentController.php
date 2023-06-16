<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Document,Client};
use Validator;
use Excel;
use App\Imports\DocumentImport;

class DocumentController extends Controller
{
    public function search(Request $request)
    {
        try {  
            $search=$request->search;
            $client_id=$request->client_id;
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=Client::with('ClientDoc')->orWhere('client_name','like', '%' . $search . '%')
                    ->orWhere('client_code','like', '%' . $search . '%')
                    ->orWhere('pan','like', '%' . $search . '%')
                    ->orWhere('mobile','like', '%' . $search . '%')
                    ->orWhere('email','like', '%' . $search . '%')
                    ->get();      
            }else if ($client_id!='') {
                $data=Client::with('ClientDoc')->where('id',$client_id)
                    ->get();     
            }else if ($paginate!='') {
                $data=Document::join('md_client','md_client.id','=','md_documents.client_id')
                    ->select('md_documents.*','md_client.client_name as client_name','md_client.client_code as client_code')
                    ->whereDate('md_documents.updated_at',date('Y-m-d'))->groupBy('client_id')->paginate($paginate);
            } else{
                $data=Document::join('md_client','md_client.id','=','md_documents.client_id')
                    ->select('md_documents.*','md_client.client_name as client_name','md_client.client_code as client_code')
                    ->whereDate('md_documents.updated_at',date('Y-m-d'))->groupBy('client_id')->get();      
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
                ->groupBy('md_client.client_code')
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

    public function Edit(Request $request)
    {
        try {  
            $client_id=$request->client_id;
            $data=Document::where('client_id',$client_id)->get();      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'client_id' =>'required',
            // 'doc_type_id' =>'required',
            // 'doc_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            
                $doc_name='';
                $doc_type_id=$request->doc_type_id;
                // return $doc_type_id;
                $files=$request->file;
                $data='';
                foreach ($files as $key => $file) {
                    // return $file;
                    if ($file) {
                        $cv_path_extension=$file->getClientOriginalExtension();
                        $doc_name=microtime(true).'_'.$request->client_id.".".$cv_path_extension;
                        $file->move(public_path('client-doc/'.$request->client_id."/"),$doc_name);
                    }
                    Document::create(array(
                        'client_id'=>$request->client_id,
                        'doc_type_id'=>$request->doc_type_id[$key],
                        'doc_name'=>$doc_name,
                        // 'created_by'=>'',
                    ));      
                }
            $data=Client::with('ClientDoc')->where('id',$request->client_id)->get();      
            
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'client_id' =>'required',
            // 'doc_type_id' =>'required',
            // 'doc_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }

        try {
            // return $request;
            // return $request->row_id;
            $data='';
            $file=$request->file;
            $doc_type_id=$request->doc_type_id;
            $doc_name='';
            if ($request->row_id!='') {
                foreach ($request->row_id as $key => $row_id) {
                    // return $row_id;
                    if ($row_id==0) {
                        if ($file[$key]) {
                            $cv_path_extension=$file[$key]->getClientOriginalExtension();
                            $doc_name=microtime(true).'_'.$request->client_id.".".$cv_path_extension;
                            $file[$key]->move(public_path('client-doc/'.$request->client_id."/"),$doc_name);
                        }
                        Document::create(array(
                            'client_id'=>$request->client_id,
                            'doc_type_id'=>$request->doc_type_id[$key],
                            'doc_name'=>$doc_name,
                            // 'created_by'=>'',
                        ));    
                    } else {
                        if ($file[$key]) {
                            $cv_path_extension=$file[$key]->getClientOriginalExtension();
                            $doc_name=microtime(true).'_'.$request->client_id.".".$cv_path_extension;
                            $file[$key]->move(public_path('client-doc/'.$request->client_id."/"),$doc_name);
                        }
                        $data=Document::find($row_id);
                        if($data->doc_name!=null){
                            $filecv = public_path('client-doc/'.$request->client_id."/") . $data->doc_name;
                            if (file_exists($filecv) != null) {
                                unlink($filecv);
                            }
                        } 
                        $data->doc_name=$doc_name;
                        $data->save();
                    }
                    
                }
            }
            $data=Client::with('ClientDoc')->where('id',$request->client_id)->get();      

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
            // return $data[0][0];
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "opt_name") {
            //     return "hii";
                Excel::import(new DocumentImport,$request->file);
                // Excel::import(new DocumentImport,request()->file('file'));
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
