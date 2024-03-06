<?php
namespace App\Helpers;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class Helper{

    public static function SuccessResponse($data)
    {
        return response()->json([
            'suc'=>1,
            'msg'=>Controller::DATA_FETCH_SUCCESS,
            'data'=>$data
        ],200);
    }

    public static function ErrorResponse($msg)
    {
        return response()->json([
            'suc'=>0,
            'msg'=>$msg,
            'data'=>[]
        ],400);
    }

    public static function WarningResponse($msg)
    {
        return response()->json([
            'suc'=>0,
            'msg'=>$msg,
            'data'=>[]
        ],201);
    }

    public static function loginErrorResponse($msg)
    {
        return response()->json([
            'suc'=>0,
            'msg'=>$msg,
            'data'=>null
        ],201);
    }

    public static function ipWhitelist($ip)
    {
        return response()->json([
            'suc'=>10,
            'msg'=>Controller::IP_WHITELIST_ERROR.$ip,
            'data'=>[]
        ],400);
    }

    public static function unauthorized($msg)
    {
        return response()->json([
            'suc'=>0,
            'msg'=>$msg,
            'data'=>[]
        ],403);
    }

    public static function modifyUser($user)
    {
        return $user->id;
    }

    public static function CommonParamValue($id)
    {
        return DB::table('md_parameters')->where('sl_no',$id)->value('param_value');
    }

    public static function TempTINGen($val,$product_id)
    {
        if ($product_id==1) {  // MUTUAL FUND
            $tin='MFT00'.$val;
        }elseif ($product_id==3) { // Insurance 
            $tin='INST00'.$val;
        }elseif ($product_id==4) { // Foixed Deposit 
            $tin='FDT00'.$val;
        }
        return $tin;
    }

    public static function GenTIN($product_id,$trans_type_id,$val)
    {
        if ($product_id==1) {  // MUTUAL FUND
            if ($trans_type_id==1) {  // Financial
                $tin='F00'.$val;
            }else if ($trans_type_id==2) {  // KYC
                $tin='KYC00'.$val;
            }else if ($trans_type_id==3) {  // Non Financial
                $tin='NF00'.$val;
            }else if ($trans_type_id==4) {  // NFO
                $tin='NFO00'.$val;
            }
        }elseif ($product_id==3) {  // Insurance
            $tin='INS00'.$val;
        }elseif ($product_id==4) { // Foixed Deposit 
            $tin='FD00'.$val;
        }
        return $tin;
    }

    public static function encrypt($inputFile, $outputFile, $password, $ownerPassword = null)
    {
        $mpdf = new \Mpdf\Mpdf();

        $pagecount = $mpdf->setSourceFile($inputFile);

        for ($p = 1; $p <= $pagecount; $p++) {
            $tplId = $mpdf->importPage($p);
            $wh = $mpdf->getTemplateSize($tplId);
            if (($p == 1)) {
                $mpdf->state = 0;
                $mpdf->AddPage($wh['width'] > $wh['height'] ? 'L' : 'P');
                $mpdf->UseTemplate($tplId);
            } else {
                $mpdf->state = 1;
                $mpdf->AddPage($wh['width'] > $wh['height'] ? 'L' : 'P');
                $mpdf->UseTemplate($tplId);
            }
        }

        //set owner password to user password if null
        $ownerPassword = is_null($ownerPassword) ? $password : $ownerPassword;

        $mpdf->SetProtection(['copy', 'print'], $password, $ownerPassword);

        $mpdf->Output($outputFile, \Mpdf\Output\Destination::FILE, );
    }

    public static function getBranchCode()
    {
        $branch_cd=1;
        return $branch_cd;
    }

    // seatch from date to to date 
    public static function FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString)
    {
        $Query='';
        if ($from_date && $to_date) {
            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
            $Query.=$condition."date(".$queryString.")"." >= '". $from_date."'";
            $Query.=" AND date(".$queryString.")"." <= '". $to_date."'";
        }
        return $Query;
    }

    public static function WhereRawQuery($row_name,$rawQuery,$queryString)
    {

        $Query='';
        $condition=(strlen($rawQuery) > 0)? " AND ":" ";
        if (is_array($row_name) && !empty($row_name)) {
            $row_name_string=  "'" .implode("','", $row_name). "'";
            // $row_name_string= implode(',', $row_name);
            $Query.=$condition.$queryString." IN (".$row_name_string.")";
        } elseif($row_name) {
            $Query.=$condition.$queryString."='".$row_name."'";
        }
        return $Query;
    }

    public static function WhereRawQueryOR($row_name,$rawQuery,$queryString)
    {

        $Query='';
        $condition=(strlen($rawQuery) > 0)? " OR ":" ";
        if (is_array($row_name) && !empty($row_name)) {
            $row_name_string=  "'" .implode("','", $row_name). "'";
            // $row_name_string= implode(',', $row_name);
            $Query.=$condition.$queryString." IN (".$row_name_string.")";
        } elseif($row_name) {
            $Query.=$condition.$queryString."='".$row_name."'";
        }
        return $Query;
    }

    public static function RawQueryLike($row_name,$rawQuery,$queryString)
    {
        $Query='';
        if ($row_name) {
            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
            $Query.=$condition.$queryString." LIKE '%".$row_name."%'";
        }
        return $Query;
    }

    public static function RawQueryOR($row_name,$rawQuery,$queryString)
    {
        $Query='';
        if ($row_name) {
            $condition=(strlen($rawQuery) > 0)? " OR ":" ";
            $Query.=$condition.$queryString." LIKE '%".$row_name."%'";
        }
        return $Query;
    }

    public static function RawQueryOnlyMonth($row_name,$rawQuery,$queryString)
    {
        $Query='';
        if ($row_name) {
            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
            $Query.=$condition."month(".$queryString.") ='".$row_name."'";
        }
        return $Query;
    }

    public static function RawQueryOnlyYear($row_name,$rawQuery,$queryString)
    {
        $Query='';
        if ($row_name) {
            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
            $Query.=$condition."year(".$queryString.") ='".$row_name."'";
        }
        return $Query;
    }
}