<?php

namespace App\Http\Controllers;

use App\Models\Availment;
use App\Models\AvailmentDoctor;
use App\Models\AvailmentProcedure;
use App\Globals\AuditTrailService;
use App\Globals\AvailmentService;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Schema;

class AvailmentController extends BaseController
{
    private $model;
    public function __contruct(Availment $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){ 
        $avail_model = new Availment();
        // $this->model = $avail_model->with(['member','availment_doctor.doctor','availment_procedure.procedure']); //lazy loader
        $this->model = $avail_model->with(['member.testing','provider']); //lazy loader
    }
    public function show(Request $request, $id)
    {
        $avail_model = new Availment();
        $this->model = $avail_model->with(['member','benefit_type','diagnosis','provider','availment_procedure.procedure','availment_doctor.doctor', 'coverage_plan.coverage_plan_benefits']);
        return $this->model->find($id);
    }
    public function index(Request $request)
    {
        $this->loadWithRelatedModel();
        
        $company_id = $request->company_id;
        $limit = isset($request->limit) ? $request->limit  : 5;
        $name = $request->name;
        $page = $request->page;
        $provider_id = $request->provider_id;
        $status = $request->status;

        if($name){
            $this->model->where('approval_id', 'LIKE', "%{$name}%")
            ->orWhere('carewell_id', 'LIKE', "%{$name}%")
            ->orWhereHas('member', function($member_query) use ($name){
                $member_query->where('first_name', 'LIKE', "%{$name}%")
                ->orWhere('last_name', 'LIKE', "%{$name}%")
                ->orWhereHas('testing', function($company_query) use ($name){
                    $company_query->where('universal_id', 'LIKE', "%{$name}%");
                });
            });
        }

        if($company_id !== "all"){
            $this->model->where('company_id', $company_id);
        }

        if($provider_id !== "all"){
            $this->model->where('provider_id', $provider_id);
        }

        $this->model->where('status', $status);

        return $this->getResultList($this->model->latest());
    }

