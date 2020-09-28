<?php
namespace App\Http\Controllers\Uploads\Availment\Validation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Availment;
use App\Models\CoveragePlanBenefit;
use App\Models\CoveragePlanProcedure;
use App\Models\Doctor;
use App\Models\Procedure;
use App\Globals\AvailmentService;
use DB;

class AvailmentImportFormula extends Controller
{
    public static function validateDoctorName($provider_id, $doctor_name)
    {
        $remarks = "";
        $trim_name = [];
        $bool = true;
        $doctor_providers = DB::table('doctors_providers')->where('provider_id', $provider_id)->get();
        foreach($doctor_providers as $key => $value) {
            $doctors = Doctor::where('id', $value->doctor_id)->select('first_name', 'middle_name', 'last_name')->first();
            array_push($trim_name,preg_replace('/\s+/', ' ', $doctors->name));
        }
        foreach($trim_name as $key => $value)
        {
            if($value == $doctor_name)
            {
                $bool = false;
            }
        }
        return $bool;
    }

    public static function amountValidation($data)
    {
        $remarks = "";
        if(!$data['actual_pf_charge'] || $data['actual_pf_charge'] == 0)
        {
            $remarks = "No Actual PF Charges.";
            return $remarks;
        }
        else
        {
            $gross = $data['actual_pf_charge'];
            $phic = $data['phys_phic_charity'] ? $data['phys_phic_charity'] : 0;
            $patient = $data['phys_charged_to_patient'] ? $data['phys_charged_to_patient'] : 0;
            $carewell = $data['phys_charged_to_carewell'] ? $data['phys_charged_to_carewell'] : 0;
            $total = $phic + $patient + $carewell;

            if($gross < $total)
            {
                $remarks = "Cannot be more than Actual PF Charges.";
            }
            else if($gross > $total)
            {
                $remarks = "Cannot be less than Actual PF Charges.";
            }
            return $remarks;
        }
    }

    public static function limitPerAvailmentValidation($availment_date, $limit_per_year, $limit_per_month, $availment_record)
    {
        if($limit_per_year != 0 || $limit_per_month != 0)
        {
            $yearly_limit = $limit_per_year;
            $monthly_limit = $limit_per_month;
            $year_select = date('Y', strtotime($availment_date));
            $month_select = date('m/Y', strtotime($availment_date));
            $month_ctr = 0;
            $year_ctr = 0;
            $remarks = "";

            foreach ($availment_record as $key => $value) {
                $yearly = date('Y', strtotime($value->date_avail));
                $monthly = date('m/Y', strtotime($value->date_avail));

                if($year_select === $yearly)
                {
                    $year_ctr += 1;
                }

                if($month_select === $monthly)
                {
                    $month_ctr += 1;
                }
            }

            if($yearly_limit !== 0 && $yearly_limit > $monthly_limit)
            {
                if($year_ctr >= $yearly_limit)
                {
                    $remarks = "The member already reach the limit of availment per year.";
                    return $remarks;
                }

                if($month_ctr >= $monthly_limit && $monthly_limit !== 0)
                {
                    $remarks = "The member already reach the limit of availment per month.";
                    return $remarks;
                }
            }
            else
            {
                if($monthly_limit !== 0 && $month_ctr >= $monthly_limit)
                {
                    $remarks = "The member already reach the limit of availment per month.";
                    return $remarks;
                }
            }
        }
    }

