<?php

namespace App\Http\Controllers;

use App\Models\Cal;
use App\Models\Company;
use App\Models\PaynmentMode;
use App\Globals\AuditTrailService;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;

class CalController extends BaseController
{
    private $model;

    public function __construct(Cal $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){
        $this->model = $this->model->with([
            'payment_mode','payment_term','company','company_deployment'
        ]); //lazy loader
        
    }
    public function show(Request $request, $id)
    {
        $this->model = $this->model;
        return $this->model->with(['cal_member.member'])->find($id);
    }
    public function index(Request $request)
    {
        $this->loadWithRelatedModel();
        $this->filterTable($request->all());
        return $this->getResultList($this->model->latest());
    }

    public function filterTable()
    { 
        $related_fields             = [""];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name",'cal_number'],[['company_id','company_id']]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "company_id"        => "bail|required|",
            // "reference_number"  => "bail|required|",
        ]);

        if($validator->fails())
        {	
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
        	$cal_number = '';
        	$getNumber = Cal::where("cal_number",'like','%CAL-%')->get()->last();
        	if($getNumber){

        		$cal_number = explode('-', $getNumber->cal_number);
        		if(($cal_number[1]+1)>99){
        			$num = $cal_number[1]+1;
        			$cal_number = 'CAL-00'.$num;
        		}
        		if(($cal_number[1]+1)>9)
        		{
        			$num = $cal_number[1]+1;
        			$cal_number = 'CAL-000'.$num;
        		}else{
        			$num = $cal_number[1]+1;
        			$cal_number = 'CAL-0000'.$num;
        		}
        	}
        	else
        	{
        		$cal_number = 'CAL-00001';
        	}

            $cal            			= $this->model;
            $cal->cal_number  			= $cal_number;
            $cal->company_id			= $request->company_id;
            $cal->deployment_id         = $request->deployment_id;
            $cal->payment_term_id       = $request->payment_term_id;
            $cal->payment_mode_id		= $request->payment_mode_id;
            $cal->payment_start_date	= $request->start_date;
            $cal->payment_end_date		= $request->end_date;
            $cal->status             	= 'open';
            $cal->reference_number      = $request->reference_number;
            $cal->payment_cal_date      = $request->cal_date;
            $cal->payment_due_date      = $request->due_date;
            $cal->save();

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $cal_number." has been added in Billing and Collection Center.";
            AuditTrailService::createAuditTrail('Added Collection Active List', $description, null ,$new_data,$this->model->id,'App\Models\Cal');
            $message = 'Collection Active List has been successfully added!';
            $code = 200;
        }
 
        return response($message, $code);
    }

    public function update(Request $request,$id)
    {
        $old_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
        if($request->for_archive)
        {
            return $this->archiveData($this->model, "cal", $id);
        }

       $validator = Validator::make($request->all(), [
            "company_id"        => "bail|required|",
            // "reference_number"  => "bail|required|",
        ]);

        if($validator->fails())
        {	
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
        	// die(var_dump($request->all()));
            $cal                        = $this->model->find($id);
            $cal->cal_number  			= $request->cal_number;
            $cal->company_id			= $request->company_id;
            $cal->deployment_id         = $request->deployment_id;
            $cal->payment_term_id       = $request->payment_term_id;
            $cal->payment_mode_id		= $request->payment_mode_id;
            $cal->payment_start_date	= $request->payment_start_date;
            $cal->payment_end_date		= $request->payment_end_date;
            $cal->status             	= $request->status;
            $cal->reference_number      = $request->reference_number;
            $cal->payment_cal_date      = $request->payment_cal_date;
            $cal->payment_due_date      = $request->payment_due_date;
            $cal->save();

            $message = 'Collection active list has been successfully update!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->cal_number." (collection active list) has been updated.";
            AuditTrailService::createAuditTrail('Update Collection Active List', $description, $old_data ,$new_data,$id,'App\Models\Cal');
        }
 
        return response($message, $code);
    
    }
}
