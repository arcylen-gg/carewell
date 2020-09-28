<?php
namespace App\Globals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use DB;
use Carbon\Carbon;
use App\Globals\CoveragePlanService;
use App\Globals\MemberService;


class AvailmentService
{
    public static function validate_member($input, $availment_id = null)
    {
    	$member_id = $input->memberInformation['member']['id'];
    	$company_id = $input->memberInformation['member']['company']['company_id'];
    	$coverage_plan_id = $input->memberInformation['member']['company']['coverage_plan_id'];
    	$benefit_type_id = $input->availmentInformation['benefitType']['benefit_type_id'];
    	$date_availment = $input->availmentInformation['date_avail'];

        $diagnosis_id  = isset($input->availmentInformation['diagnosis_id']) ? $input->availmentInformation['diagnosis_id'] : null;

        $procedure_total_carewell = $input->procedureInformation['procedures_carewell_charged_total'];
        $doctor_total_carewell = $input->physicianInformation['doctors_carewell_charged_total'];
        $charged_to_carewell_total = $input->payeeInformation['grand_total'];

        $procedure_total = 0;
        $_procedures_list = $input->procedureInformation["procedure_list"];
        $_procedures = array();
        foreach ($_procedures_list as $key => $value) 
        {
        	if($value['procedure']['id'] && $value['procedures_carewell_charge'] && !$value['procedures_is_disapproved'])
        	{
        		$_procedures[$key]['procedures_id'] = $value['procedure']['id'];
        		$_procedures[$key]['procedures_amount'] = $value['procedures_carewell_charge'];
                $procedure_total += $value['procedures_carewell_charge'];
        	}
        }
        $doctor_total = 0;
        $_doctors_list = $input->physicianInformation["physician_list"];
        $_doctors = array();
        foreach ($_doctors_list as $key => $value) 
        {
        	if($value['doctor_id'] && $value['doctor_carewell_charge'])
        	{
        		$_doctors[$key]['doctors_id'] = $value['doctor_id'];
        		$_doctors[$key]['doctors_amount'] = $value['doctor_carewell_charge'];
                $doctor_total += $value['doctor_carewell_charge'];
        	}
        }
        $return = null;
        /* CHECK FOR SPECIAL AMOUNT */
        $return .= Self::validate_special_amount($_procedures, $coverage_plan_id, $benefit_type_id, $member_id, $company_id, $diagnosis_id, $availment_id);

        /* CHECK FOR BALANCE IN AMOUNT COVERED */
        $return .= Self::validate_amount_covered($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $charged_to_carewell_total, $company_id, $date_availment, $availment_id);

        /* CHECK FOR CONFINEMENT BALANCE */
        $return .= Self::validate_confinement($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $charged_to_carewell_total, $company_id, $date_availment, $availment_id);

        /* CHECK FOR BALANCE IN MBL */
        $return .= Self::validate_mbl($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $charged_to_carewell_total, $company_id, $date_availment, $availment_id);

        /* CHECK FOR AVAIL PER MONTH AND YEAR */
        $return .= Self::validate_year_month($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $date_availment, $company_id, $availment_id);

        /* CHECK FOR AVAILMENT OF DEATH */
        $return .= Self::validate_if_alive($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $date_availment, $company_id, $availment_id);
        return $return;
    }
    public static function validate_special_amount($_procedures, $coverage_plan_id, $benefit_type_id,$member_id, $company_id, $diagnosis_id = null, $availment_id = null)
    {
    	$return = '';
    	$_coverage_procedures = CoveragePlanService::get_coverage_info($coverage_plan_id, $benefit_type_id)['_procedures'];
    	foreach($_procedures as $key => $value) 
    	{
    		foreach($_coverage_procedures as $keycov => $valuecov) 
    		{
    			if($value['procedures_id'] == $valuecov->procedure_id)
    			{
                    $usage = MemberService::get_member_procedures($member_id, $company_id, $coverage_plan_id, $benefit_type_id, $value['procedures_id'], $diagnosis_id, $availment_id); 
                    $special_limit_balance = round(($valuecov->amount - $usage),2);
                    if($valuecov->amount != 0)
                    {
        				if($value['procedures_amount'] > $special_limit_balance)
        				{
                            $return .= '<br> The remaining balance for '.$valuecov->name.' is '.number_format($special_limit_balance,2).'. The member already used '.number_format($usage,2).' out of '.number_format($valuecov->amount,2).' and the amount for this procedure is '.number_format($value['procedures_amount'],2).'<br>';
        					// $return .= '<br> '.$valuecov->name.' cannot be exceed to its special amount worth '.number_format($valuecov->amount,2).' <br>';
        				}
                    }
    			}
    		}
    	}
    	return $return;
    }

