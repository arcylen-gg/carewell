<?php
namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cal;
use PDF;

class CALUploadController extends Controller
{
    public function export(Request $request, Cal $model)
    {
        // return $model->with([
        //     'company',
        //     'payment_mode',
        //     'payment_term',
        //     'cal_member.member'
        //     ])->find($request->id);

        // $data['data'] = $model->with([
        //     'company',
        //     'payment_mode',
        //     'payment_term',
        //     'cal_member.member'
        //     ])->find($request->id);

        $getData = $model->with([
            'company',
            'payment_mode',
            'payment_term',
            'cal_member.member'
        ])->find($request->id);

        // return $getData['reference_number'];

        $data['data']['reference_number']       = $getData['reference_number'];
        $data['data']['payment_cal_date']       = $getData['payment_cal_date'];
        $data['data']['payment_due_date']       = $getData['payment_due_date'];
        $data['data']['payment_start_date']     = $getData['payment_start_date'];
        $data['data']['payment_end_date']       = $getData['payment_end_date'];
        $data['data']['company']['name']        = $getData['company']['name'];
        $data['data']['payment_mode']['name']   = $getData['payment_mode']['name'];
        $data['data']['payment_term']['name']   = $getData['payment_term']['name'];
        $data['data']['total_paid_amount']      = 0;
        $data['data']['total_monthly_premium']  = 0;

        foreach ($getData->cal_member as $key => $value) {
            $data['data']['cal_member'][$key]['member']['company']['universal_id'] = $value->member->company->universal_id;
            $data['data']['cal_member'][$key]['member']['last_name'] = $value->member->last_name;
            $data['data']['cal_member'][$key]['member']['first_name'] = $value->member->first_name;
            $data['data']['cal_member'][$key]['member']['middle_name'] = $value->member->middle_name;
            $data['data']['cal_member'][$key]['member']['birth_date'] = $value->member->birth_date;
            $data['data']['cal_member'][$key]['member']['company']['coverage_plan']['name'] = $value->member->company->coverage_plan->name;
            $data['data']['cal_member'][$key]['member']['company']['company_deployment']['name'] = $value->member->company->company_deployment->name;
            $data['data']['cal_member'][$key]['monthly_premium'] = $value->monthly_premium;
            $data['data']['cal_member'][$key]['paid_amount'] = $value->paid_amount;
            $data['data']['total_paid_amount'] += $value->paid_amount;
            $data['data']['total_monthly_premium'] += $value->monthly_premium;
        }

        // return $data;
        
        return view('exports.cal.cal_export_print', $data);


        // $pdf = PDF::loadView('exports.cal.cal_export_print',$data)->setPaper('A4', 'landscape')->setWarnings(false);
        // return $pdf->stream();
    }
}