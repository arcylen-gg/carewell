<?php

namespace App\Http\Controllers;
use App\Models\Member;
use App\Models\MemberDependent;
use App\Models\MemberCompany;
use App\Globals\AuditTrailService;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use DB;

class MemberController extends BaseController
{
     protected $model;

    public function __construct(Member $model)
    {
    	$this->model = $model;
        //die(var_dump($this->model));
    }

    public function index(Request $request)
    {
        $is_archived = $request->is_archived;
        $name = $request->name;
        $limit = $request->limit;
        $company_id = $request->company_id;

        if($name)
        {
            $this->model = $this->model->with(['testing'])
            ->where('first_name','like','%'.$name.'%')
            ->orWhere('middle_name','like','%'.$name.'%')
            ->orWhere('last_name','like','%'.$name.'%')
            ->orWhereHas('testing', function($query) use ($name) {
                $query->where('carewell_id','like','%'.$name.'%')
                    ->orWhere('universal_id','like','%'.$name.'%');
            });
        }
 
        if($company_id !== 'all')
        {
            $this->model = $this->model->whereHas('testing', function($query) use ($company_id) {
                $query->where('company_id',$company_id);
            });
        }

        return $this->model->where('is_archived',$is_archived)->orderBy('first_name')->orderBy('middle_name')->orderBy('last_name')->paginate($limit);
    }

    public function store(Request $request)
    { 
        $validator = $this->validateRequestInput(request()->all(), $this->model->requestRules());

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            // return $request->all();

            $this->model = $this->model->create([
                'first_name'      			=> $request->first_name,
                'middle_name' 				=> $request->middle_name,
                'last_name'      			=> $request->last_name,
                'mothers_maiden_name' 		=> $request->mothers_maiden_name,
                'birth_date'      			=> $request->birth_date,
                'gender' 					=> $request->gender,
                'marital_status'      		=> $request->marital_status,
                'contact_number' 			=> $request->contact_number,
                'email'      				=> $request->email,
                'permanent_address' 		=> $request->permanent_address,
                'present_address'      		=> $request->present_address,
                'sss_number'      			=> $request->sss_number,
                'tin' 						=> $request->tin,
                'philhealth_number'      	=> $request->philhealth_number,
                'pagibig_number' 			=> $request->pagibig_number,
                
            ]);   
            
            if($request->birth_date)
            {
                $birthdate = date_format(date_create($request->birth_date),"m/d/Y");
                $this->model->setId(array(
                    'universal_id'  => null,
                    'birth_date'    => $birthdate,
                    'first_name'    => $request->first_name,
                    'middle_name'   => $request->middle_name,
                    'last_name'     => $request->last_name,
                    'company_code'  => $request->company["code"],
                    'billing_date'  => date("mdy"),
                ));
                $universal_id = $this->model->modelAction('generateUniversalId');
            }

            $member_company = $this->model;
            $member_company ->member_company()->create([
                'universal_id'      		=> isset($universal_id) ? $universal_id : null,
                'member_id'                 => $this->model->id,
                'company_ref_id' 			=> $request->employee_number,
                'company_id' 				=> $request->company_ref_id['id'],
                'company_deployment_id' 	=> $request->deployment,
                'payment_mode_id' 			=> $request->mode_of_payment,
                'coverage_plan_id' 			=> $request->coveragePlan,
            ]);
            
            if($request->dependents)
            {
	         	foreach ($request->dependents as $key => $value) 
	            {
                    if($value["full_name"] && $value["birth_date"] && $value["relationship"])
                    {
                        $this->model->dependents()->create([
                            'member_id'            => $this->model->id,
                            'full_name'            => $value["full_name"],
                            'birth_date'           => $value["birth_date"],
                            'relationship'         => $value["relationship"],
                        ]);
                    }
	            }
	        }

            $message = "Member has been successfully created!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." member has been added.";
            AuditTrailService::createAuditTrail('Added Member', $description, null ,$new_data,$this->model->id,'App\Models\Member');
        }

