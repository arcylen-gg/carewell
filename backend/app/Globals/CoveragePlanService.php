<?php
namespace App\Globals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use DB;
use Carbon\Carbon;

class CoveragePlanService
{
	public static function get_coverage_info($coverage_plan_id, $benefit_type_id = '')
	{
		$data['coverage_plan'] = DB::table('coverage_plans')
										  ->where('coverage_plans.id', $coverage_plan_id)
										  ->first();
		$data['benefit_data'] = DB::table('coverage_plan_benefits')
										  ->where('coverage_plan_id', $coverage_plan_id);

		$data['_procedures']  = DB::table('coverage_plan_benefits')
										  ->leftjoin('coverage_plan_procedures','coverage_plan_benefit_id','=','coverage_plan_benefits.id')
										  ->leftjoin('procedures','procedures.id','=','procedure_id')
										  ->where('coverage_plan_id', $coverage_plan_id);
		if($benefit_type_id)
		{
			$data['_procedures'] = $data['_procedures']->where('benefit_type_id', $benefit_type_id);
			$data['benefit_data'] = $data['benefit_data']->where('benefit_type_id', $benefit_type_id);
		}
		$data['_procedures'] = $data['_procedures']->get();
		$data['benefit_data'] = $data['benefit_data']->first();
		return $data;
	}
}