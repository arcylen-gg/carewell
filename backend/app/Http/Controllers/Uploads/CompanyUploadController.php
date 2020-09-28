<?php
namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Controller;
use App\Exports\CompanyExportation;
use App\Exports\CompanyExport;
use App\Imports\CompanyImport;
use App\Models\Company;
use App\Models\CompanyContactPerson;
use App\Models\CompanyCoveragePlan;
use App\Models\CompanyDeployment;
use App\Models\CoveragePlan;
use Excel;
use Validator;

use Illuminate\Http\Request;

class CompanyUploadController extends Controller
{
	public function export(Request $request)
    {
		$company_array = [];
		$company_use = [];
		$company = CoveragePlan::with('company_coverage_plans')->get();
		foreach($company as $key=>$value)
		{
			if($value->company_coverage_plans == null)
			{array_push($company_array,$value);}
			else
			{array_push($company_use,$value);}
		}

		if(!empty($company_array))
		{
			return Excel::download(new CompanyExport($company_array),'Company_Template.xlsx');
		}
		else
		{
			echo("No Coverage Plan is available. Please try again");
		}
	}
	
 	public function exportation(Request $request)
    {
    	$company = new Company();
		$company = $company::where('is_archived',$request->status)->get()->toArray();
		// return $company;
    	return Excel::download(new CompanyExportation($company), 'Company_export.csv');   
	}

	public function getCompanyList(Request $request)
	{
		$path = $request->file->storeAs('uploads',$request->file->getClientOriginalName());
		
		$import = new CompanyImport(); 
		$array = $import->toArray($path);
		return $array[0];
	}
	
