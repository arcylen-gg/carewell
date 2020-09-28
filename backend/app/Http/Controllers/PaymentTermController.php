<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

class PaymentTermController extends BaseController
{
    private $model;

    public function __construct(PaymentTerm $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){
        $this->model = $this->model->with([
            // 'user_position'
        ]); //lazy loader
    }

    public function index(Request $request)
    {
        // $this->loadWithRelatedModel();
        $this->filterTable($request->all());
        return $this->getResultList($this->model->latest());
    }

    public function filterTable()
    { 
        $related_fields             = [""];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name","name","number"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"    => "bail|required|unique:payment_terms",
            "number"         => "bail|required|unique:payment_terms",
        ]);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $pre_existing                   = $this->model;
            $pre_existing->name            = $request->name;	 
            $pre_existing->number            = $request->number;	 

            $pre_existing->save();
            $message = 'Payment term has been successfully created!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name." has been added to payment term.";
            AuditTrailService::createAuditTrail('Added Payment Term', $description, null ,$new_data,$this->model->id,'App\Models\PaymentTerm');
        }
 
        return response($message, $code);
    }

    public function update(Request $request,$id)
    {
        $old_data = AuditTrailService::getDataForAuditTrail($this->model,$id);

        if($request->for_archive)
        {
            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $new_data->is_archived = $new_data->is_archived ? 0 : 1;
            $response = $new_data->is_archived ? 'Archive' : 'Restore';
            $description = $request->name." payment term has been ".strtolower($response).".";
            AuditTrailService::createAuditTrail($response.' Payment Term', $description, $old_data ,$new_data,$id,'App\Models\PaymentTerm');

            return $this->archiveData($this->model, "pre_existing", $id);
        }
        
        $validator = Validator::make($request->all(), [
            "name"      =>  [
                                "required",
                                Rule::unique('payment_terms')->ignore($id),
                            ],
            "number"    =>  [
                                "required",
                                Rule::unique('payment_terms')->ignore($id),
                            ],
        ]);

        $update_tagged = $this->validateArchiveTagged($this->model, "pre_existing_id", $id);

        if($update_tagged)
        {
            return response($update_tagged["message"], $update_tagged["code"]);
        }

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $pre_existing                   = $this->model->find($id);
            $pre_existing->name            = $request->name;	 
            $pre_existing->number            = $request->number;	 

            $pre_existing->save();
            $message = 'Payment term has been successfully updated!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." payment term has been updated.";
            AuditTrailService::createAuditTrail('Update Payment Term', $description, $old_data ,$new_data,$id,'App\Models\PaymentTerm');
        }
 
        return response($message, $code);
    
    }
}
