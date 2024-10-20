<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\DocumentType;
use Validator;
use Excel;
use App\Imports\DocumentTypeImport;

class DocumentTypeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $doc_type=$request->doc_type;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $data=DocumentType::where('doc_type','like', '%' . $doc_type . '%')
                ->orderBy('updated_at','DESC')->paginate($paginate);      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $data=DocumentType::orderBy('updated_at','DESC')->get();      
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
                $data=DocumentType::where('doc_type','like', '%' . $search . '%')->get();      
            }elseif ($id!='') {
                $data=DocumentType::where('id',$id)->get();   
            }elseif ($paginate!='') {
                $data=DocumentType::paginate($paginate);      
            } else {
                $data=DocumentType::get();      
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
            'doc_type' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=DocumentType::find($request->id);
                $data->doc_type=$request->doc_type;
                $data->save();
            }else{
                $data=DocumentType::create(array(
                    'doc_type'=>$request->doc_type,
                    // 'created_by'=>'',
                ));      
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
            // return $data[0][0];

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if (str_replace(" ","_",$value[0])!="Document_Type") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    DocumentType::create(array(
                        'doc_type'=>$value[0],
                        // 'created_by'=>'',
                    ));       
                }
               
            }
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "opt_name") {
            //     return "hii";
                // Excel::import(new DocumentTypeImport,$request->file);
                // Excel::import(new DocumentTypeImport,request()->file('file'));
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
