<?php
namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoveragePlan;
use PDF;
use DB;

class CoveragePlanUploadController extends Controller
{
    public function export(Request $request)
    {
        // $data['data'] = CoveragePlan::with(['coverage_plan_benefits.benefit_type','coverage_plan_benefits.coverage_plan_procedures.procedures'],'pre_existing')->find($request->id);
        $data['user'] = $request->user;
        // dd($data['data']);

        $coverage_plan = DB::table('coverage_plans')->find($request->id);

        $age = unserialize($coverage_plan->age_bracket);
        $coverage_plan->age_bracket_from = $age[0];
        $coverage_plan->age_bracket_to = $age[1];

        $coverage_plan->pre_existing = DB::table('pre_existings')->find($coverage_plan->pre_existing_id);

        $coverage_plan->coverage_plan_benefits = DB::table('coverage_plan_benefits')->where('coverage_plan_id',$coverage_plan->id)->get();
        foreach ($coverage_plan->coverage_plan_benefits as $key => $value) {
            //get benefit type of coverage plan
            $benefit_type_id = $value->benefit_type_id;
            $value->benefit_type = DB::table('benefit_types')->find($benefit_type_id);

            //get procedure of coverage plan
            $coverage_plan_benefit_id = $value->id;
            $value->coverage_plan_procedures = DB::table('coverage_plan_procedures')->where('coverage_plan_benefit_id', $coverage_plan_benefit_id)->get();

            foreach ($value->coverage_plan_procedures as $key_procedure => $value_procedure) {
                $procedure_id = $value_procedure->procedure_id;
                $value_procedure->procedures = DB::table('procedures')->find($procedure_id);
            }
        }
        
        $data['data'] = $coverage_plan;

        return view('exports.coverage_plan_export_template', $data);
        // dd($coverage_plan);
        // return $data;
        // $pdf = PDF::loadView('exports.coverage_plan_export_template',$data)->setPaper('A4', 'potrait')->setWarnings(false);
        // return $pdf->stream();
    }
}