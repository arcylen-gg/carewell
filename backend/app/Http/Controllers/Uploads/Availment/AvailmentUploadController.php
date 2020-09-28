<?php
namespace App\Http\Controllers\Uploads\Availment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Uploads\Availment\Validation\AvailmentImportValidation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Availment;
use App\Models\AvailmentProcedure;
use App\Models\AvailmentDoctor;
use App\Models\BenefitType;
use App\Models\Company;
use App\Models\CompanyCoveragePlan;
use App\Models\CompanyDeployment;
use App\Models\CoveragePlan;
use App\Models\DiagnosisList;
use App\Models\Doctor;
use App\Models\Member;
use App\Models\ProcedureType;
use App\Models\Procedure;
use App\Models\Provider;
use App\Models\ProviderPayee;
use App\Models\Specialization;
use App\Exports\AvailmentExport;
use App\Imports\AvailmentImport;
use PDF;
use Excel;
use Validator;
use DB;

class AvailmentUploadController extends Controller
{
    public function export(Request $request, Availment $model)
    {
        $data['data'] = $model->with([
            'member',
            'provider',
            'benefit_type',
            'diagnosis',
            'availment_procedure.procedure',
            'availment_doctor.doctor',
            'availment_doctor.procedure',
            'availment_doctor.specialization',
            'provider_payee',
            'doctor'
        ])->find($request->id);
        // return $data['data'];
       

        // $getData = $model->with([
        //     'company',
        //     'payment_mode',
        //     'payment_term',
        //     'cal_member.member'
        // ])->find($request->id);

        // // return $getData['reference_number'];

        // $data['data']['reference_number']       = $getData['reference_number'];
        // $data['data']['payment_cal_date']       = $getData['payment_cal_date'];
        // $data['data']['payment_due_date']       = $getData['payment_due_date'];
        // $data['data']['payment_start_date']     = $getData['payment_start_date'];
        // $data['data']['payment_end_date']       = $getData['payment_end_date'];
        // $data['data']['company']['name']        = $getData['company']['name'];
        // $data['data']['payment_mode']['name']   = $getData['payment_mode']['name'];
        // $data['data']['payment_term']['name']   = $getData['payment_term']['name'];

        // foreach ($getData->cal_member as $key => $value) {
        //     $data['data']['cal_member'][$key]['member']['company']['universal_id'] = $value->member->company->universal_id;
        //     $data['data']['cal_member'][$key]['member']['last_name'] = $value->member->last_name;
        //     $data['data']['cal_member'][$key]['member']['first_name'] = $value->member->first_name;
        //     $data['data']['cal_member'][$key]['member']['middle_name'] = $value->member->middle_name;
        //     $data['data']['cal_member'][$key]['member']['birth_date'] = $value->member->birth_date;
        //     $data['data']['cal_member'][$key]['member']['company']['coverage_plan']['name'] = $value->member->company->coverage_plan->name;
        //     $data['data']['cal_member'][$key]['member']['company']['company_deployment']['name'] = $value->member->company->company_deployment->name;
        //     $data['data']['cal_member'][$key]['paid_amount'] = $value->paid_amount;
        // }
        
        return view('exports.availment.availment_export_print', $data);


        // $pdf = PDF::loadView('exports.cal.cal_export_print',$data)->setPaper('A4', 'landscape')->setWarnings(false);
        // return $pdf->stream();
    }

    public function import(Request $request)
    {
        $remarks = '';
        $color = 'red';
        
        $messages = [
            'approval_id.required'          => 'Approval ID is required.',
            'caller_name.required'          => 'Caller Name is required.',
            'caller_date.required'          => 'Caller Date is required.',
            'first_name.required'           => 'First Name is required.',
            'last_name.required'            => 'Last Name is required.',
            'universal_id.required'         => 'Universal ID is required.',
            'carewell_id.required'          => 'Carewell ID is required.',
            'company.required'              => 'Company is required.',
            'deployment.required'           => 'Deployment is required.',
            'coverage_plan.required'        => 'Coverage Plan is required.',
            'provider.required'             => 'Provider is required.',
            'benefit_type.required'         => 'Benefit Type is required.',
            'availment_date.required'       => 'Availment Date is required.',
            'plan_description.required'     => 'Plan Description is required.',
            'disapproved.required'          => 'Disapproved is required.',
        ];

        $validator = Validator::make($request->all(), [
			'approval_id'           => 'required',
            'caller_name'           => 'required',
            'caller_date'           => 'required',
            'first_name'            => 'required',
            'last_name'             => 'required',
            'universal_id'          => 'required',
            'carewell_id'           => 'required',
            'company'               => 'required',
            'deployment'            => 'required',
            'coverage_plan'         => 'required',
            'provider'              => 'required',
            'benefit_type'          => 'required',
            'availment_date'        => 'required',
            'plan_description'      => 'required',
            'disapproved'           => 'required',
        ], $messages);
        
        if($validator->fails())
        {
            $errors = $validator->errors();
            return Self::forArray($errors->first(),$color,$request->all());
        }
        else
        {
            $return_data = AvailmentImportValidation::validate_availment($request);
            if($return_data)
            {
                return Self::forArray($return_data, $color, $request->all());
            }
            else
            {
                return Self::forArray("[".$request->approval_id."] Availment Added.", 'green', $request->all());
            }
        }
    }