    public static function innerLimitValidation($request, $procedure_type, $procedure, $coverage_benefit, $availment_record)
    {
        $remarks = "";
        if(!$request['gross_amount'] || $request['gross_amount'] == 0)
        {
            $remarks = "No Gross Amount.";
            return $remarks;
        }
        else
        {
            $gross = $request['gross_amount'];
            $phic = $request['phic_charity'] ? $request['phic_charity'] : 0;
            $patient = $request['charged_to_patient'] ? $request['charged_to_patient'] : 0;
            $carewell = $request['charged_to_carewell'] ? $request['charged_to_carewell'] : 0;
            $others = $request['charged_to_others'] ? $request['charged_to_others'] : 0;
            $total = $phic + $patient + $carewell + $others;
            $availmentAmount = 0;

            foreach($availment_record as $key => $value)
            {
                $procedureData = $value->availment_procedure;
                foreach($procedureData as $index => $array)
                {
                    if($array->procedures_id == $procedure['id'])
                    {
                        $availmentAmount += $array->carewell_charged;
                    }
                }
            }

            if($gross < $total)
            {
                $remarks = "Cannot be more than Gross Amount.";
            }
            else if($gross > $total)
            {
                $remarks = "Cannot be less than Gross Amount.";
            }
            else
            {
                $inner_limit = CoveragePlanProcedure::where([
                    'procedure_type_id'=>$procedure_type['id'],
                    'procedure_id'=>$procedure['id'],
                    'coverage_plan_benefit_id'=>$coverage_benefit['id']
                ])->first();

                $procedure_name = $procedure->name;
                    
                $procedure_carewell = $request['charged_to_carewell'];
                $allTotal = $availmentAmount + $procedure_carewell;
                $inner_amount_limit = $inner_limit->amount ? $inner_limit->amount : 0;
                $innerBalance = $inner_amount_limit != 0 ? $inner_amount_limit - $availmentAmount : 0;
                // return $procedure_carewell.','.$allTotal.','.$inner_amount_limit.','.$innerBalance;
                if($inner_amount_limit < $allTotal && $inner_amount_limit != 0)
                {
                    $remarks ="The remaining balance for ".$procedure_name." is ".
                    number_format($innerBalance,2).".\r\n The member already used ".
                    number_format($availmentAmount,2)." out of ".number_format($inner_amount_limit,2).
                    " and the amount for this procedure is ".number_format($procedure_carewell,2);
                }
            }
            return $remarks;
        }
    }

    public static function amountCoveredValidation($availment_date, $charged_to_carewell, $availment_record, $coverage_benefit, $coverage_plan, $benefit_id, $diagnosis)
    {
        $year_selected = date('Y', strtotime($availment_date));
        $yearly = '';
        $availment_total = 0;
        $benefit_amount = 0;
        $current_amount = 0;
        $current_balance = 0;
        
        foreach ($availment_record as $key => $value) {
            if($value->benefit_type_id == $benefit_id)
            {
                $benefit_amount += $value->grand_total;
            }
            $availment_total += $value->grand_total;
            $yearly = date('Y', strtotime($value->date_avail));
        }

        $deduction = $availment_total - $benefit_amount;
        $covered_amount = $coverage_benefit['covered_amount'];
        $maximum_benefit_limit = $coverage_plan['maximum_benefit_limit'];
        $remaining_balance = $maximum_benefit_limit - $deduction;
        $confinement_amount = $coverage_benefit['per_confinement_amount'];
        $current_balance = self::forCurrentBalance($maximum_benefit_limit, $covered_amount, $remaining_balance, $benefit_amount, $year_selected, $yearly, $coverage_plan, $diagnosis);

        if($benefit_id != 7)
        {
            if($charged_to_carewell > $covered_amount)
            {
                $remarks = "The Amount Covered to be Charged to Carewell must not exceed to ".number_format($covered_amount, 2)."!";
                return $remarks;
            }

            if($charged_to_carewell > $current_balance)
            {
                $remarks = "The total amount exceed to the balance of the member, The current balance is ".number_format($current_balance, 2).".";
                return $remarks;
            }
        }
        else
        {
            if($charged_to_carewell > $confinement_amount && $confinement_amount != 0)
            {
                $remarks = "The Per Confinement to be Charged to Carewell must not exceed to ".number_format($covered_amount, 2)."!";
                return $remarks;
            }

            if($charged_to_carewell > $covered_amount)
            {
                $remarks = "The Amount Covered to be Charged to Carewell must not exceed to ".number_format($covered_amount, 2)."!";
                return $remarks;
            }

            if($charged_to_carewell > $current_balance)
            {
                $remarks = "The total amount exceed to the balance of the member, The current balance is ".number_format($current_balance, 2).".";
                return $remarks;
            }
        }
    }

