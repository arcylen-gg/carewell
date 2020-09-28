<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\PreExisting;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

class PreExistingController extends BaseController
{
    private $model;

    public function __construct(PreExisting $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){
        $this->model = $this->model->with([
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
            "name"    => "bail|required|unique:pre_existings",
            "number"         => "bail|required|unique:pre_existings",
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
            $message = 'Pre-existing has been successfully created!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name." has been added to pre-existing.";
            AuditTrailService::createAuditTrail('Added Pre-existing', $description, null ,$new_data,$this->model->id,'App\Models\PreExisting');
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
            $description = $request->name." pre-existing has been " .strtolower($response).".";
            AuditTrailService::createAuditTrail($response.' Pre-existing', $description, $old_data ,$new_data,$id,'App\Models\PreExisting');
            return $this->archiveData($this->model, "pre_existing", $id);
        }
        
        $validator = Validator::make($request->all(), [
            "name"      =>  [
                                "required",
                                Rule::unique('pre_existings')->ignore($id),
                            ],
            "number"    =>  [
                                "required",
                                Rule::unique('pre_existings')->ignore($id),
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
            $message = 'Pre-existing has been successfully updated!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." pre-existing has been updated.";
            AuditTrailService::createAuditTrail('Update Pre-existing', $description, $old_data ,$new_data,$id,'App\Models\PreExisting');
        }
 
        return response($message, $code);
    
    }
}
