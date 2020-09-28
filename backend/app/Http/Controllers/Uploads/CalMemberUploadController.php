<?php

namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Uploads;
use App\Http\Controllers\Controller;
use App\Exports\CalExport;
use App\Imports\CalImport;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Cal;
use App\Models\CalMember;
use App\Models\Member;
use App\Models\MemberCompany;
use App\Models\CompanyDeployment;
use App\Models\CoveragePlan;
use App\Models\Company;
use App\Models\CompanyCoveragePlan;
use Validator;
use Illuminate\Validation\Rule;
use DB;

class CalMemberUploadController extends Controller
{
    public function getCalMemberList(Request $request)
    {
        $path   = $request->file->storeAs('uploads',$request->file->getClientOriginalName());
        $import = new CalImport();
        $array  = $import->toArray($path);
        // $items  = json_decode($request->items,true);
        return $array[0];
    }
	public function import(Request $request) 
    {
        $saving_status = '';
        $member        = '';
        $deployment    = '';
        $carewell_id   = '';
        $coverage_plan = '';
        $color         = 'red';
        $has           = false;
        $filtered      = null;

        if(!isset($request->last_name) && !isset($request->first_name))
        {
            return Self::forArray('Invalid Member Name.', $color, $request->all());
        }

        $deployment = CompanyDeployment::where('company_id',$request->cal_data['company_id'])->where('name',$request->deployment)->first();
        if(!$deployment)
        {
            return Self::forArray('Invalid Deployment Name.', $color, $request->all());
        }
        
        if($request->birth_date)
        {
            $birthdate['converted'] = self::computeDate($request->birth_date);
            if(!$birthdate['converted'])
            {
                return Self::forArray('Invalid Birth Date Value.', $color, $request->all());
            }
        }
        $newMember = array();
        if($request->universal_id == null)
        {
            $birthdate['converted'] = self::computeDate($request->birth_date);
            $newMember = $this->createNew($request->all(),$request->cal_data,$deployment->id,$birthdate);
        }

        $universal_id = empty($request->universal_id)  ? $newMember->company->universal_id : $request->universal_id;
        $first_name = empty($request->universal_id)  ? $newMember->first_name : $request->first_name;
        $last_name = empty($request->universal_id)  ? $newMember->last_name : $request->last_name;
        $member = collect(Member::where('first_name',$first_name)->where('last_name',$last_name)->where('id', $request->member_code)->first());
        // $filtered = $member->first(function ($memberData,$memberKey) use($universal_id) {
        //     return $memberData['company']['universal_id'] == $universal_id;
        // });

        $check = CalMember::with('cal')->where('cal_id','!=', $request->cal_data['id'])->where('member_id',$request->member_code)->get();
        if($check)
        {
            foreach ($check as $checkKey => $checkValue) 
            {
                if($check[$checkKey]['cal']['status'] != 'close')
                {
                    $has = true;
                }
            }

            $saving_status      = $has ? 'The member is already in other CAL!' : 'Successfully Added.';
            $color              = $has ? 'red' : 'green';
        }
        else
        {
            $saving_status      = 'Successfully Added.';
            $color              = 'green';
        }

        $array['payment_mode'] = isset($member['company']['payment_mode']['name']) ? $member['company']['payment_mode']['name'] : $request->payment_mode;
        $array['universal_id'] = isset($member['company']['universal_id']) ? $member['company']['universal_id'] : $request->universal_id;
        $array['member_status'] = $member['is_archived'];
        $array['coverage_plan_id'] = $member['company']['coverage_plan_id'];
        $array['deployment'] = $deployment != '' ? $deployment->name : '';
        $array['birth_date'] = $birthdate['converted'];
        $array['saving_status'] = $saving_status;
        $array['color'] = $color;
        $array['last_name'] = $request->last_name;
        $array['first_name'] = $request->first_name;
        $array['middle_name'] = $request->middle_name ;
        $array['monthly_premium'] = $request->monthly_premium;
        $array['payment_amount'] = $request->payment_amount;
        $array['status'] = $request->status;
        $array['member_id'] = $request->member_code;
        $array['coverage_plan'] = $member['company']['coverage_plan']['name'];
        return $array;
    }
    public function export(Request $request)
    {  
        $id = $request->company_id;
        $payment = $request->payment_id;
        $cal_id = $request->cal_id;
        $cal = DB::table('cals')->leftJoin('companies','companies.id','cals.company_id')
                                ->leftJoin('company_deployments','company_deployments.id','cals.deployment_id')
                                ->selectRaw('companies.name as company_name, company_deployments.name as deployment_name, cal_number')
                                ->where('cals.id', $cal_id)->first();
        $deployment_id = DB::table('cals')->where('id', $cal_id)->value('deployment_id');
        $arrayData = [];
        $member_company = DB::table('member_companies')
            ->leftJoin('members', 'member_id', '=', 'members.id')
            ->leftJoin('coverage_plans', 'coverage_plan_id', '=', 'coverage_plans.id')
            ->leftJoin('company_deployments', 'company_deployment_id', '=', 'company_deployments.id')
            ->leftJoin('payment_modes', 'payment_mode_id', '=', 'payment_modes.id')
            ->where(['member_companies.company_id'=>$id, 'member_companies.payment_mode_id'=>$payment])
            ->select('coverage_plans.name as coverage_plan_name','company_deployments.name as deployment_name',
                'universal_id', 'monthly_premium', 'members.first_name', 'members.middle_name', 'members.last_name','member_id',
                'members.birth_date', 'members.is_archived', 'payment_modes.name as payment_mode', 'payment_mode_id');

        if($deployment_id)
        {
            $member_company = $member_company->where('company_deployment_id',$deployment_id);
        }

        $member_company = $member_company->get();

        $company = DB::table('companies')->where(['is_archived'=>0, 'id'=>$id])->get();
        foreach ($company as $key => $value) {
            $_deployment = DB::table('company_deployments')
                ->where('company_id',$value->id);
            if($deployment_id)
            {
                $_deployment = $_deployment->where('id', $deployment_id);
            }
            $value->company_deployment = $_deployment->get();

            $value->coverage_plan = DB::table('company_coverage_plans')
                ->leftJoin('coverage_plans', 'company_coverage_plans.coverage_plan_id' ,'=', 'coverage_plans.id')
                ->where('company_id',$value->id)
                ->get();
        }
        $cal_info['company'] = $company[0];
        $cal_info['member'] = $member_company;
        $cal_info['payment_id'] = $payment;
        if($cal)
        {
            return Excel::download(new CalExport($cal_info), $cal->cal_number.'_Cal_export_member_'.$cal->company_name.'_'.$cal->deployment_name.'.xlsx');   
        }
        else
        {
            dd("CAL not found!");
        }
    }
    public function getData($table_name, $select_column, $value)
    {
        return DB::table($table_name)
        ->where($select_column,$value)
        ->first();
    }
    public function updateCal(Request $request)
    {
        $cal = new Cal();
        $cal = $cal->find($request->id);
        $cal->status = 'pending';
        $cal->save();
    }
    public function createNew($data,$items,$deployment,$birthdate)
    {
        $newMember = new Member();
        $newMember = $newMember->create([
            'first_name'      			=> $data['first_name'],
            'middle_name' 				=> isset($data['middle_name']) ? $data['middle_name'] : null,
            'last_name'      			=> $data['last_name'],
            'mothers_maiden_name' 		=> null,
            'birth_date'      			=> $birthdate['converted'],
            'gender' 					=> null,
            'marital_status'      		=> null,
            'contact_number' 			=> null,            
            'email'      				=> null,   
            'permanent_address' 		=> null,
            'present_address'      		=> null, 
            'sss_number'      			=> null,        
            'tin' 						=> null, 
            'philhealth_number'      	=> null,
            'pagibig_number' 			=> null,        
            'is_archived'               => $data['status'] === "Active" ? 0 : 1, 
        ]); 

        $memberController = new Member();
        
        $birthdate = date_format(date_create($birthdate['converted']),"m/d/Y");

        $memberController->setId(array(
            'universal_id'  => null,
            'birth_date'    => $birthdate,
            'first_name'    => $data['first_name'],
            'middle_name'   => isset($data['middle_name']) ? $data['middle_name'] : null,
            'last_name'     => $data['last_name'],
            'company_code'  => null,
            'billing_date'  => date("mdy"),
            'member_id'     => $newMember->id
        ));
        $universal_id = $memberController->modelAction('generateUniversalId');

        $coverage_plan = $this->getCoveragePlan($data['coverage_plan'],$items['company_id']);

        $newMember->member_company()->create([
            'universal_id'      		=> $universal_id,
            'member_id'                 => $newMember->id,
            'company_id' 				=> $items['company_id'],            
            'company_deployment_id' 	=> $deployment,        
            'payment_mode_id' 			=> $items['payment_mode_id'],
            'coverage_plan_id' 			=> $coverage_plan->id,    
            'company_ref_id' 			=> null, 
        ]);
        
        return $newMember;
    }

