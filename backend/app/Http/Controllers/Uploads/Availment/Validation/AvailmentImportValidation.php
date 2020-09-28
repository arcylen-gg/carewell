<?php
namespace App\Http\Controllers\Uploads\Availment\Validation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Availment;
use App\Models\BenefitType;
use App\Models\Company;
use App\Models\CompanyCoveragePlan;
use App\Models\CoveragePlan;
use App\Models\CoveragePlanBenefit;
use App\Models\DiagnosisList;
use App\Models\Member;
use App\Models\MemberCompany;
use App\Models\Provider;
use App\Models\ProviderPayee;
use App\Models\ProcedureType;
use App\Models\Specialization;
use App\Http\Controllers\Uploads\Availment\Validation\AvailmentImportFormula;
use DB;

class AvailmentImportValidation extends Controller
{
    public static function validate_availment($input)
    {
        $remarks = "";
        //check Approval ID
        $availment = Availment::where('approval_id', $input->approval_id)->first();
        if($availment)
        {
            $remarks = "Approval ID is already exist.";
            return $remarks;
        }
        //check Member Name
        $member = Member::where(['first_name'=> $input->first_name,
            'middle_name'=> $input->middle_name ?? null,
            'last_name'=> $input->last_name,])->first();
        if(!$member)
        {
            $remarks = "Member doesn't exist.";
            return $remarks;
        }
        if($member->is_archived !== 0)
        {
            $remarks = "Member is currently inactive.";
            return $remarks;
        }
        //check Member Universal ID
        $company = Company::where('name', $input->company)->first();
        $member_coverage = MemberCompany::where(['member_id'=> $member->id,
            'company_id'=>$company->id])->first();
        if(!$member_coverage->universal_id)
        {
            $remarks = "Member doesn't have birthdate.";
            return $remarks;
        }
        else
        {
            if($input->universal_id != $member_coverage->universal_id)
            {
                $remarks = "Invalid Universal ID.";
                return $remarks;
            }
        }
        //check Member Carewell ID
        if(!$member_coverage->carewell_id)
        {
            $remarks = "Member doesn't have Carewell ID.";
            return $remarks;
        }
        else
        {
            if($input->carewell_id != $member_coverage->carewell_id)
            {
                $remarks = "Invalid Carewell ID.";
                return $remarks;
            }
        }
        //check Coverage Plan
        $coverage_plan = CoveragePlan::where('name',$input->coverage_plan)->first();
        if(!$coverage_plan)
        {
            $remarks = "Coverage Plan doesn't exist.";
            return $remarks;
        }
        //check Provider Name
        $provider = Provider::where('name', $input->provider)->first();
        if(!$provider)
        {
            $remarks = "Provider doesn't exist.";
            return $remarks;
        }
        //check if Benefit Type exist in Coverage Plan
        $benefit = BenefitType::where('name', $input->benefit_type)->first();
        $coverage_plan_benefits = CoveragePlanBenefit::where([
            'coverage_plan_id' => $coverage_plan->id,
            'benefit_type_id' => $benefit->id
        ])->first();
        if(!$coverage_plan_benefits)
        {
            $remarks = "Benefit Type doesn't exist in Coverage Plan.";
            return $remarks;
        }
        //validate if Benefit Type is Outpatient Consultation
        if($benefit->id != 2)
        {
            if(!$input->chief_complaint)
            {
                $remarks = "Chief Complaint is required.";
                return $remarks;
            }

            if(!$input->initial_diagnosis)
            {
                $remarks = "Initial Diagnosis is required.";
                return $remarks;
            }

            if(!$input->final_diagnosis)
            {
                $remarks = "Final Diagnosis is required.";
                return $remarks;
            }

            if(!$input->charge_to)
            {
                $remarks = "Charge to is required.";
                return $remarks;
            }
            else
            {
                $diagnosis = DiagnosisList::where('name', $input->charge_to)->first();
                if(!$diagnosis)
                {
                    $remarks = "Diagnosis doesn't exist.";
                    return $remarks;
                }
            }
        }
        else
        {
            if($input->charge_to)
            {
                $diagnosis = DiagnosisList::where('name', $input->charge_to)->first();
                if(!$diagnosis)
                {
                    $remarks = "Diagnosis doesn't exist.";
                    return $remarks;
                }
            }
            else
            {
                $diagnosis = null;
            }
        }
        //check procedure type exist then save doesn't exist
        $procedure_type = ProcedureType::where('name',$input->plan_description)->first();
        if(!$procedure_type)
        {
            $model_procedure_type = new ProcedureType();
            $procedure_type = $model_procedure_type->create(['name'=>$input->plan_description]);
        }
        //validate if procedures & gross amount exist
        if($input->procedures && !$input->gross_amount)
        {
            $remarks = "Gross Amount is required.";
            return $remarks;
        }
        else if(!$input->procedures && $input->gross_amount)
        {
            $remarks = "Procedures is required.";
            return $remarks;
        }

        //get Procedures
        if($input->procedures)
        {
            $procedure_validation = AvailmentImportFormula::forProcedures($input->all(),$procedure_type,$coverage_plan,$benefit->id,$member->id,$company->id, $diagnosis);
            if($procedure_validation)
            {
                return $procedure_validation;
            }
        }
        if($input->physician && !$input->actual_pf_charge)
        {
            $remarks = "Actual PF Charge is required.";
            return $remarks;
        }
        else if(!$input->physician && $input->actual_pf_charge)
        {
            $remarks = "Physician is required.";
            return $remarks;
        }
        //get Doctor
        if($input->physician)
        {
            //validation of doctor
            $physician_name = AvailmentImportFormula::validateDoctorName($provider->id, $input->physician);
            if($physician_name)
            {
                $remarks = "Physician doesn't exist.";
                return $remarks;
            }
            //validation of specialization
            if($input->specialization)
            {
                $specialize = Specialization::where('name', $input->specialization)->first();
                if(!$specialize)
                {
                    $remarks = "Specialization doesn't exist.";
                    return $remarks;
                }
            }
            //validation of amount
            $amount_notification = AvailmentImportFormula::amountValidation($input->all());
            if($amount_notification)
            {
                return $amount_notification;
            }
        }

        $payee = ProviderPayee::where(['provider_id'=> $provider->id, 'name'=> $input->provider_payee])->first();
        if(!$payee)
        {
            $remarks = "Provider Payee doesn't exist.";
            return $remarks;
        }

        if($payee && !$input->hospital_fee)
        {
            if($input->hospital_fee != $input->charged_to_carewell)
            {
                $remarks = "Hospital Fee is required.";
                return $remarks;
            } 
        }
        else
        {
            if($input->hospital_fee != $input->charged_to_carewell)
            {
                $remarks = "Invalid Hospital Fee Amount.";
                return $remarks;
            }
        }

        if($input->doctor)
        {
            $doctor = AvailmentImportFormula::validateDoctorName($provider->id, $input->doctor);
            if($doctor)
            {
                $remarks = "Doctor doesn't exist.";
                return $remarks;
            }
        }
        if($input->physician && !$input->doctor)
        {
            $remarks = "Doctor is required.";
            return $remarks;
        }

        if($input->doctor && !$input->professional_fee)
        {
            $remarks = "Professional Fee is required.";
            return $remarks;
        }
        else
        {
            if($input->professional_fee != $input->phys_charged_to_carewell)
            {
                $remarks = "Invalid Professional Fee Amount.";
                return $remarks;
            }
        }
        //check date cell format
        $caller_date = AvailmentImportFormula::computeDate($input->caller_date);
        if(!$caller_date)
        {
            $remarks = "Invalid Caller Date Value.";
            return $remarks;
        }
        else
        {
            //validate if year is less than 1900
            $remarks = AvailmentImportFormula::validateDate($caller_date, 'Caller');
            if($remarks)
            {
                return $remarks;
            }
        }
        $availment_date = AvailmentImportFormula::computeDate($input->availment_date);
        if(!$availment_date)
        {
            $remarks = "Invalid Availment Date Value.";
            return $remarks;
        }
        else
        {
            $remarks = AvailmentImportFormula::validateDate($availment_date, 'Availment');
            if($remarks)
            {
                return $remarks;
            }
        }

        $discharged_date = AvailmentImportFormula::computeDate($input->discharged_date);
        if(!$discharged_date)
        {
            $remarks = "Invalid Discharged Date Value.";
            return $remarks;
        }
        else
        {
            $remarks = AvailmentImportFormula::validateDate($discharged_date, 'Availment');
            if($remarks)
            {
                return $remarks;
            }
        }
        //if procedures is empty, validate physician procedure
        if(!$input->procedures && $input->physician && !$input->procedure)
        {
            $remarks = "Procedure in Physician is empty.";
            return $remarks;
        } 
    }
}