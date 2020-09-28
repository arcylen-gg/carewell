<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\ProcedureType;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcedureTypeController extends BaseController
{
    protected $model;

    public function __construct(ProcedureType $model)
    {
    	$this->model = $model;
    }

    public function index(Request $request)
    {
        $this->model = $this->model->with(['procedures']);
        $this->filterTable($request->all());
    	return $this->getResultList($this->model->orderBy('id'));
    }
    
    public function filterTable()
    {
        $related_fields             = [];
        $inexact_fields             = ["name"];
        $search_inexact_fields      = [["name", "name"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    // 
    public function store(Request $request)
    { 
    	//die(var_dump($request->all()));
        $validator = Validator::make($request->all(), [
            "name"                      => "bail|required|unique:procedure_types"
        ]);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $this->model = $this->model->create([
                'name'          =>  $request->name,
            ]);

            $message = "Procedure type has been successfully created!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name." has been added to procedure type.";
            AuditTrailService::createAuditTrail('Added Procedure Type', $description, null ,$new_data,$this->model->id,'App\Models\ProcedureType');
        }

        return response($message, $code);
    }

    public function update(Request $request, $id)
    {
        $old_data = AuditTrailService::getDataForAuditTrail($this->model,$id);

        if($request->for_archive)
        {
            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $new_data->is_archived = $new_data->is_archived ? 0 : 1;
            $response = $new_data->is_archived ? 'Archive' : 'Restore';
            $description = $request->name." procedure type has been ".strtolower($response).".";
            AuditTrailService::createAuditTrail($response.' Procedure Type', $description, $old_data ,$new_data,$id,'App\Models\ProcedureType');
            return $this->archiveData($this->model, "procedure_type", $id);
        }

        $validator = Validator::make($request->all(), [

            "name"                  => "bail|required|unique:procedure_types,name,".$id,
        ]);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $update_tagged = $this->validateArchiveTagged($this->model, "procedure_type_id", $id);

            if($update_tagged)
            {
                return response($update_tagged["message"], $update_tagged["code"]);
            }

            $this->model = $this->model->find($id);
            $this->model = $this->model->fill([
            'name' => $request->name]);
            $this->model->save();

            $message = "Procedure type has been successfully updated!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." procedure type has been updated.";
            AuditTrailService::createAuditTrail('Update Procedure Type', $description, $old_data ,$new_data,$id,'App\Models\ProcedureType');
        }
        
        return response($message, $code);
    }
}
