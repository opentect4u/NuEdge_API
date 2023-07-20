<?php
namespace App\Helpers;
use App\Http\Controllers\Controller;
use DB;

class TransHelper{

    public function transSubTypeKFIN($trans_sub_type_code,$trans_flag)
    {
        $sub_type_name="";
        switch ($trans_sub_type_code) {
            case 'NEW':
                $sub_type_name="Fresh";
                break;
            case 'ADD':
                $sub_type_name="Additional";
                break;
            case 'IPO':
                $sub_type_name="NFO";
                break;
            case 'IPOR':
                $sub_type_name="NFO Rejection";
                break;
            case 'IPOD':
                $sub_type_name="NFO Pre-Rejection";
                break;
            case 'NEWR':
                $sub_type_name="Fresh";
                break;
            case 'ADDR':
                $sub_type_name="Additional";
                break;
            case 'NEWD':
                $sub_type_name="Fresh Pre-Rejection";
                break;
            case 'BNS':
                $sub_type_name="Bonus Unit";
                break;
            case 'BNSR':
                $sub_type_name="Bonus Rejection";
                break;
            case 'BNSRR':
                $sub_type_name="Bonus Rejection Reversal";
                break;
            case 'CNI':
                $sub_type_name="Consolidation In";
                break;
            case 'CNO':
                $sub_type_name="Consolidation Out";
                break;
            case 'DMT':
                $sub_type_name="DEMET";
                break;
            case 'DIV':
                if ($trans_flag=='DP') {
                    $sub_type_name="DIVIEDEND Payout";
                }elseif ($trans_flag=='DR') {
                    $sub_type_name="DIVIEDEND Reinvestment";
                }
                break;
            case 'LTINR' || 'LTIAR':
                $sub_type_name="Lateral Switch In Rejection";
                break;
            case 'LTOFR' || 'LTOPR':
                $sub_type_name="Lateral Switch Out Rejection";
                break;
            case 'LTIA' || 'LTIN':
                $sub_type_name="Lateral Switch In";
                break;
            case 'LTIAD' || 'LTIND':
                $sub_type_name="Lateral Switch In Rejection";
                break;
            case 'LTOF' || 'LTOP':
                $sub_type_name="Lateral Switch Out";
                break;
            case 'LTOPD':
                $sub_type_name="Lateral Switch Out Rejection";
                break;
            case 'PLDO':
                $sub_type_name="Lien-In";
                break;
            case 'RED':
                $sub_type_name="Pertial Redemtion";
                break;
            case 'FUL':
                $sub_type_name="Full Redemtion";
                break;
            case 'FULD':
                $sub_type_name="Redemtion Rejection";
                break;
            case 'REDR':
                $sub_type_name="Pertial Redemtion Rejection";
                break;
            case 'FULR':
                $sub_type_name="Full Redemtion Rejection";
                break;
            case 'REDRR':
                $sub_type_name="Redemtion Rejection Reversal";
                break;
            case 'RFD':
                $sub_type_name="Refund";
                break;
            case 'STPA' || 'STPI' || 'STPN':
                $sub_type_name="STP In";
                break;
            case 'STPAR' || 'STPNR':
                $sub_type_name="STP In Rejection";
                break;
            case 'STPO':
                $sub_type_name="STP Out";
                break;
            case 'STPOR':
                $sub_type_name="STP Out Rejection";
                break;
            case 'BNS':
                $sub_type_name="Segregated";
                break;
            case 'BNSR':
                $sub_type_name="Segregated Unit Rejection";
                break;
            case 'SINR':
                $sub_type_name="SIP Rejection";
                break;
            case 'SWIAR' || 'SWINR':
                $sub_type_name="Switch In Rejection";
                break;
            case 'SWOFR':
                $sub_type_name="Switch Out Rejection";
                break;
            case 'SWIA' || 'SWIN':
                $sub_type_name="Switch In";
                break;
            case 'SWOF' || 'SWOP':
                $sub_type_name="Switch Out";
                break;
            case 'SWDR':
                $sub_type_name="SWP Rejection";
                break;
            case 'SWDPR':
                $sub_type_name="SWP Rejection Reversal";
                break;
            case 'SIM':
                $sub_type_name="SIP Purchase";
                break;
            case 'SIND':
                $sub_type_name="SIP Installment Rejection";
                break;
            case 'SWD':
                $sub_type_name="SWP";
                break;
            case 'SWDD':
                $sub_type_name="SWP Installment Rejection";
                break;
            case 'TMO' || 'TRMO':
                $sub_type_name="Transmission Out";
                break;
            case 'TMI' || 'TRMI':
                $sub_type_name="Transmission In";
                break;
            default:
                $sub_type_name="N/A";
                break;
        }
        return $sub_type_name;
    }

    public function transTypeToCodeCAMS($trxn_type)
    {
        $trxn_type_code="";
        if (str_starts_with($trxn_type, 'P')) {
            $trxn_type_code="P";
        }else if (str_starts_with($trxn_type, 'R')) {
            $trxn_type_code="R";
        }else if (str_starts_with($trxn_type, 'SI')) {
            $trxn_type_code="SI";
        }else if (str_starts_with($trxn_type, 'SO')) {
            $trxn_type_code="SO";
        }else if (str_starts_with($trxn_type, 'TI')) {
            $trxn_type_code="TI";
        }else if (str_starts_with($trxn_type, 'TO')) {
            $trxn_type_code="TO";
        }else if (str_starts_with($trxn_type, 'DR')) {
            $trxn_type_code="DR";
        }else if (str_starts_with($trxn_type, 'J')) {
            $trxn_type_code="J";
        }else {
            $trxn_type_code="ALL Others";
        }
        return $trxn_type_code;
    }

    public function trxnNatureCodeCAMS($trxn_nature)
    {
        $trxn_nature_code="";
        if (str_starts_with($trxn_nature, 'Systematic')) {
            $trxn_nature_code="Systematic";
        // }else if (str_starts_with($trxn_nature, 'NFO Purchase')) {
        //     $trxn_nature_code="NFO Purchase";
        }else {
            $trxn_nature_code=$trxn_nature;
        }
        return $trxn_nature_code;
    }
}