    public function filterTable()
    { 
        $related_fields             = ["member_company_universal_id"];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name","approval_id"],[["company_id","company_id"],["provider_id","provider_id"]]];

        // $inexact_fields             = ["name","company_id","provider_id","date_from","date_to"]; 
        // $search_inexact_fields      = [["name","approval_id"],[["company_id","company_id"],["provider_id","provider_id"]]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function store(Request $request)
    {
        $ret = AvailmentService::validate_member($request);
        if($ret)
        {
            $code = 300;
            $data['status_message'] = $ret;
            $data['return_status'] = 'error';
            return json_encode($data);
        }

        // return $request->procedureInformation["procedure_list"][0]['procedure']['id'];
        $availmodel = new Availment();
        // $validator = $this->validateRequestInput($request->all(), $availmodel->requestRules());

        $approval_number = '';
        $availment_date = date_format(date_create($request->date_avail),"md");
        $availmodel->setId(array(
            'date_avail' => $availment_date,
        ));
        $approval_number = $availmodel->modelAction('generateApprovalId');
        // if($validator->fails())
        // {
        //     $message = $validator->errors()->first();
        //     $code = 300;
        // }
        // else
        // {
            
            $availment                                      = new Availment();
            $availment->approval_id                         = $approval_number;
            $availment->member_id                           = $request->memberInformation['member']['id'];
            $availment->company_id                          = $request->memberInformation['member']['company']['company_id'];
            $availment->carewell_id                         = $request->memberInformation['member']['company']['carewell_id'] ? $request->memberInformation['member']['company']['carewell_id'] : 'CAREWELL ID';
            $availment->coverage_plan_id                    = $request->memberInformation['member']['company']['coverage_plan_id'];
            $availment->provider_id                         = $request->availmentInformation['provider']['id'];
            $availment->benefit_type_id                     = $request->availmentInformation['benefitType']['benefit_type_id'];
            $availment->date_avail                          = $request->availmentInformation['date_avail'];
            $availment->discharged_date                     = isset($request->availmentInformation['discharged_date']) ? $request->availmentInformation['discharged_date'] : null;
            $availment->chief_complaint                     = $request->availmentInformation['chief_complaint'] ? $request->availmentInformation['chief_complaint'] : " "; 
            $availment->initial_diagnosis                   = $request->availmentInformation['initial_diagnosis'] ? $request->availmentInformation['initial_diagnosis'] : " ";
            $availment->final_diagnosis                     = $request->availmentInformation['final_diagnosis'] ? $request->availmentInformation['final_diagnosis'] : " ";
            $availment->diagnosis_id                        = isset($request->availmentInformation['diagnosis_id']) ? $request->availmentInformation['diagnosis_id'] : null;
            $availment->provider_payee_id                   = $request->payeeInformation['form']['provider_payee_id'];
            $availment->doctor_id                           = $request->payeeInformation['form']['doctor_id'] ? $request->payeeInformation['form']['doctor_id'] : null;
            $availment->provider_payee_fee                  = $request->payeeInformation['total_amount_procedure'];
            $availment->doctor_fee                          = $request->payeeInformation['total_amount_physician'];
            $availment->caller_name                         = $request->callerInformation['caller_name'];
            $availment->caller_position                     = isset($request->callerInformation['caller_position']) ? $request->callerInformation['caller_position'] : null;
            $availment->caller_contact_number               = isset($request->callerInformation['caller_contact_number']) ? $request->callerInformation['caller_contact_number'] : null;
            $availment->caller_date                         = $request->callerInformation['caller_date'];
            $availment->procedures_gross_amount_total       = $request->procedureInformation['procedures_gross_amount_total'];
            $availment->procedures_phic_amount_total        = $request->procedureInformation['procedures_phic_amount_total'];
            $availment->procedures_patient_charged_total    = $request->procedureInformation['procedures_patient_charged_total'];
            $availment->procedures_carewell_charged_total   = $request->procedureInformation['procedures_carewell_charged_total'];
            $availment->procedures_charge_other_total       = $request->procedureInformation['procedures_charge_other_total'];
            $availment->doctors_gross_amount_total          = $request->physicianInformation['doctors_gross_amount_total'];
            $availment->doctors_phic_amount_total           = $request->physicianInformation['doctors_phic_amount_total'];
            $availment->doctors_patient_charged_total       = $request->physicianInformation['doctors_patient_charged_total'];
            $availment->doctors_carewell_charged_total      = $request->physicianInformation['doctors_carewell_charged_total'];
            $availment->doctors_charge_other_total          = $request->physicianInformation['doctors_charge_other_total'];
            $availment->grand_total                         = $request->payeeInformation['grand_total'];
            $availment->save();

            foreach($request->procedureInformation["procedure_list"] as $key => $value)
            {
                $availment_procedure                    = new AvailmentProcedure();
                $availment_procedure->availment_id      = $availment->id;
                $availment_procedure->procedures_id     = $value['procedure']['id'];
                $availment_procedure->gross_amount      = $value['procedures_gross_amt'];
                $availment_procedure->phic_charity_swa  = $value['procedures_phic_amt'];
                $availment_procedure->patient_charged   = $value['procedures_patient_charge'];
                $availment_procedure->carewell_charged  = $value['procedures_carewell_charge'];
                $availment_procedure->charge_other      = $value['procedures_charge_other'];
                $availment_procedure->remarks           = $value['procedures_remarks'];
                $availment_procedure->is_disapproved    = $value['procedures_is_disapproved'] ? 1 : 0;
                $availment_procedure->save();
            }

            
            foreach($request->physicianInformation["physician_list"] as $key => $value)
            {
                $availment_doctor                       = new AvailmentDoctor();
                $availment_doctor->availment_id         = $availment->id;
                $availment_doctor->doctor_id            = $value['doctor_id'];
                $availment_doctor->specialization_id    = $value['specialization_id'];
                $availment_doctor->rate_rvs             = $value['doctor_rate_rvs'];
                $availment_doctor->procedures_id        = $value['doctor_procedures_id'];
                $availment_doctor->doctors_fee          = $value['doctor_fee'];
                $availment_doctor->phic_charity_swa     = $value['doctor_phic_amt'];
                $availment_doctor->patient_charged      = $value['doctor_patient_charge'];
                $availment_doctor->carewell_charged     = $value['doctor_carewell_charge'];
                $availment_doctor->charge_other         = $value['doctor_charge_other'];
                $availment_doctor->save();
            }

            $message = 'Availment has been successfully created!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($availment,$availment->id);
            $description = $approval_number." (availment) has been created.";
            AuditTrailService::createAuditTrail('Create Availment', $description, null ,$new_data,$availment->id,'App\Models\Availment');
        // }
 
        $data['status_message'] = $message;
        $data['return_status'] = 'success';
        return json_encode($data);

    }

    public function update(Request $request,$id)
    {
        // return $request->availmentInformation['diagnosis_id'];
        $availment_model = new Availment();
        $old_data = AuditTrailService::getDataForAuditTrail($availment_model,$id);
        if($request->for_disapproved)
        {
            $availment_model                                      = $availment_model->find($id);
            $availment_model->status                              = $request->status;
            $availment_model->status_remarks                      = $request->status_remarks;
            $availment_model->save();
            
            $new_data = AuditTrailService::getDataForAuditTrail($availment_model,$id);
            $description = $request->approval_id." (availment) has been disapproved.";
            AuditTrailService::createAuditTrail('Disapproved Availment', $description, $old_data ,$new_data,$id,'App\Models\Availment');

            $message = 'Availment has been successfully disapproved!';
            $code = 200;

            return response($message, $code);
        }
        else
        {
            $ret = AvailmentService::validate_member($request, $id);
            if($ret)
            {
                $code = 300;
                $data['status_message'] = $ret;
                $data['return_status'] = 'error';
                return json_encode($data);
            }
            
            $availment_model                                      = $availment_model->find($id);
            $availment_model->approval_id                         = $request->dBData['approval_id'];
            $availment_model->member_id                           = $request->memberInformation['member']['id'];
            $availment_model->company_id                          = $request->memberInformation['member']['company']['company_id'];
            $availment_model->carewell_id                         = $request->memberInformation['member']['company']['carewell_id'] ? $request->memberInformation['member']['company']['carewell_id'] : 'CAREWELL ID';
            $availment_model->coverage_plan_id                    = $request->memberInformation['member']['company']['coverage_plan_id'];
            $availment_model->provider_id                         = $request->availmentInformation['provider']['id'];
            $availment_model->benefit_type_id                     = $request->availmentInformation['benefitType']['benefit_type_id'];
            $availment_model->date_avail                          = $request->availmentInformation['date_avail'];
            $availment_model->discharged_date                     = isset($request->availmentInformation['discharged_date']) ? $request->availmentInformation['discharged_date'] : null;
            $availment_model->chief_complaint                     = $request->availmentInformation['chief_complaint'] ? $request->availmentInformation['chief_complaint'] : " ";
            $availment_model->initial_diagnosis                   = $request->availmentInformation['initial_diagnosis'] ? $request->availmentInformation['initial_diagnosis'] : " ";
            $availment_model->final_diagnosis                     = $request->availmentInformation['final_diagnosis'] ? $request->availmentInformation['final_diagnosis'] : " ";
            $availment_model->diagnosis_id                        = isset($request->availmentInformation['diagnosis_id']) ? $request->availmentInformation['diagnosis_id'] : null;
            $availment_model->provider_payee_id                   = $request->payeeInformation['form']['provider_payee_id'];
            $availment_model->doctor_id                           = $request->payeeInformation['form']['doctor_id'] ? $request->payeeInformation['form']['doctor_id'] : null;
            $availment_model->provider_payee_fee                  = $request->payeeInformation['total_amount_procedure'];
            $availment_model->doctor_fee                          = $request->payeeInformation['total_amount_physician'];
            $availment_model->caller_name                         = $request->callerInformation['caller_name'];
            $availment_model->caller_position                     = isset($request->callerInformation['caller_position']) ? $request->callerInformation['caller_position'] : null;
            $availment_model->caller_contact_number               = isset($request->callerInformation['caller_contact_number']) ? $request->callerInformation['caller_contact_number'] : null;
            $availment_model->caller_date                         = $request->callerInformation['caller_date'];
            $availment_model->procedures_gross_amount_total       = $request->procedureInformation['procedures_gross_amount_total'];
            $availment_model->procedures_phic_amount_total        = $request->procedureInformation['procedures_phic_amount_total'];
            $availment_model->procedures_patient_charged_total    = $request->procedureInformation['procedures_patient_charged_total'];            
            $availment_model->procedures_carewell_charged_total   = $request->procedureInformation['procedures_carewell_charged_total'];
            $availment_model->procedures_charge_other_total       = $request->procedureInformation['procedures_charge_other_total'];
            $availment_model->doctors_gross_amount_total          = $request->physicianInformation['doctors_gross_amount_total'];
            $availment_model->doctors_phic_amount_total           = $request->physicianInformation['doctors_phic_amount_total'];
            $availment_model->doctors_patient_charged_total       = $request->physicianInformation['doctors_patient_charged_total'];
            $availment_model->doctors_carewell_charged_total      = $request->physicianInformation['doctors_carewell_charged_total'];
            $availment_model->doctors_charge_other_total          = $request->physicianInformation['doctors_charge_other_total'];  
            $availment_model->grand_total                         = $request->payeeInformation['grand_total'];
            $availment_model->save();

            $delete_procedures = AvailmentProcedure::where("availment_id",$id)->delete();
            foreach($request->procedureInformation["procedure_list"] as $key => $value)
            {
                $availment_procedure                    = new AvailmentProcedure();
                $availment_procedure->availment_id      = $id;
                $availment_procedure->procedures_id     = $value['procedure']['id'];
                $availment_procedure->gross_amount      = $value['procedures_gross_amt'];
                $availment_procedure->phic_charity_swa  = $value['procedures_phic_amt'];
                $availment_procedure->patient_charged   = $value['procedures_patient_charge'];
                $availment_procedure->carewell_charged  = $value['procedures_carewell_charge'];
                $availment_procedure->charge_other      = $value['procedures_charge_other'];
                $availment_procedure->remarks           = $value['procedures_remarks'];
                $availment_procedure->is_disapproved    = $value['procedures_is_disapproved'] ? 1 : 0;
                $availment_procedure->save();
            }

            $delete_doctors = AvailmentDoctor::where("availment_id",$id)->delete();
            foreach($request->physicianInformation["physician_list"] as $key => $value)
            {
                $availment_doctor                       = new AvailmentDoctor();
                $availment_doctor->availment_id         = $id;
                $availment_doctor->doctor_id            = $value['doctor_id'];
                $availment_doctor->specialization_id    = $value['specialization_id'];
                $availment_doctor->rate_rvs             = $value['doctor_rate_rvs'];
                $availment_doctor->procedures_id        = $value['doctor_procedures_id'];
                $availment_doctor->doctors_fee          = $value['doctor_fee'];
                $availment_doctor->phic_charity_swa     = $value['doctor_phic_amt'];
                $availment_doctor->patient_charged      = $value['doctor_patient_charge'];
                $availment_doctor->carewell_charged     = $value['doctor_carewell_charge'];
                $availment_doctor->charge_other         = $value['doctor_charge_other'];
                $availment_doctor->save();
            }

        //     $message = 'Availment has been successfully close!';
        //     $code = 200;

        //     return response($message, $code);
        // }

        // $validator = $this->validateRequestInput($request->all(), $availment_model->requestRules());
        
        // if($validator->fails())
        // {
        //     $message = $validator->errors()->first();
        //     $code = 300;
        // }
        // else
        // {
            
        //     $availment                                      = new Availment();
        //     $availment                                      = $availment->find($id);
        //     $availment->approval_id                         = $request->approval_id;
        //     $availment->company_id                          = $request->company_id;
        //     $availment->member_id                           = $request->member['id'];
        //     $availment->carewell_id                         = $request->member['carewell_id'];
        //     $availment->provider_id                         = $request->provider_id;
        //     $availment->benefit_type_id                     = $request->benefit_type_id;
        //     $availment->date_avail                          = $request->date_avail;
        //     $availment->chief_complaint                     = $request->chief_complaint;
        //     $availment->initial_diagnosis                   = $request->initial_diagnosis;
        //     $availment->final_diagnosis                     = $request->final_diagnosis;
        //     $availment->diagnosis_id                        = $request->diagnosis_id;
        //     $availment->provider_payee_id                   = $request->provider_payee_id;
        //     $availment->doctor_id                           = $request->doctor_id;
        //     $availment->provider_payee_fee                  = $request->provider_payee_fee;
        //     $availment->doctor_fee                          = $request->doctor_fee;
        //     $availment->caller_name                         = $request->caller_name;
        //     $availment->caller_relationship                 = $request->caller_relationship;
        //     $availment->caller_contact_number               = $request->caller_contact_number;
        //     $availment->caller_date                         = $request->caller_date;
        //     $availment->status_remarks                      = $request->status_remarks;
        //     $availment->procedures_gross_amount_total       = $request->procedures_gross_amount_total;
        //     $availment->procedures_phic_amount_total        = $request->procedures_phic_amount_total;
        //     $availment->procedures_patient_charged_total    = $request->procedures_patient_charged_total;
        //     $availment->procedures_carewell_charged_total   = $request->procedures_carewell_charged_total;
        //     $availment->doctors_gross_amount_total          = $request->doctors_gross_amount_total;
        //     $availment->doctors_phic_amount_total           = $request->doctors_phic_amount_total;
        //     $availment->doctors_patient_charged_total       = $request->doctors_patient_charged_total;
        //     $availment->doctors_carewell_charged_total      = $request->doctors_carewell_charged_total;
        //     $availment->grand_total                         = $request->grand_total;
        //     $availment->save();
            
        //     $delete_procedures = AvailmentProcedure::where("availment_id",$id)->delete();
        //     foreach($request->procedure as $key => $value)
        //     {
        //         $availment_procedure                    = new AvailmentProcedure();
        //         $availment_procedure->availment_id      = $id;
        //         $availment_procedure->procedures_id     = $value['procedures_id'];
        //         $availment_procedure->gross_amount      = $value['gross_amount'];
        //         $availment_procedure->phic_charity_swa  = $value['phic_charity_swa'];
        //         $availment_procedure->patient_charged   = $value['patient_charged'];
        //         $availment_procedure->carewell_charged  = $value['carewell_charged'];
        //         $availment_procedure->is_disapproved    = $value['is_disapproved'] ? 1 : 0;
        //         $availment_procedure->save();
        //     }

        //     $delete_doctors = AvailmentDoctor::where("availment_id",$id)->delete();
        //     foreach($request->doctor as $key => $value)
        //     {
        //         $availment_doctor                       = new AvailmentDoctor();
        //         $availment_doctor->availment_id         = $id;
        //         $availment_doctor->doctor_id            = $value['doctor_id'];
        //         $availment_doctor->specialization_id    = $value['specialization_id'];
        //         $availment_doctor->rate_rvs             = $value['rate_rvs'];
        //         $availment_doctor->procedures_id        = $value['procedures_id'];
        //         $availment_doctor->doctors_fee          = $value['doctors_fee'];
        //         $availment_doctor->phic_charity_swa     = $value['phic_charity_swa'];
        //         $availment_doctor->patient_charged      = $value['patient_charged'];
        //         $availment_doctor->carewell_charged     = $value['carewell_charged'];
        //         $availment_doctor->save();
        //     }

            $new_data = AuditTrailService::getDataForAuditTrail($availment_model,$id);
            $description = $request->dBData['approval_id']." (availment) has been updated.";
            AuditTrailService::createAuditTrail('Update Availment', $description, $old_data ,$new_data,$id,'App\Models\Availment');
            
            $message = 'Availment has been successfully updated!';
            $code = 200;
        //}
    }
 
    $data['status_message'] = $message;
    $data['return_status'] = 'success';
    return json_encode($data);

    }
}
