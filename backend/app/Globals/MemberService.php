<?php
namespace App\Globals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use DB;
use Carbon\Carbon;

class MemberService
{

	public static function get_member_availment($member_id, $company_id, $coverage_plan_id, $diagnosis_id = null, $benefit_type_id = null, $this_year = null, $this_month = null, $availment_id = null)
	{
		$_get = DB::table('availments')->selectRaw('*, availments.id as availment_id')
									   ->leftjoin('members','availments.member_id','=','members.id')
		 							   ->where('availments.member_id', $member_id)
									   ->where('company_id', $company_id)
									   ->where('coverage_plan_id', $coverage_plan_id)
									   ->orderBy('members.id','DESC')
									   ->groupBy('availments.id');
		if($availment_id)
		{
			$_get = $_get->where('availments.id', '!=', $availment_id);			
		}
		if($benefit_type_id)
		{
			$_get = $_get->where('benefit_type_id', $benefit_type_id);
		}
		if($diagnosis_id)
		{
			$_get = $_get->where('diagnosis_id', $diagnosis_id);			
		}
		if($this_year)
		{
			$_get = $_get->whereYear('date_avail',date('Y',strtotime($this_year)));
		}
		if($this_month)
		{
			$_get = $_get->whereMonth('date_avail',date('m',strtotime($this_month)))
						 ->whereYear('date_avail',date('Y',strtotime($this_year)));
		}

		$_get = $_get->get();

		$_availment = array();										
		foreach ($_get as $key => $value) 
		{
			$_availment[$key] = $value;
			$_availment[$key]->procedures = Self::get_availment_procedures($value->availment_id);
			$_availment[$key]->doctors = Self::get_availment_doctors($value->availment_id);
		}
		return $_availment;
	}
	public static function get_availment_procedures($availment_id)
	{
		return  DB::table('availment_procedures')->leftjoin('procedures','procedures.id','availment_procedures.procedures_id')->where('availment_id', $availment_id)->get();
	}
	public static function get_availment_doctors($availment_id)
	{
		return  DB::table('availment_doctors')->where('availment_id', $availment_id)->get();
	}
	public static function get_member_procedures($member_id, $company_id, $coverage_plan_id, $benefit_type_id, $procedures_id, $diagnosis_id = null, $availment_id = null)
	{
		$ret = DB::table('availment_procedures')
					->leftjoin('procedures','procedures.id','availment_procedures.procedures_id')
					->leftjoin('availments','availments.id','availment_procedures.availment_id')
				    ->where('availments.member_id', $member_id)
				    ->where('company_id', $company_id)
				    ->where('coverage_plan_id', $coverage_plan_id)
					->where('benefit_type_id', $benefit_type_id)
					->where('procedures_id', $procedures_id);
		if($diagnosis_id)
		{
			$ret = $ret->where('diagnosis_id', $diagnosis_id);
		}
		if($availment_id)
		{

			$ret = $ret->where('availment_id', '!=', $availment_id);
		}
		return $ret->sum('carewell_charged');
	}
}