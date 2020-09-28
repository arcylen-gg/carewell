<?php

namespace App\Http\Controllers;

use App\Models\Cal;
use App\Models\Member;
use App\Models\MemberCompany;
use App\Models\Document;
use App\Models\CalStatus;
use Illuminate\Http\Request;
use App\Globals\DocumentService;
use App\Globals\AuditTrailService;
use Carbon\Carbon;
use DB;
class CalStatusController extends Controller
{
	private $model;

    public function __construct(CalStatus $model)
    {
        $this->model = $model;
    }
    public function store(Request $request)
    {
		$json = json_decode($request->items,true);

		$is_close = Cal::where('status','close')->where('id',$json['id'])->first();
		if($is_close)
		{
			$message = 'The status is already close!';
			$code = 300;
		}
		else
		{
			foreach ($json['cal_members'] as $key => $value) 
			{
				$member = Member::find($value['member_id']);
				
				
				if($member)
				{
					$member->setId(array(
						'universal_id'  => null,
						'birth_date'    => $member['birth_date'],
						'first_name'    => $member['first_name'],
						'middle_name'   => $member['middle_name'],
						'last_name'     => $member['last_name'],
						'company_code'  => $json['company']['code'],
						'billing_date'  => date("mdy"),
						'member_id'		=> $member['id'],
					));
					
					$carewell_id = $member->modelAction('generateCarewellId');
					
					$member_company = collect(MemberCompany::where('member_id',$member['id'])->get())->last();
					$member_company->carewell_id = $carewell_id;
					$member_company->policy_date = date("y-m-d");
					$member_company->save();
				}
			}

			$cal = DB::table('cals')->where('id', $json['id'])->first();
			$cal_members = DB::table('cal_members')->where('cal_id', $json['id'])->get();
			foreach ($cal_members as $key => $value) 
			{
				$check_paid_count = DB::table('cal_paid_counts')
									  ->where('cal_id',$value->cal_id)
									  ->where('cal_member_id',$value->id)
									  ->first();
				if(!$check_paid_count)
				{
					$ins['amount'] = $value->paid_amount;
					$ins['cal_id'] = $value->cal_id;
					$ins['cal_member_id'] = $value->id;
					$ins['start_date'] = $cal->payment_start_date;
					$ins['end_date'] = $cal->payment_end_date;
					$ins['created_at'] = Carbon::now();
					$ins['updated_at'] = Carbon::now();
					DB::table('cal_paid_counts')->insert($ins);
				}
			}
			$cal_status							= $this->model;
			$cal_status->cal_id					= $json['id'];
			$cal_status->bank_id            	= $json['bank_id'];
			$cal_status->pending_date  			= date('Y-m-d');
			$cal_status->pending_remarks		= $json['remarks'];
			$cal_status->collection_date 		= $json['collection_date'];
			$cal_status->check_number			= $json['check_no'];
			$cal_status->check_date				= $json['check_date'];
			$cal_status->or_number				= $json['or_number'];
			$cal_status->amount             	= $json['amount'];
			$cal_status->save();

			DocumentService::upload($json['id']);

			$cal = new Cal();
			$old_data = AuditTrailService::getDataForAuditTrail($cal,$json['id']);
			$cal = $cal->find($json['id']);
			$cal->status = 'close';
			$cal->save();

			$message = 'Collection active list has been successfully change status to close!';
			$code = 200;

			$new_data = AuditTrailService::getDataForAuditTrail($cal,$json['id']);
			$description = $is_close['cal_number']." (collection active list) has been change status to close.";
            AuditTrailService::createAuditTrail('Close Collection Active List', $description, null, $new_data, $json['id'], 'App\Models\CalStatus');
		}
		
		return response($message, $code);
		
    }
    public function update(Request $request,$id)
    {
			$carewell_id = '';
			
			$old_data = AuditTrailService::getDataForAuditTrail(new Cal(),$id);

			$is_pending = Cal::where('status','pending')->where('id',$id)->first();

    		if($is_pending)
    		{
	 			$message = 'The status is already pending!';
	            $code = 300;
			}
			else
    		{
				$cal_members = Cal::with(['cal_member'])->find($id);
				foreach ($cal_members->cal_member as $key => $value) 
				{
					$member = Member::find($value['member_id']);
					if($member)
					{
	    				$member->setId(array(
							'universal_id'  => null,
							'birth_date'    => $member['birth_date'],
							'first_name'    => $member['first_name'],
							'middle_name'   => $member['middle_name'],
							'last_name'     => $member['last_name'],
							'company_code'  => $request->company["code"],
							'billing_date'  => date("mdy"),
							'member_id'		=> $member['id'],
						));
						$carewell_id = $member->modelAction('generateCarewellId');
						
						$member_company = collect(MemberCompany::where('member_id',$member['id'])->get())->last();
						$member_company->carewell_id = $carewell_id;
						$member_company->policy_date = date("y-m-d");
						$member_company->save();
	    			}
				}
				
				$cal_status            				= $this->model;
				$cal_status->cal_id					= $request->id;
	            $cal_status->pending_date  			= date('Y-m-d');
	            $cal_status->pending_remarks		= $request->remarks;
	            $cal_status->save();

	           	$cal = new Cal();
		        $cal = $cal->find($request->id);
		        $cal->status = 'pending';
				$cal->save();
				
	            $message = 'Collection active list has been successfully change status to pending!';
				$code = 200;
				
				$new_data = AuditTrailService::getDataForAuditTrail($cal,$request->id);
				$description = $request->cal_number." (collection active list) has been change status to pending.";
				AuditTrailService::createAuditTrail('Pending Collection Active List', $description, $old_data ,$new_data,$request->id,'App\Models\Cal');
        	}

        return response($message, $code);
    }


}
