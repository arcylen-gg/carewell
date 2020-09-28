<?php

namespace App\Http\Controllers\ModelQueryController;
use App\Models\CoveragePlan;
use App\Models\CoveragePlanBenefit;
use App\Models\CoveragePlanProcedure;
use App\Models\Provider;
use App\Models\Member;
use Illuminate\Http\Request;

use App\Models\Availment;

use DB;

class AvailmentQueryController extends FilterQueryController
{
    public function getMemberCoveragePlanBenefit($id){
        $model = CoveragePlan::with(['coverage_plan_benefits.benefit_type']);
        return $model->find($id);
    }

    public function getBenefitTypeProcedure($id){
        $model = CoveragePlanProcedure::with(['procedures']);
        $request['coverage_plan_benefit_id'] = $id;
        return $this->filterBaseTableModel($model,$request);
    }

    public function getProviderPayeeAndDoctor($id){
        $model = Provider::with(['provider_payee','doctor_providers.doctor']);
        return $model->find($id);
    }
    
    public function getMemberAvailment($id){
        $model = Member::with(['availment.availment_procedure','member_company']);
        return $model->find($id);
    }

    public function getMemberAvailmentOptions(Request $request){
        $is_archived = $request->is_archived;
        $name = $request->name;

        $get_member_id =  DB::table('members')
        ->leftJoin('member_companies','member_companies.member_id','=','members.id')
        ->where('is_archived',$is_archived)
        ->where('first_name','like','%'.$name.'%')
        ->orWhere('middle_name','like','%'.$name.'%')
        ->orWhere('last_name','like','%'.$name.'%')
        ->orWhere('carewell_id','like','%'.$name.'%')
        ->orWhere('universal_id','like','%'.$name.'%')
        ->select('members.id','members.first_name','members.middle_name','members.last_name','members.birth_date','members.is_archived')
        // ->select('members.id')
        ->distinct()
        ->orderBy('members.first_name')
        ->orderBy('members.middle_name')
        ->orderBy('members.last_name')
        ->get();

        $data = [];

        foreach ($get_member_id as $key => $value) {
            // $data[$key] = Member::find($value->id);
            $data[$key]['id'] = $value->id;
            $data[$key]['first_name'] = $value->first_name;
            $data[$key]['middle_name'] = $value->middle_name;
            $data[$key]['last_name'] = $value->last_name;
            $data[$key]['fullname'] = Self::setFullName($value->first_name, $value->middle_name, $value->last_name);
            
            $data[$key]['birth_date'] = $value->birth_date;
            
            $data[$key]['age'] = Self::setAge($value->birth_date);

            $data[$key]['is_archived'] = $value->is_archived;

            $get_company = DB::table('member_companies')->where('member_id',$value->id)->latest()->first();
            $data[$key]['company'] = $get_company;
            $data[$key]['company']->company = DB::table('companies')->find($get_company->company_id);
            $data[$key]['company']->company_deployment = DB::table('company_deployments')->find($get_company->company_deployment_id);

            $get_coverage_plan = DB::table('coverage_plans')->find($get_company->coverage_plan_id);
            $data[$key]['company']->coverage_plan = $get_coverage_plan;

            $get_availment = DB::table('availments')->where('member_id',$value->id)->get();
            $data[$key]['availment'] = $get_availment;

            // $data[$key]['current_balance'] = Self::setCurrentBalance($get_availment, $get_coverage_plan, $value->id);
            // $data[$key]['current_balance'] = $get_coverage_plan->maximum_benefit_limit;
        }

        return $data;
    }

    private static function setFullName($first_name, $middle_name, $last_name){
        return ucwords("{$first_name} {$middle_name} {$last_name}");
    }

    private static function setAge($birth_date){
        $dob = new \DateTime($birth_date);
        $now = new \DateTime();
        $difference = $now->diff($dob);
        $age = $difference->y;

        return $age;
    }

    private static function setCurrentBalance($availment, $coverage_plan, $id){
        if(count($availment) == 0)
        {
            $current_balance = $coverage_plan->maximum_benefit_limit;
        }
        else
        {
            $current_balance = DB::table('availments')->where('member_id',$id)->where('status','closed')->sum('grand_total');
        }

        return $current_balance;
    }
}