    public function save_import(Request $request)
    {
        $benefit_type = BenefitType::where('name', $request->availmentInformation['benefit_type'])->first();
        $company = Company::where('name', $request->memberInformation['company'])->first();
        $coverage_plan = CoveragePlan::where('name', $request->coverage_plan)->first();
        $diagnosis = DiagnosisList::where('name',$request->availmentInformation['diagnosis'])->first();
        $member = Member::where(['first_name'=> $request->memberInformation['first_name'],
            'middle_name'=> $request->memberInformation['middle_name'] ?? null,
            'last_name'=> $request->memberInformation['last_name']])->first();
        $procedure_type = ProcedureType::where('name',$request->procedure_type)->first();
        $provider = Provider::where('name', $request->availmentInformation['provider'])->first();
        $provider_payee = ProviderPayee::where([
            'name' => $request->payeeInformation['provider_payee'],
            'provider_id' => $provider->id
        ])->first();
        // return $request->payeeInformation['doctor'] ? Self::getDoctorID($provider->id, $request->payeeInformation['doctor']) : null;
        $availment = new Availment();
        $availment->approval_id = $request->approval_id;
        $availment->company_id = $company->id;
        $availment->member_id = $member->id;
        $availment->carewell_id = $request->memberInformation['carewell_id'];
        $availment->provider_id = $provider->id;
        $availment->coverage_plan_id = $coverage_plan->id;
        $availment->benefit_type_id = $benefit_type->id;
        $availment->date_avail = Self::computeDate($request->availmentInformation['date_avail']);
        $availment->discharged_date = $request->availmentInformation['discharged_date'] ? Self::computeDate($request->availmentInformation['discharged_date']) : null;
        $availment->chief_complaint = $request->availmentInformation['chief_complaint'];
        $availment->initial_diagnosis = $request->availmentInformation['initial_diagnosis'];
        $availment->final_diagnosis = $request->availmentInformation['final_diagnosis'];
        $availment->diagnosis_id = $diagnosis ? $diagnosis->id : null;
        $availment->provider_payee_id = $provider_payee->id;
        $availment->doctor_id = $request->payeeInformation['doctor'] ? Self::getDoctorID($provider->id, $request->payeeInformation['doctor']) : null;
        $availment->provider_payee_fee = $request->payeeInformation['hospital_fee'];
        $availment->doctor_fee = $request->payeeInformation['professional_fee'];
        $availment->caller_name = $request->callerInformation['name'];
        $availment->caller_position = $request->callerInformation['position'] ? $request->callerInformation['position'] : null;
        $availment->caller_date = Self::computeDate($request->callerInformation['date']);
        $availment->caller_contact_number = $request->callerInformation['contact_number'] ? $request->callerInformation['contact_number'] : null;
        $availment->procedures_gross_amount_total = Self::getAvailmentTotal('procedure_gross', $request->all());
        $availment->procedures_phic_amount_total = Self::getAvailmentTotal('procedure_phic', $request->all());
        $availment->procedures_patient_charged_total = Self::getAvailmentTotal('procedure_patient', $request->all());
        $availment->procedures_carewell_charged_total = Self::getAvailmentTotal('procedure_carewell', $request->all());
        $availment->procedures_charge_other_total = Self::getAvailmentTotal('procedure_others', $request->all());
        $availment->doctors_gross_amount_total = Self::getAvailmentTotal('physician_actual_pf', $request->all());
        $availment->doctors_phic_amount_total = Self::getAvailmentTotal('physician_phic', $request->all());
        $availment->doctors_patient_charged_total = Self::getAvailmentTotal('physician_patient', $request->all());
        $availment->doctors_carewell_charged_total = Self::getAvailmentTotal('physician_carewell', $request->all());
        $availment->grand_total = Self::getAvailmentTotal('grand_total', $request->all());
        $availment->save();

        foreach($request->procedureInformation as $key => $value)
        {
            $procedure = Procedure::where([
                'name' => $value['procedures'],
                'procedure_type_id' => $procedure_type->id
            ])->first();

            $availment_procedure                    = new AvailmentProcedure();
            $availment_procedure->availment_id      = $availment->id;
            $availment_procedure->procedures_id     = $value['procedures'] ? $procedure->id : null;
            $availment_procedure->gross_amount      = $value['gross_amount'];
            $availment_procedure->phic_charity_swa  = $value['phic_charity'];
            $availment_procedure->patient_charged   = $value['charged_to_patient'];
            $availment_procedure->carewell_charged  = $value['charged_to_carewell'];
            $availment_procedure->charge_other      = $value['charged_to_others'];
            $availment_procedure->remarks           = $value['procedure_remarks'];
            $availment_procedure->is_disapproved    = $value['disapproved'];
            $availment_procedure->save();
        }

        
        foreach($request->physicianInformation as $key => $value)
        {
            $procedure = Procedure::where([
                'name' => $value['procedure'],
                'procedure_type_id' => $procedure_type->id
            ])->first();

            $specialize = Specialization::where('name',$value['specialization'])->first();

            $availment_doctor                       = new AvailmentDoctor();
            $availment_doctor->availment_id         = $availment->id;
            $availment_doctor->doctor_id            = $value['physician'] ? Self::getDoctorID($provider->id, $value['physician']) : null ;
            $availment_doctor->specialization_id    = $value['specialization'] ? $specialize->id : null;
            $availment_doctor->rate_rvs             = $value['rate_rvs'] ? $value['rate_rvs'] : null;
            $availment_doctor->procedures_id        = $value['procedure'] ? $procedure->id : null;
            $availment_doctor->doctors_fee          = $value['actual_pf_charge'] ? $value['actual_pf_charge'] : 0;
            $availment_doctor->phic_charity_swa     = $value['phys_phic_charity'] ? $value['phys_phic_charity'] : 0;
            $availment_doctor->patient_charged      = $value['phys_charged_to_patient'] ? $value['phys_charged_to_patient'] : 0;
            $availment_doctor->carewell_charged     = $value['phys_charged_to_carewell'] ? $value['phys_charged_to_carewell'] : 0;
            $availment_doctor->save();
        }
    }
    
