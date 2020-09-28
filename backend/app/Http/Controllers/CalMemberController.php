<?php

namespace App\Http\Controllers;

use App\Models\CompanyDeployment;
use App\Models\CalMember;
use App\Models\Member;
use App\Models\MemberCompany;
use App\Models\Company;
use App\Models\CoveragePlan;
use App\Models\PaynmentMode;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Models\Cal;

class CalMemberController extends Controller
{
	private $model;

    public function __construct(CalMember $model)
    {
        $this->model = $model;
    }
     public function store(Request $request)
    {
		$member_company = MemberCompany::where('universal_id',$request->universal_id)->first();
		if(!$member_company)
		{
			$member_company = MemberCompany::where('member_id',$request->member_id)->first();
		}
		
		$cal_member								= new CalMember();
		$cal_member->cal_id						= $request->cal_id;
		$cal_member->member_id					= $member_company->member_id;
		$cal_member->deployment_id				= $member_company->company_deployment_id;
		$cal_member->carewell_id				= isset($member_company->carewell_id) ? $member_company->carewell_id : null;
		$cal_member->monthly_premium			= $request->monthly_premium;
		$cal_member->period_count				= 0;
		$cal_member->date_paid					= date("Y-m-d");
		$cal_member->paid_amount				= isset($request->payment_amount) ? $request->payment_amount : 0;
		$cal_member->status						= $request->status == 'Active' ? 0 : 1;
		$cal_member->save();

		$member 				= new Member();
		$member 				= $member->find($member_company->member_id);
		$member->is_archived 	= $request->status == 'Active' ? 0 : 1;
		$member->save();

		$message = 'Collection active list member successfully imported!';
		$code = 200;
    	

		//getting total paid amount of member and save to cal
		$getTotal = $this->model::where('cal_id',$request->cal_id)->sum('paid_amount');
		$cal 						= new Cal();
		$cal 						= $cal->find($request->cal_id);
		$cal->total_amount_paid 	= $getTotal;
		$cal->save();

        return response($message, $code);
	}

	public function destroy(Request $request, $id)
	{
		$delete_ids = collect(CalMember::where("cal_id", $id)->get())->pluck('id')->all();
		CalMember::destroy($delete_ids);
		return 'done';
	}
}