        return response($message, $code); 
    }

    public function update(Request $request, $id)
    {
        $old_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
        if($request->for_archive)
        {
            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $new_data->is_archived = $new_data->is_archived ? 0 : 1;
            $response = $new_data->is_archived ? 'Archive' : 'Restore';
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." member has been ".strtolower($response)."."; 
            AuditTrailService::createAuditTrail($response.' Member', $description, $old_data ,$new_data,$id,'App\Models\Member');
            return $this->archiveData($this->model, "member", $id);
        }

        $update_tagged = $this->validateArchiveTagged($this->model, "member_id", $id);

        if($update_tagged)
        {
            return response($update_tagged["message"], $update_tagged["code"]);
        }

        $rules = $this->model->requestRules();

        $validator = $this->validateRequestInput(request()->all(), $rules);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            // return $request->company['carewell_id'];
            //$carewell_id = $this->model->modelAction('generateCarewellId');
            
            $this->model = $this->model->find($id);
            $this->model = $this->model->fill([
                'first_name'      			=> $request->first_name,
                'middle_name' 				=> $request->middle_name,
                'last_name'      			=> $request->last_name,
                'mothers_maiden_name' 		=> $request->mothers_maiden_name,
                'birth_date'      			=> $request->birth_date,
                'gender' 					=> $request->gender,
                'marital_status'      		=> $request->marital_status,
                'contact_number' 			=> $request->contact_number,
                'email'      				=> $request->email,
                'permanent_address' 		=> $request->permanent_address,
                'present_address'      		=> $request->present_address,
                'sss_number'      			=> $request->sss_number,
                'tin' 						=> $request->tin,
                'philhealth_number'      	=> $request->philhealth_number,
                'pagibig_number' 			=> $request->pagibig_number,
                ]);
            $this->model->save();

            if($request->birth_date)
            {
                $birthdate = date_format(date_create($request->birth_date),"m/d/Y");

                $this->model->setId(array(
                    'universal_id'  => null,
                    'birth_date'    => $birthdate,
                    'first_name'    => $request->first_name,
                    'middle_name'   => $request->middle_name,
                    'last_name'     => $request->last_name,
                    'company_code'  => $request->company["company"]["code"],
                    'member_id'     => $id,
                    'billing_date'  => date("mdy"),
                ));
                $universal_id = $this->model->modelAction('generateUniversalId');
            }

            $is_same_company = DB::table('member_companies')->where('member_id',$id)->latest()->first();
            $get_company_id = isset($request->company_ref_id['company_id']) ? $request->company_ref_id['company_id'] : $request->company_ref_id['id'];

            MemberCompany::create([
                'universal_id'      		=> isset($universal_id) ? $universal_id : null,
                'carewell_id'               => $is_same_company->company_id == $get_company_id ? $request->company['carewell_id'] : null,
                // 'carewell_id'               => isset($request->company['carewell_id']) ? $request->company['carewell_id'] : null,
                'member_id'                 => $id,
                'company_ref_id' 			=> $request->employee_number,
                'company_id' 				=> $get_company_id,
                // 'company_id' 				=> isset($request->company_ref_id['company_id']) ? $request->company_ref_id['company_id'] : $request->company_ref_id['id'],
                'company_deployment_id' 	=> isset($request->deployment['id']) ? $request->deployment['id'] : $request->deployment,
                'payment_mode_id' 			=> $request->mode_of_payment,
                'coverage_plan_id' 			=> isset($request->coveragePlan['id']) ? $request->coveragePlan['id'] : $request->coveragePlan,
            ]);
                
            if(isset($request->dependents)){
	            $units = MemberDependent::where("member_id", $request->id)->get();
	            //die(var_dump($units));
	            foreach ($units as $k => $v) 
	            {
	               $delete_ids[$k] = $v["id"];
	            }
	           	if(isset($delete_ids)){
	           		MemberDependent::destroy($delete_ids);
	           	}
	            
	        }
            
            if(isset($request->dependents))
            {
	         	foreach ($request->dependents as $key => $value) 
	            {
	                if(isset($value["full_name"]))
	                {
                        $this->model->dependents()->create([
                            'full_name'            => $value["full_name"],
                            'birth_date'           => $value["birth_date"],
                            'relationship'         => $value["relationship"],
                        ]);
	                }
	            }
            }
            
            $message = "Member has been successfully updated!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." member has been updated.";
            AuditTrailService::createAuditTrail('Update Member', $description, $old_data ,$new_data,$id,'App\Models\Member');
        }
        
        return response($message, $code);
    }
}