    public static function validate_confinement($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $grand_total = 0, $company_id, $date_avail, $availment_id = null)
    {
        $return = '';
        if($benefit_type_id == 7)
        {
            $balance = Self::member_balance($member_id, $company_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $date_avail, $availment_id);
            if($grand_total < $balance['amount_covered'])
            {
                if($grand_total > $balance['confinement_limit'])
                {
                    $return = '<br> This availment total is '.number_format($grand_total,2).' and already exceed to the confinement limit worth '.number_format($balance['confinement_limit'],2).' <br>';
                }
            }
        }
        return $return;
    }
    public static function validate_amount_covered($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $grand_total = 0, $company_id, $date_avail, $availment_id = null)
    {
        $return = '';
    	$balance = Self::member_balance($member_id, $company_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $date_avail, $availment_id);
        if($grand_total > $balance['amount_covered'])
        {
            $return = '<br> The remaining balance for amount covered is '.number_format($balance['amount_covered'],2).'. The member already used '.number_format($balance['amount_covered_usage'],2).' out of '.number_format($balance['amount_covered_orig'],2).' and the total amount of the member availment is '.number_format($grand_total,2).' <br>';
        }
        return $return;
    }
    public static function validate_mbl($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $grand_total = 0, $company_id, $date_avail, $availment_id = null)
    {        
        $return = '';
        $balance = Self::member_balance($member_id, $company_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $date_avail, $availment_id);
        if($balance['mbl'] != $grand_total)
        {
            if($grand_total > $balance['mbl'])
            {
                $return = '<br> The remaining balance for maximum benefit limit is '.number_format($balance['mbl'],2).'. The member already used '.number_format($balance['mbl_usage'],2).' out of '.number_format($balance['mbl_orig'],2).' and the total amount of the member availment is '.number_format($grand_total,2).' <br>';
            }
        }
        return $return;
    }
    public static function validate_year_month($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $date_avail, $company_id, $availment_id = null)
    {        
        $return = '';
        $balance = Self::member_balance($member_id, $company_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $date_avail, $availment_id);
        if($balance['limit_per_month'] != 0)
        {
            if($balance['avail_month'] >= $balance['limit_per_month'])
            {
                $return .= '<br> The member already avail '.$balance['avail_month'].' times in the month of '.date('F',strtotime($date_avail)).' and your limit is '.$balance['limit_per_month'].' times per month <br>';
            }
        }
        if($balance['limit_per_year'] != 0)
        {
            if($balance['avail_year'] >= $balance['limit_per_year'])
            {
                $return .= '<br> The member already avail '.$balance['avail_year'].' times in the year of '.date('Y',strtotime($date_avail)).' and your limit is '.$balance['limit_per_year'].' times per year <br>';;
            }
        }
        return $return;
    }
    public static function validate_if_alive($member_id, $coverage_plan_id, $benefit_type_id, $diagnosis_id, $date_avail, $company_id)
    {
        $return = '';
        $balance = Self::member_balance($member_id, $company_id, $coverage_plan_id);

        if($balance['member_is_dead'])
        {
            $return .= '<br> The member already avail Death benefit see approval # '.$balance['death_availment'].'<br>';
        }
        return $return;
    }
    public static function member_balance($member_id, $company_id, $coverage_plan_id, $benefit_type_id = null, $diagnosis_id = null, $date_avail= null, $availment_id = null)
    {
    	$coverage_data = CoveragePlanService::get_coverage_info($coverage_plan_id, $benefit_type_id);
    	$return['amount_covered'] = 0;
    	$return['mbl'] = 0;

        $return['amount_covered_usage'] = 0;
        $return['mbl_usage'] = 0;

        $return['amount_covered_orig'] = 0;
        $return['mbl_orig'] = 0;

        $return['confinement_limit'] = 0;

        $return['avail_year'] = 0;
        $return['avail_month'] = 0;
        
        $return['limit_per_year'] = 0;
        $return['limit_per_month'] = 0;

        $return['member_is_dead'] = false;
        $return['death_availment'] = '';

	    /* CHECK IF PER ILLNESS/DISEASE = 2 OR PER YEAR = 1 */
	    $max_limit_per = $coverage_data['coverage_plan']->max_limit_per;
        $c_diagnosis_id = null;
        if($max_limit_per == 2)
        {
          $c_diagnosis_id = $diagnosis_id;
        } 
        /* USAGE IN MBL*/
    	$mbl_usage = 0;
    	$_availment_mbl = MemberService::get_member_availment($member_id, $company_id, $coverage_plan_id, $c_diagnosis_id, null, null, null,$availment_id);
    	foreach ($_availment_mbl as $key => $value) 
    	{
    		$mbl_usage += $value->grand_total;
    	}

        /* USAGE IN AMOUNT COVERED*/
    	$amount_covered_usage = 0;
        $_availment_am = MemberService::get_member_availment($member_id, $company_id, $coverage_plan_id, $c_diagnosis_id, $benefit_type_id, null, null, $availment_id);
        foreach ($_availment_am as $key => $value) 
        {
            foreach ($value->procedures as $key_procedures => $value_procedures) 
            {
                $amount_covered_usage += $value_procedures->carewell_charged;
            }
        }
        /* IF MEMBER IS DEAD */ 
        $member_is_dead = 0;
        $death_availment = '';
        $death_arr = [];
        $_availment_isdead = MemberService::get_member_availment($member_id, $company_id, $coverage_plan_id);
        foreach ($_availment_am as $key => $value) 
        {
            foreach ($value->procedures as $key_procedures => $value_procedures) 
            {
                if($value_procedures->name == 'ACCIDENTAL DEATH')
                {
                    $member_is_dead++;
                }
                if($value_procedures->name == 'NATURAL DEATH')
                {
                    $member_is_dead++;
                }
            }
            if($member_is_dead > 0)
            {
                $death_arr[$value->approval_id] = $value->approval_id;
            }
        }
        if(count($death_arr) > 0)
        {
            foreach ($death_arr as $key => $value) 
            {
                $death_availment .= $value.". ";
            }
        }

        /* USAGE IN YEAR */
        $_availment_year = MemberService::get_member_availment($member_id, $company_id, $coverage_plan_id, $c_diagnosis_id, $benefit_type_id,date('Y',strtotime($date_avail)),null, $availment_id);
        $return['avail_year'] = count($_availment_year);

        /* USAGE IN MONTH */
        $_availment_month = MemberService::get_member_availment($member_id, $company_id, $coverage_plan_id, $c_diagnosis_id, $benefit_type_id, date('Y',strtotime($date_avail)), date('m',strtotime($date_avail)), $availment_id);
        $return['avail_month'] = count($_availment_month);

    	if($coverage_data['coverage_plan'])
    	{
            $amount_covered = $coverage_data['benefit_data']->covered_amount;
            $return['amount_covered_orig'] = round($coverage_data['benefit_data']->covered_amount,2);
            $return['mbl_orig'] = round($coverage_data['coverage_plan']->maximum_benefit_limit,2);

            $return['amount_covered_usage'] = round($amount_covered_usage,2);
            $return['mbl_usage'] = round($mbl_usage,2);

            $return['amount_covered'] = round($amount_covered - $amount_covered_usage,2);
            $return['mbl'] = round($coverage_data['coverage_plan']->maximum_benefit_limit - $mbl_usage,2);

            $return['confinement_limit'] = round($coverage_data['benefit_data']->per_confinement_amount,2);

            $return['limit_per_year'] = $coverage_data['benefit_data']->limit_per_year;
            $return['limit_per_month'] = $coverage_data['benefit_data']->limit_per_month;

            $return['member_is_dead'] = $member_is_dead > 0 ? true : false;
            $return['death_availment'] = $death_availment;
    	}

        return $return;
    }
}