    public function export_template(Request $request)
    {
        $coverage_plan_list = [];
        $company = Company::where('id',$request->company_id)->first();
        $deployment = CompanyDeployment::where('company_id', $request->company_id)->select('name')->get();
        $coverage_plan = CompanyCoveragePlan::where('company_id', $request->company_id)->select('coverage_plan_id')->get();
        foreach($coverage_plan as $key=>$value)
        {
            $coverage = CoveragePlan::where('id',$value['coverage_plan_id'])->first();
            array_push($coverage_plan_list,$coverage->name);
        }
        $benefit = BenefitType::all();
        $availmentData['company'] = $company;
        $availmentData['deployment'] = $deployment;
        $availmentData['benefit'] = $benefit;
        $availmentData['coverage_plan'] = $coverage_plan_list;
        return Excel::download(new AvailmentExport($availmentData),'Availment_Template.xlsx');
    }

    public function forArray($remarks, $color, $request)
	{
        $empty = '';
        return [
            'remarks'                   => $remarks,
            'color'                     => $color,
            'approval_id'               => $request['approval_id'] ?? $empty,
            'caller_name'               => $request['caller_name'] ?? $empty,
            'caller_position'           => $request['caller_position'] ?? $empty,
            'caller_contact_number'     => $request['caller_contact_number'] ?? $empty,
            'caller_date'               => $request['caller_date'] ?? $empty,
            'first_name'                => $request['first_name'] ?? $empty,
            'middle_name'               => $request['middle_name'] ?? $empty,
            'last_name'                 => $request['last_name'] ?? $empty,
            'universal_id'              => $request['universal_id'] ?? $empty,
            'carewell_id'               => $request['carewell_id'] ?? $empty,
            'company'                   => $request['company'] ?? $empty,
            'deployment'                => $request['deployment'] ?? $empty,
            'coverage_plan'             => $request['coverage_plan'] ?? $empty,
            'provider'                  => $request['provider'] ?? $empty,
            'benefit_type'              => $request['benefit_type'] ?? $empty,
            'availment_date'            => $request['availment_date'] ?? $empty,
            'discharged_date'           => $request['discharged_date'] ?? $empty,
            'chief_complaint'           => $request['chief_complaint'] ?? $empty,
            'initial_diagnosis'         => $request['initial_diagnosis'] ?? $empty,
            'final_diagnosis'           => $request['final_diagnosis'] ?? $empty,
            'charge_to'                 => $request['charge_to'] ?? $empty,
            'plan_description'          => $request['plan_description'] ?? $empty,
            'procedures'                => $request['procedures'] ?? $empty,
            'gross_amount'              => $request['gross_amount'] ?? 0,
            'phic_charity'              => $request['phic_charity'] ?? 0,
            'charged_to_patient'        => $request['charged_to_patient'] ?? 0,
            'charged_to_carewell'       => $request['charged_to_carewell'] ?? 0,
            'charged_to_others'         => $request['charged_to_others'] ?? 0,
            'procedure_remarks'         => $request['procedure_remarks'] ?? $empty,
            'disapproved'               => $request['disapproved'] ?? $empty,
            'physician'                 => $request['physician'] ?? $empty,
            'specialization'            => $request['specialization'] ?? $empty,
            'rate_rvs'                  => $request['rate_rvs'] ?? $empty,
            'procedure'                 => $request['procedure'] ?? $empty, 
            'actual_pf_charge'          => $request['actual_pf_charge'] ?? 0,
            'phys_phic_charity'         => $request['phys_phic_charity'] ?? 0,
            'phys_charged_to_patient'   => $request['phys_charged_to_patient'] ?? 0,
            'phys_charged_to_carewell'  => $request['phys_charged_to_carewell'] ?? 0,
            'provider_payee'            => $request['provider_payee'] ?? $empty,
            'hospital_fee'              => $request['hospital_fee'] ?? 0,
            'doctor'                    => $request['doctor'] ?? $empty,
            'professional_fee'          => $request['professional_fee'] ?? 0
        ];
    }
    