	public function import(Request $request)
	{
		$remarks = '';
		$color = 'red';

		$messages = [
			'name.required' => 'Company Name is required.',
			'email.email' => 'Check Email Format.',
			'email.required' => 'Email is required.',
			'account_type.required' => 'Account Type is required.',
			'code.required' => 'Company Code is required.',
			'contact_number.required' => 'Company Contact Number is required.',
			'address.required' => 'Company Address is required.',
			'policy_effective_date.required' => 'Policy Effective Date is required.',
			'policy_expiry_date.required' => 'Policy Expiry Date is required.',
			'contact_person_firstname.required' => 'Contact Person First Name is required.',
			'contact_person_lastname.required' => 'Contact Person Last Name is required.',
			'contact_person_position.required' => 'Contact Person Position is required.',
			'contact_person_contact_number.required' => 'Contact Person Contact Number is required.',
			'coverage_plan.required' => 'Coverage Plan selected is required.',
			'deployment.required' => 'Deployment is required.'
		];

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required|email',
			'code' => 'required',
			'account_type' => 'required',
			'policy_effective_date' => 'required',
			'policy_expiry_date' => 'required',
			'contact_number' => 'required',
			'address' => 'required',
			'contact_person_firstname' => 'required',
			'contact_person_lastname' => 'required',
			'contact_person_position' => 'required',
			'contact_person_contact_number' => 'required',
			'coverage_plan' => 'required',
			'deployment' => 'required'
		], $messages);

		if($validator->fails())
		{
			$errors = $validator->errors();
            return Self::forArray($errors->first(),$color,$request->all());
		}
		else
		{
			$company = Company::where('name', $request->name)->first();
			if($company)
			{
				$company_id = $company->id;

				$validateContact = [
					'company_id'		=> $company_id, 
					'first_name'		=> $request->contact_person_firstname, 
					'middle_name'		=> $request->contact_person_middlename ?? null, 
					'last_name'			=> $request->contact_person_lastname,
				];

				$coverage_info = CoveragePlan::where('name',$request->coverage_plan)->first();
				$company_contacts = CompanyContactPerson::where($validateContact)->first();
				if(!$company_contacts)
				{
					$count_contacts = CompanyContactPerson::where('company_id', $company_id)->count();
					if($count_contacts >= 2)
					{
						return Self::forArray('Company Contact Person Exceeded.',$color,$request->all());
					}

					$company_contacts = Self::addNewContactPerson($company_id, $request->all());

					$coverage_plan = Self::validateCoveragePlan($company_id, $coverage_info->id);

					$deployments = Self::validateDeployment($company_id, $request->deployment);

					if(!$coverage_plan && $deployments)
					{
						Self::addNewCoveragePlan($company_id, $coverage_info->id);
						$remarks = "New Contact Person and Coverage Plan Added.";
					}
					else if($coverage_plan && !$deployments)
					{
						Self::addNewDeployment($company_id, $request->deployment);
						$remarks = "New Contact Person and Deployment Added.";
					}
					else if(!$coverage_plan && !$deployments)
					{
						Self::addNewCoveragePlan($company_id, $coverage_info->id);
						Self::addNewDeployment($company_id, $request->deployment);
						$remarks = "New Contact Person, Coverage Plan and Deployment Added.";
					}
					else
					{
						$remarks ="New Contact Person Added.";
					}

					$color = 'green';
					return Self::forArray($remarks,$color,$request->all());
				}
				else
				{
					
					$coverage_plan = Self::validateCoveragePlan($company_id, $coverage_info->id);

					$deployments = Self::validateDeployment($company_id, $request->deployment);

					if(!$coverage_plan && $deployments)
					{
						Self::addNewCoveragePlan($company_id, $coverage_info->id);
						$remarks = "New Coverage Plan Added.";
						$color = 'green';
					}
					else if($coverage_plan && !$deployments)
					{
						Self::addNewDeployment($company_id, $request->deployment);
						$remarks = "New Deployment Added.";
						$color = 'green';
					}
					else if(!$coverage_plan && !$deployments)
					{
						Self::addNewCoveragePlan($company_id, $coverage_info->id);
						Self::addNewDeployment($company_id, $request->deployment);
						$remarks = "New Coverage Plan and Deployment Added.";
						$color = 'green';
					}
					else
					{
						$remarks = "Same Company Details Exists.";
					}
					return Self::forArray($remarks,$color,$request->all());
				}
			}
			else
			{
				$company_code = Company::where('code', $request->code)->first();
				if($company_code)
				{
					$remarks = "Company Code Already Use.";
					return Self::forArray($remarks, $color, $request->all());
				}

				$coverage_plan_id = CoveragePlan::where('name',$request->coverage_plan)->first();
				$coverage_plan = CompanyCoveragePlan::where('coverage_plan_id',$coverage_plan_id->id)->first();
				if($coverage_plan)
				{
					$remarks = "Coverage Plan Already Use.";
					return Self::forArray($remarks,$color,$request->all());
				}

				$requiredCompany = [
					'name' 					=> $request->name,
					'code' 					=> $request->code,
					'address' 				=> $request->address,
					'email'					=> $request->email,
					'contact_number'		=> $request->contact_number,
					'account_type'			=> $request->account_type,
					'policy_effective_date'	=> Self::computeDate($request->policy_effective_date),
					'policy_expiry_date'	=> Self::computeDate($request->policy_expiry_date)
				];

				$model_company = new Company();
				$company = $model_company->create($requiredCompany);
				$company_id = $company->id;

				Self::addNewContactPerson($company_id, $request->all());

				Self::addNewCoveragePlan($company_id, $coverage_plan_id->id);
	
				Self::addNewDeployment($company_id, $request->deployment);

				$remarks = "New Company Added.";
				$color = 'green';
				return Self::forArray($remarks,$color,$request->all());
			}
		}
	}
	
	public function addNewCoveragePlan($company_id, $coverage_plan_id)
	{
		$requiredCoveragePlan = [
			'company_id'=>$company_id, 
			'coverage_plan_id'=>$coverage_plan_id
		];

		$model_coverage_plan = new CompanyCoveragePlan();
		$company_coverage_plan = $model_coverage_plan->create($requiredCoveragePlan);
	}

	public function addNewDeployment($company_id, $deployment)
	{
		$requiredDeployment = [
			'company_id'=>$company_id, 
			'name'=>$deployment
		];

		$model_deployment = new CompanyDeployment();
		$company_deployment = $model_deployment->create($requiredDeployment);
	}

	public function addNewContactPerson($company_id, $data)
	{
		$requiredContactPerson = [
			'company_id'		=> $company_id, 
			'first_name'		=> $data['contact_person_firstname'], 
			'middle_name'		=> $data['contact_person_middlename'], 
			'last_name'			=> $data['contact_person_lastname'], 
			'position'			=> $data['contact_person_position'], 
			'contact_number'	=> $data['contact_person_contact_number']
		];

		$model_contact_person = new CompanyContactPerson();
		$company_contact_person = $model_contact_person->create($requiredContactPerson);
	}

	public function validateCoveragePlan($company_id, $coverage_plan_id)
	{
		$remarks = '';
		$validatePlan = [
			'company_id'=>$company_id, 
			'coverage_plan_id'=>$coverage_plan_id
		];

		$coverage_plan = CompanyCoveragePlan::where($validatePlan)->first();
		if($coverage_plan)
		{
			$remarks = "Coverage Plan Already Exist.";
			return $remarks;
		}
		return $remarks;

	}

	public function validateDeployment($company_id, $deployment_name)
	{
		$remarks = '';
		$validateDeployment = [
			'company_id' => $company_id,
			'name' => $deployment_name
		];

		$company_deployment = CompanyDeployment::where($validateDeployment)->first();
		if($company_deployment)
		{
			$remarks = "Deployment Already Exist.";
		}
		return $remarks;
	}

	public function validateIfEmpty($table_value)
	{
		if(ctype_space($table_value) && ($table_value == "" || $table_value == null))
		{
			$bool = true;
		}
		else
		{
			$bool = false;
		}
		return $bool;
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

	public function forArray($remarks, $color, $request)
	{
		$array = [];
		$array['remarks'] = $remarks;
		$array['color'] = $color;
		$array['name'] = $request['name'] ? $request['name'] : "";
		$array['email'] = $request['email'] ? $request['email'] : "";
		$array['code'] = $request['code'] ? $request['code'] : "";
		$array['contact_number'] = $request['contact_number'] ? $request['contact_number'] : "";
		$array['account_type'] = $request['account_type'] ? $request['account_type'] : "";
		$array['address'] = $request['address'] ? $request['address'] : "";
		$array['policy_effective_date'] = $request['policy_effective_date'] ? $request['policy_effective_date'] : "";
		$array['policy_expiry_date'] = $request['policy_expiry_date'] ? $request['policy_expiry_date'] : "";
		$array['contact_person_firstname'] = $request['contact_person_firstname'] ? $request['contact_person_firstname'] : "";
		$array['contact_person_middlename'] = $request['contact_person_middlename'] ? $request['contact_person_middlename'] : "";
		$array['contact_person_lastname'] = $request['contact_person_lastname'] ? $request['contact_person_lastname'] : "";
		$array['contact_person_position'] = $request['contact_person_position'] ? $request['contact_person_position'] : "";
		$array['contact_person_contact_number'] = $request['contact_person_contact_number'] ? $request['contact_person_contact_number'] : "";
		$array['coverage_plan'] = $request['coverage_plan'] ? $request['coverage_plan'] : "";
		$array['deployment'] = $request['deployment'] ? $request['deployment'] : "";

		return $array;
	}
}