    public static function forCurrentBalance($mbl, $covered, $remaining, $benefit_amount, $year_selected, $yearly, $coverage_plan, $diagnosis)
    {
        $current_amount = 0;
        //set current amount
        if($mbl == $covered && $year_selected == $yearly)
        {
            $current_amount = $remaining;
        }
        else if(($covered > $remaining && $remaining < $benefit_amount) && $year_selected == $yearly)
        {
            $current_amount = $covered;
        }
        else if(($covered > $remaining && $remaining > $benefit_amount) && $year_selected == $yearly)
        {
            $current_amount = $remaining;
        }
        else
        {
            $current_amount = $covered;
        }
        //get current balance
        if($coverage_plan['max_limit_per'] == "1")
        {
            if($year_selected == $yearly)
            {
                $current_balance = $current_amount - $benefit_amount;
            }
            else if($year_selected != $yearly)
            {
                $current_balance = $current_amount;
            }
            else
            {
                $current_balance = 0;
            }
        }
        else if($coverage_plan['max_limit_per'] == "2")
        {
            if($diagnosis['id'] != null)
            {
                $current_balance = $current_amount - $benefit_amount;
            }
            else
            {
                $current_balance = $current_amount;
            }
        }
        else 
        {
            $current_balance = 0;
        }
        return $current_balance;
    }

    public static function forProcedures($input, $procedure_type, $coverage_plan, $benefit_id, $member_id, $company_id, $diagnosis)
    {
        //validate Procedure Name and Procedure Type 
        $procedure = Procedure::where([
            'name' => $input['procedures'],
            'procedure_type_id' => $procedure_type['id']
        ])->first();
        $coverage_benefit = CoveragePlanBenefit::where([
            'coverage_plan_id' => $coverage_plan['id'],
            'benefit_type_id' => $benefit_id
        ])->first();
        //save if doesn't exist
        if(!$procedure)
        {
            $model_procedure = new Procedure();
            $procedure = $model_procedure->create(['name'=>$input['procedures'],
                'procedure_type_id'=>$procedure_type['id']]);

            $model_coverage_procedure = new CoveragePlanProcedure();
            $newCoverageProcedure = $model_coverage_procedure->create([
                'procedure_type_id'=>$procedure_type['id'],
                'procedure_id'=>$procedure->id,
                'coverage_plan_benefit_id'=>$coverage_benefit->id,
                'amount'=> 0]);
        }
        
        //get availment record
        $availment_record = Availment::with('availment_procedure')->where(['member_id' => $member_id,
        'company_id' => $company_id,
        'coverage_plan_id' => $coverage_plan['id']])->get();
        //validation of death
        $deathLimit = AvailmentService::member_balance($member_id, $company_id, $coverage_plan['id']);
        if($deathLimit['member_is_dead'])
        {
            $remarks = 'The member already avail Death benefit see approval # '.$deathLimit['death_availment'].'.';
            return $remarks;
        }
        //validation of limits
        $limitPerAvailment = Self::limitPerAvailmentValidation($input['availment_date'], $coverage_benefit->limit_per_year, $coverage_benefit->limit_per_month, $availment_record);
        if($limitPerAvailment)
        {
            return $limitPerAvailment;
        }
        //validation of inner limit
        $innerLimit = Self::innerLimitValidation($input, $procedure_type, $procedure, $coverage_benefit, $availment_record);
        if($innerLimit)
        {
            return $innerLimit;
        }
        //validation of amount covered
        $covered = Self::amountCoveredValidation($input['availment_date'], $input['charged_to_carewell'], $availment_record, $coverage_benefit, $coverage_plan, $benefit_id, $diagnosis);
        if($covered)
        {
            return $covered; 
        }
    }

    public static function computeDate($date)
    {
        $import_date = $date;

        $test_date = date('m/d/Y', strtotime($date));
        //check even the $date is manipulated it is still the same as $import_date
        if($import_date == $test_date)
        {
            $convert_date = date('Y-m-d', strtotime($date));

            return $convert_date;
        }
        return null;
    }

    public static function validateDate($date, $column)
    {
        if(date('Y',strtotime($date)) < 1950)
        {
            return "Invalid ".$column." Year.";
        }
        return null;
    }
}