    public static function getCoveragePlan($coverage_plan_name,$company_id)
    {
        $company = Company::with('coverage_plan')->find($company_id);
        $companyCollect = collect($company->coverage_plan);
        $companyFilterCoverage = $companyCollect->first( function($value,$key) use ($coverage_plan_name){
            $return = $value->name == $coverage_plan_name;
            return $return;
        });

        return $companyFilterCoverage;
    }

    public function computeDate($date)
    {
        // $converted_date['converted'] = self::date_validation($date);
        // $date_explode = explode("/", $converted_date['converted']);
        // $converted_date['boolean'] = checkdate($date_explode[0],$date_explode[1],$date_explode[2]);
        // return $converted_date;

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

    private function date_validation($date_value)
    {
        if(is_numeric($date_value))
        {
            $date_from_excel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date_value);
            $date_formatted =  date_format($date_from_excel,"d/m/Y");
        }
        else
        {
            $date_formatted = date_format(date_create($date_value),"m/d/Y");
        }

        return $date_formatted;
    }

    public function paymentMode($id)
    {
        $payment = "";
        if($id == 1)
        { $payment = "Semi Monthly"; }
        
        if($id == 2)
        { $payment = "Monthly"; }

        if($id == 3)
        { $payment = "Quarterly"; }

        if($id == 4)
        { $payment = "Semestral"; }

        if($id == 5)
        { $payment = "Annually"; }
        return $payment;
    }

    public function forArray($remarks, $color, $request)
	{
        $empty = null;
        return [
            'remarks'                   => $remarks,
            'color'                     => $color,
            'universal_id'              => $request['universal_id'] ?? $empty,
            'last_name'                 => $request['last_name'] ?? $empty,
            'first_name'                => $request['first_name'] ?? $empty,
            'middle_name'               => isset($request['middle_name']) ? $request['middle_name'] : $empty,
            'birth_date'                => $request['birth_date'] ?? $empty,
            'coverage_plan_name'        => $request['coverage_plan_name'] ?? $empty,
            'deployment_name'           => $request['deployment_name'] ?? $empty,
            'payment_mode'              => $request['payment_mode'] ?? $empty,
            'payment_amount'            => $request['payment_amount'] ?? $empty,
            'status'                    => $request['status'] ?? $empty,
        ];
    }
}
