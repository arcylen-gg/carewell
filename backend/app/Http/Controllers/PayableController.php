<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\PayableAvailment;
use App\Models\Availment;

use Illuminate\Http\Request;

use App\Globals\AuditTrailService;

class PayableController extends BaseController
{
    private $model;

    public function __construct(Payable $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
    	$this->loadWithRelatedModel();
        $this->filterTable();
    	return $this->getResultList($this->model->latest());
    }
    
    public function filterTable()
    {
        $related_fields             = [];
        $inexact_fields             = ["name"];
        $search_inexact_fields      = [["name", "soa_number"],[["provider_id","provider_id"]]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }
    public function loadWithRelatedModel(){
        $this->model = $this->model->with(['provider','payable_availment','user']); //lazy loader
    }

    public function show($id){
        return $this->model->with([
            'provider',
            'payable_availment.availment.provider',
            'payable_availment.availment.member',
            'payable_availment.availment.benefit_type',
            'payable_availment.availment.diagnosis',
            'payable_availment.availment.availment_doctor.doctor',
            'payable_availment.availment.availment_procedure.procedure',
            'user'
        ])->find($id);
    }


    public function store(Request $request)
    {
        // return $request->all();
        $validator = $this->validateRequestInput(request()->all(), $this->model->requestRules());

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $payable = $this->model;
            $payable->provider_id = $request->provider_id;
            $payable->payment_term_id = $request->payment_term_id;
            $payable->user_id = $request->user_id;
            $payable->soa_number = $request->soa_number;
            $payable->received_date = $request->received_date;
            $payable->due_date = $request->due_date;
            $payable->save();

            foreach ($request->selected as $key => $value) {
                $payable_availment = new PayableAvailment();
                $payable_availment->payable_id = $payable->id;
                $payable_availment->availment_id = $value['id'];
                $payable_availment->payable_amount = isset($value['payable_amount']) ? $value['payable_amount'] : null ;
                $payable_availment->remarks = isset($value['remarks']) ? $value['remarks'] : null ;
                $payable_availment->save();

                $availment = Availment::find($value['id']);
                $availment->status = $value['payable_amount'] == $availment->grand_total ? 'close' : 'open';
                $availment->save();
            }

            $message = "Payable has been successfully created!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($payable,$payable->id);
            $description = $request->soa_number." has been added to payable.";
            AuditTrailService::createAuditTrail('Added Payable', $description, null ,$new_data,$payable->id,'App\Models\Payable');
        }

        return response($message, $code);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateRequestInput(request()->all(), $this->model->requestRules());

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $old_data = AuditTrailService::getDataForAuditTrail($this->model,$id);

            $payable = $this->model->find($id);
            $payable->provider_id = $request->provider_id;
            $payable->payment_term_id = $request->payment_term_id;
            $payable->user_id = $request->user_id;
            $payable->soa_number = $request->soa_number;
            $payable->received_date = $request->received_date;
            $payable->due_date = $request->due_date;
            $payable->save();

            //update availment to new to avoid conflict on showing of data
            $get_availment = PayableAvailment::where('payable_id',$id)->get();
            $availment_id = collect($get_availment)->pluck('availment_id');
            Availment::whereIn('id',$availment_id)->update(['status'=>'new']);

            $delete_availment = PayableAvailment::where("payable_id", $id)->delete();

            foreach ($request->selected as $key => $value) {
                $payable_availment = new PayableAvailment();
                $payable_availment->payable_id = $id;
                $payable_availment->availment_id = $value['id'];
                $payable_availment->payable_amount = $value['payable_amount'];
                $payable_availment->remarks = $value['remarks'];
                $payable_availment->save();

                $availment = Availment::find($value['id']);
                $availment->status = $availment->status === 'open' ? 'new' : 'open';
                $availment->save();
            }

            $message = "Payable has been successfully updated!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($payable,$payable->id);
            $description = $request->soa_number." payable has been updated.";
            AuditTrailService::createAuditTrail('Added Payable', $description, $old_data ,$new_data,$payable->id,'App\Models\Payable');
        }

        return response($message, $code);
    }
}
