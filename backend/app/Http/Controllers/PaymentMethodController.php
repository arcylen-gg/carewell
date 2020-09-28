<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

class PaymentMethodController extends BaseController
{
    private $model;

    public function __construct(PaymentMethod $model)
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
        $search_inexact_fields      = [["name","name"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"    => "bail|required|unique:payment_methods",
        ]);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $payment_method                   = $this->model;
            $payment_method->name            = $request->name;	 

            $payment_method->save();
            $message = 'Payment method has been successfully created!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name." has been added to payment method.";
            AuditTrailService::createAuditTrail('Added Payment Method', $description, null ,$new_data,$this->model->id,'App\Models\PaymentMethod');
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
            $description = $request->name ." payment method has been ". strtolower($response) .".";
            AuditTrailService::createAuditTrail($response.' Payment Method', $description, $old_data ,$new_data,$id,'App\Models\PaymentMethod');
            return $this->archiveData($this->model, "payment method", $id);
        }

        $validator = Validator::make($request->all(), [
            "name"         =>  [
                                    "required",
                                    Rule::unique('payment_methods')->ignore($id),
                                ],
        ]);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $payment_method                   = $this->model->find($id);
            $payment_method->name            = $request->name;	 

            $payment_method->save();
            $message = 'Payment method has been successfully updated!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." payment method has been updated.";
            AuditTrailService::createAuditTrail('Update Payment Method', $description, $old_data ,$new_data,$id,'App\Models\PaymentMethod');
        }
 
        return response($message, $code);
    
    }
}
