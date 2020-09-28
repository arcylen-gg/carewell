<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\Procedure;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcedureController extends BaseController
{
    protected $model;

    public function __construct(Procedure $model)
    {
    	$this->model = $model;
    }

    public function index(Request $request)
    {
        //die(var_dump($request->all()));
    	$this->loadWithRelatedModel();
        $this->filterTable();
    	return $this->getResultList($this->model->orderBy('name'));
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
    public function loadWithRelatedModel(){
        $this->model = $this->model->with(['procedure_type']); //lazy loader
    }

    // 
    public function store(Request $request)
    { 
    	
        $check = $this->model->where('name',$request->name)->where('procedure_type_id',$request->procedure_type_id)->first();
        if($check != null)
        {
            $message = 'The name has already been taken.';
            $code = 300;
        }
        else
        {
            $this->model = $this->model->create([
                'name'              => $request->name,
                'procedure_type_id' => $request->procedure_type_id
            ]);

            $message = "Procedure description has been successfully added!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name." has been added to procedure.";
            AuditTrailService::createAuditTrail('Added Procedure', $description, null ,$new_data,$this->model->id,'App\Models\Procedure');
            
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
            $description = $request->name." procedure has been ". strtolower($response).".";
            AuditTrailService::createAuditTrail($response.' Procedure', $description, $old_data ,$new_data,$id,'App\Models\Procedure');
            return $this->archiveData($this->model, "procedure", $id);
        }
        
        $check = $this->model->where('name',$request->name)->where('procedure_type_id',$request->procedure_type_id)->where('id','!=', $id)->first();

        if($check != null)
        {
            $message = 'The name has already been taken.';
            $code = 300;
        }
        else
        {
            $update_tagged = $this->validateArchiveTagged($this->model, "procedure_id", $id);

            if($update_tagged)
            {
                return response($update_tagged["message"], $update_tagged["code"]);
            }

            $this->model = $this->model->find($id);
            $this->model = $this->model->fill([
            'name' => $request->name,
            'procedure_type_id' => $request->procedure_type_id
        	]);

            $this->model->save();

            $message = "Procedure description has been successfully updated!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." procedure has been updated.";
            AuditTrailService::createAuditTrail('Update Procedure', $description, $old_data ,$new_data,$id,'App\Models\Procedure');
        }
        
        return response($message, $code);
    }
}
