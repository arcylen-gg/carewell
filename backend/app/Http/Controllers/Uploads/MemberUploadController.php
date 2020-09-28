<?php

namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Controller;

use App\Exports\MemberExport;
use App\Imports\MemberImport;
use App\Exports\MemberExportation;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Member;
use App\Models\MemberCompany;
use App\Models\Company;
use App\Models\CompanyDeployment;
use App\Models\PaymentMode;
use App\Models\CoveragePlan;
use DateTime;
use Validator;
use Session;

class MemberUploadController extends Controller
{
    public function export(Request $request)
    {
        $company = Company::where('id',$request->company_id)->with('company_deployment','coverage_plan')->first();
        return Excel::download(new MemberExport($company), $company->name.'_Member_Template.xlsx');      
    }

    public function getMemberList(Request $request)
    {
        $path = $request->file->storeAs('uploads',$request->file->getClientOriginalName());
        
        $import = new MemberImport(); 
        $array = $import->toArray($path);
        return $array[0];
    }

    public function import(Request $request)
    {
        $remarks = '';
        $color = 'red';

        $sample['converted'] = self::computeDate($request->birth_date);

        $company = Company::where('name',$request->company)->first();

        $required_fields = false;

        $err_arr = Session::get('import_member_error') ? Session::get('import_member_error') : array();
        if(!isset($request->first_name))
        {
            $required_fields = true;
            $remarks = "First name is required.";
            $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
            $err[0 + count($err_arr)] = $array;
            Session::put('import_member_error', $err);
            return $array;
        }
        if(!isset($request->last_name))
        {
            $required_fields = true;
            $remarks = "Last name is required.";
            $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
            $err[0 + count($err_arr)] = $array;
            Session::put('import_member_error', $err);
            return $array;
        }
        if(isset($request->company))
        {        
            if(!$company)
            {
                $required_fields = true;
                $remarks = "Company Doesn't Exist.";
                $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
                $err[0 + count($err_arr)] = $array;
                Session::put('import_member_error', $err);
                return $array;
            }
        }
        else
        {
            $required_fields = true;
            $remarks = "Company is required";
            $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
            $err[0 + count($err_arr)] = $array;
            Session::put('import_member_error', $err);
            return $array;  
        }

        if(isset($request->company_deployment))
        {
            $deployments = CompanyDeployment::where('company_id',$company->id)->where('name',$request->company_deployment)->first();

            if(!$deployments)
            {
                $required_fields = true;
                $remarks = "Deployment Doesn't Exist.";
                $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
                $err[0 + count($err_arr)] = $array;
                Session::put('import_member_error', $err);
                return $array;
            }
        }
        else
        {
            $required_fields = true;
            $remarks = "Deployment is required";
            $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
            $err[0 + count($err_arr)] = $array;
            Session::put('import_member_error', $err);
            return $array;            
        }

        $payment_mode = PaymentMode::where('name',$request->payment_mode)->first();


        if(isset($request->payment_mode))
        {
            if(!$payment_mode)
            {
                $required_fields = true;
                $remarks = "Payment Mode Doesn't Exist.";
                $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
                $err[0 + count($err_arr)] = $array;
                Session::put('import_member_error', $err);
                return $array;
            }
        }
        else
        {
            $required_fields = true;
            $remarks = "Payment Mode is required";
            $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
            $err[0 + count($err_arr)] = $array;
            Session::put('import_member_error', $err);
            return $array;
        }

        $coverage_plan = CoveragePlan::where('name',$request->coverage_plan)->first();
        if(isset($request->coverage_plan))
        {
            if(!$coverage_plan)
            {
                $required_fields = true;
                $remarks = "Coverage Plan Doesn't Exist.";
                $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
                $err[0 + count($err_arr)] = $array;
                Session::put('import_member_error', $err);
                return $array;
            }
        }
        else
        {
            $required_fields = true;
            $remarks = "Coverage plan is required";
            $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
            $err[0 + count($err_arr)] = $array;
            Session::put('import_member_error', $err);
            return $array;
        }

        $email_address = $request->email ? $request->email : 'dummy_'.date('mdyhms').'@email.me';

        $email = Member::where('email',$email_address)->first();

        if($email)
        {
            $required_fields = true;
            $remarks = "Email is Already Exist.";
            $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
            $err[0 + count($err_arr)] = $array;
            Session::put('import_member_error', $err);
            return $array;
        }

        $status = $request->status;

        if(!$status)
        {
            $required_fields = true;
            $remarks = "Status is required";
            $array = self::forArray($remarks, $color, $request->all(), $request->universal_id, $request->carewell_id, $sample['converted']);
            $err[0 + count($err_arr)] = $array;
            Session::put('import_member_error', $err);
            return $array;
        }
        if(!$required_fields)
        {  
            $model = new Member();

            $universal_id = $request->universal_id ? $request->universal_id : "";
            
            $carewell_id = $request->carewell_id ? $request->carewell_id : "";
            $checkIfIdExist = $model->member_company()->where('universal_id',$universal_id)->orWhere('carewell_id',$carewell_id)->first();
            if(!$checkIfIdExist)
            {
                // $date_excel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['birth_date']);

                // $date =  date_format($date_excel,"d/m/Y");

                $member = $model->create([
                    'first_name'      			=> $request->first_name,
                    'middle_name' 				=> $request->middle_name,
                    'last_name'      			=> $request->last_name,
                    'mothers_maiden_name' 		=> $request->mothers_maiden_name,
                    'birth_date'      			=> $sample['converted'] ? $sample['converted'] : "",
                    'gender' 					=> $request->gender == 'MALE' ? 'Male':'Female',
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
                
                $id = $member->id;
                if($request->birth_date)
                {
                    $model->setId(array(
                        'universal_id'  => $request->universal_id,
                        'birth_date'    => date('m/d/Y', strtotime($sample['converted'])),
                        'first_name'    => $request->first_name,
                        'middle_name'   => $request->middle_name,
                        'last_name'     => $request->last_name,
                        'company_code'  => $company->code,
                        'billing_date'  => date("mdy"),
                        'member_id'     => $id,
                    ));
                    
                    $universal_id = $request->universal_id ? $request->universal_id : $model->modelAction('generateUniversalId');
                    
                    $carewell_id = $request->carewell_id ? $request->carewell_id : $model->modelAction('generateUniversalId');
                }

                $member->member_company()->create([
                    'universal_id'      		=> $universal_id,
                    'carewell_id'               => $carewell_id,
                    'member_id'                 => $id,
                    'company_ref_id' 			=> $request->employee_number,
                    'company_id' 				=> $company->id,
                    'company_deployment_id' 	=> $deployments->id,
                    'payment_mode_id' 			=> $payment_mode->id,
                    'coverage_plan_id' 			=> $coverage_plan->id,
                ]);

                $remarks = "New Member Added.";
                $color = 'green';

                $model = null ;
            }
            else
            {
                $remarks = "Duplicate Data";
                $color = 'red';
            }
            $array = self::forArray($remarks, $color, $request->all(), $universal_id, $carewell_id, $sample['converted']);

            return $array;
        }
    }
    
    public function exportation(Request $request)
    {
        // return $request->all();
        $member = new Member();
        $member = $member::where('is_archived', $request->status)->with('member_company','dependents')->get()->toArray();
        // return $member[0]['dependents'];
        return Excel::download(new MemberExportation($member), 'Member_export.xlsx');  
    }

    private function date_validation($date_value)
    {
        if(is_numeric($date_value))
        {
            $date_from_excel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date_value);
            $date_formatted =  date_format($date_from_excel,"d/m/Y");
        }
        else
        {
            $date_formatted = $date_value;
        }

        return $date_formatted;
    }

    public function computeDate($date)
    {
        // $convert_date = date('m/d/Y', strtotime($date));
        // $converted_date['converted'] = self::date_validation($date); 

        // $converted_date['converted'] = $date;
        // $date_explode = explode("/", $converted_date['converted']);
        // $converted_date['boolean'] = checkdate($date_explode[0],$date_explode[1],$date_explode[2]);

        $import_date = $date;

        $test_date = date('m/d/Y', strtotime($date));

        //check even the $date is manipulated it is still the same as $import_date
        if($import_date == $test_date)
        {
            $convert_date = date( 'Y-m-d', strtotime($date) );

            return $convert_date;
        }

        return null;


        

        // $date = checkdate($date_explode[0],$date_explode[1],$date_explode[2]) ? $convert_date : null;


    }
    
    public static function forArray($remarks, $color, $request, $universal_id, $carewell_id, $birthdate)
    {
        $array = [];
        $array['remarks']               = $remarks;
        $array['color']                 = $color;
        $array['universal_id']          = $universal_id ? $universal_id : "";
        $array['carewell_id']           = $carewell_id ? $carewell_id : "";
        $array['first_name']            = isset($request['first_name']) ? $request['first_name'] : "";
        $array['middle_name']           = isset($request['middle_name']) ? $request['middle_name'] : "";
        $array['last_name']             = isset($request['last_name']) ? $request['last_name'] : "";
        $array['mothers_maiden_name']   = isset($request['mothers_maiden_name']) ? $request['mothers_maiden_name'] : "";
        $array['date']                  = $birthdate ? $birthdate : "";
        $array['gender']                = isset($request['gender']) ? $request['gender'] : "";
        $array['marital_status']        = isset($request['marital_status']) ? $request['marital_status'] : "";
        $array['contact_number']        = isset($request['contact_number']) ? $request['contact_number'] : "";
        $array['email']                 = isset($request['email']) ? $request['email'] : "";
        $array['permanent_address']     = isset($request['permanent_address']) ? $request['permanent_address'] : "";
        $array['present_address']       = isset($request['present_address']) ? $request['present_address'] : "";
        $array['company']               = isset($request['company']) ? $request['company'] : "";
        $array['company_deployment']    = isset($request['company_deployment']) ? $request['company_deployment'] : "";
        $array['payment_mode']          = isset($request['payment_mode']) ? $request['payment_mode'] : "";
        $array['coverage_plan']         = isset($request['coverage_plan']) ? $request['coverage_plan'] : "";
        $array['sss_number']            = isset($request['sss_number']) ? $request['sss_number'] : "";
        $array['tin']                   = isset($request['tin']) ? $request['tin'] : "";
        $array['philhealth_number']     = isset($request['philhealth_number']) ? $request['philhealth_number'] : "";
        $array['pagibig_number']        = isset($request['pagibig_number']) ? $request['pagibig_number'] : "";
        return $array;
    }

    public function testing(Request $request)
    {   
        return $request->all();
    }
                
}