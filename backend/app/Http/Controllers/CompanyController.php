<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Document;
use App\Models\CompanyContactPerson;
use App\Models\CompanyCoveragePlan;
use App\Models\CompanyDeployment;
use App\Globals\DocumentService;
use App\Globals\AuditTrailService;

use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

class CompanyController extends BaseController
{
    private $model;

    public function __construct(Company $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){
        // $this->model = $this->model->with([
        //     'company_contact_person',
        //     'company_deployment',
        //     'company_coverage_plan.coverage_plan',
        //     'coverage_plan',
        // ]); //lazy loader
        $this->model = $this->model->with([
            // 'company_contact_person',
        ]); //lazy loader
    }

    public function index(Request $request)
    {
        $this->loadWithRelatedModel();
        $this->filterTable($request->all());
        return $this->getResultList($this->model->orderBy('name'));
    }

    public function filterTable()
    { 
        $related_fields             = [""];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name","name","code","address","email","contact_number"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function store(Request $request)
    {
        $json = json_decode($request->form,true);
        // return $json;

        $validator = $this->validateRequestInput($json, $this->model->requestRules());

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $company                            = $this->model;
            $company->name                      = str_replace(","," ", $json['name']);		 
            $company->code                      = $json['code'];		 
            $company->address                   = $json['address'];		 
            $company->email                     = $json['email'];	 
            $company->contact_number            = $json['contact_number'];
            $company->account_type              = $json['account_type'];
            $company->policy_effective_date     = $json['policy_effective_date'];
            $company->policy_expiry_date        = $json['policy_expiry_date'];
            $company->save();
            
            DocumentService::upload($company->id);

            $contact_person = json_decode($request->contact_person,true);
             //die(var_dump($contact_person));
            foreach($contact_person as $key => $value)
            {
                if(count($value)){
                    $this->model->company_contact_person()->create([
                        'company_id'    => $company->id,
                        'first_name'    => $value['first_name'],
                        'middle_name'   => $value['middle_name'],
                        'last_name'     => $value['last_name'],
                        'position'      => $value['position'],
                        'contact_number' => $value['contact_number'],
                    ]);
                }
            }

            $deployment = json_decode($request->deployment,true);
            foreach($deployment as $key => $value)
            {
                $this->model->company_deployment()->create([
                    'company_id' => $company->id,
                    'name' => str_replace(',','',$value['name']),
                ]);
            }

            $coverage = json_decode($request->coverage, true);
            foreach($coverage as $key => $value)
            {
                $this->model->company_coverage_plan()->create([
                    'company_id' => $company->id,
                    'coverage_plan_id' => $value['coverage_plan_id'],
                ]);
            }

            
            $message = 'Company has been successfully created!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $json['name']." company has been added.";
            AuditTrailService::createAuditTrail('Added Company', $description, null ,$new_data,$this->model->id,'App\Models\Company');
        }
 
        return response($message, $code);
    }

    public function update(Request $request,$id)
    {
        // $data['all'] = $request->all();
        // $data['form'] = json_decode($request->form,true);
        // $data['deployment'] =json_decode($request->deployment,true);
        // $data['coverage'] = json_decode($request->coverage,true);
        // return $data;

        $json = json_decode($request->form,true);
        $old_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
        if($request->for_archive)
        {
            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $new_data->is_archived = $new_data->is_archived ? 0 : 1;
            $response = $new_data->is_archived ? 'Archive' : 'Restore';
            $description = $new_data->name." company has been ".strtolower($response)."."; 
            AuditTrailService::createAuditTrail($response.' Company', $description, $old_data ,$new_data,$id,'App\Models\Company');
            return $this->archiveData($this->model, "company", $id);
        }

        $rules = $this->model->requestRules();
        $rules["name"] = $rules["name"] . ",name," . $id;
        $rules["email"] = $rules["email"] . ",email," . $id;
        $validator = $this->validateRequestInput($json, $rules);
        

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $company                            = $this->model->find($id);
            $company->name                      = $json['name'];		 
            $company->code                      = $json['code'];		 
            $company->address                   = $json['address'];		 
            $company->email                     = $json['email'];	 
            $company->contact_number            = $json['contact_number'];
            $company->account_type              = $json['account_type'];
            $company->policy_effective_date     = $json['policy_effective_date'];
            $company->policy_expiry_date        = $json['policy_expiry_date'];
            $company->save();

            $file_to_delete = json_decode($request->files_delete,true);
            $document = count($file_to_delete) !== 0 ? Document::whereIn('id',$file_to_delete)->delete() : "";
            $contact_delete = CompanyContactPerson::where('company_id',$id)->delete();
            //$deployment_delete = CompanyDeployment::where('company_id',$id)->delete();
            $coverage_delete = CompanyCoveragePlan::where('company_id',$id)->delete();
            $upload = request()->get('files') !== "no_file" ? DocumentService::upload($id) : "" ;
            
            $contact_person = json_decode($request->contact_person,true);
            foreach($contact_person as $key => $value)
            {
                CompanyContactPerson::create([
                    'company_id' => $id,
                    'first_name' => $value['first_name'],
                    'middle_name' => $value['middle_name'],
                    'last_name' => $value['last_name'],
                    'position' => $value['position'],
                    'contact_number' => $value['contact_number'],
                ]);
            }
           
           //for deployment
           $deployment = json_decode($request->deployment,true);
           $companyDeployment = new CompanyDeployment();
           $companyDeployment = $companyDeployment->where('company_id',$id)->get();
           $this->check($companyDeployment,$deployment,'deployment');

            foreach($deployment as $key => $value)
            {
                $companyDeployment = new CompanyDeployment();
                $update_tagged = count($value) > 1 ? $this->validateArchiveTagged($companyDeployment, "company_deployment_id", $value['id']) : null;
                if(!$update_tagged)
                {
                    CompanyDeployment::updateOrCreate([
                        'company_id' => $id,
                        'name' => str_replace(',','',$value['name']),
                    ]);
                }
            }
            
            //for coverage plan
            $coverage = json_decode($request->coverage, true);
            $companyCoveragePlan = new CompanyCoveragePlan();
            $companyCoveragePlan = $companyCoveragePlan->where('company_id',$id)->get();
            $this->check($companyCoveragePlan,$coverage,'coveragePlan');

            foreach($coverage as $key => $value)
            {
                CompanyCoveragePlan::updateOrCreate([
                    'company_id' => $id,
                    'coverage_plan_id' => $value['coverage_plan_id'],
                ]);
            }

            $message = 'Company has been successfully updated!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $json['name']." company has been updated.";
            AuditTrailService::createAuditTrail('Update Company', $description, $old_data ,$new_data,$id,'App\Models\Company');
        }
 
        return response($message, $code);
    
    }
    public function check($data1,$data2,$model){
        //die(var_dump($data1,$data2));
        foreach ($data1 as $key => $value) {
            $has = false;
            foreach($data2 as $key1 => $value1)
            {
                if($model == 'deployment'){
                    if(isset($value1['id']) && $value['id'] == $value1['id'])
                    {
                        $has = true;
                    }
                }
            }
            if($has == false){
                if($model == 'deployment'){
                    $res = CompanyDeployment::where('id',$value['id'])->delete();
                }
            }
        }
    }
}