    public function computeDate($date)
    {
        $import_date = $date;

        $test_date = date('m/d/Y', strtotime($date));
        //check even the $date is manipulated it is still the same as $import_date
        if($import_date == $test_date)
        {
            $convert_date = date( 'Y-m-d', strtotime($date) );

            return $convert_date;
        }
        return null;
    }

    public function getDoctorID($provider_id, $doctor_name)
    {
        $doctor = '';
        $doctor_providers = DB::table('doctors_providers')->where('provider_id', $provider_id)->get();

        foreach ($doctor_providers as $key => $value) {
            $doctors = Doctor::where('id', $value->doctor_id)->select('first_name', 'middle_name', 'last_name')->first();
            $trim_name = preg_replace('/\s+/', ' ', $doctors->name);
            if($trim_name == $doctor_name)
            {
               $doctor = $value->doctor_id;
            }
        }
        return $doctor;
    }

    public function getAvailmentTotal($type, $data)
    {
        $total_amount =  0;
        if($type == "procedure_gross")
        {
           foreach($data['procedureInformation'] as $key => $value)
           {
               $total_amount += $value['gross_amount'];
           }
           return $total_amount;
        }

        if($type == "procedure_phic")
        {
            foreach($data['procedureInformation'] as $key => $value)
            {
                $total_amount += $value['phic_charity'];
            }
            return $total_amount;
        }

        if($type == "procedure_patient")
        {
            foreach($data['procedureInformation'] as $key => $value)
            {
                $total_amount += $value['charged_to_patient'];
            }
            return $total_amount;
        }

        if($type == "procedure_carewell")
        {
            foreach($data['procedureInformation'] as $key => $value)
            {
                $total_amount += $value['charged_to_carewell'];
            }
            return $total_amount;
        }

        if($type == "procedure_others")
        {
            foreach($data['procedureInformation'] as $key => $value)
            {
                $total_amount += $value['charged_to_others'];
            }
            return $total_amount;
        }

        if($type == "physician_actual_pf")
        {
            foreach($data['physicianInformation'] as $key => $value)
            {
                $total_amount += $value['actual_pf_charge'];
            }
            return $total_amount;
        }

        if($type == "physician_phic")
        {
            foreach($data['physicianInformation'] as $key => $value)
            {
                $total_amount += $value['phys_phic_charity'];
            }
            return $total_amount;
        }

        if($type == "physician_patient")
        {
            foreach($data['physicianInformation'] as $key => $value)
            {
                $total_amount += $value['phys_charged_to_patient'];
            }
            return $total_amount;
        }

        if($type == "physician_carewell")
        {
            foreach($data['physicianInformation'] as $key => $value)
            {
                $total_amount += $value['phys_charged_to_carewell'];
            }
            return $total_amount;
        }

        if($type == "grand_total")
        {
            $amount_procedure = 0;
            foreach($data['procedureInformation'] as $key => $value)
            {
                $amount_procedure += $value['charged_to_carewell'];
            }

            $amount_physician = 0;
            foreach($data['physicianInformation'] as $key => $value)
            {
                $amount_physician += $value['phys_charged_to_carewell'];
            }

            $total_amount = $amount_procedure + $amount_physician;
            return $total_amount;
        }
    }
}