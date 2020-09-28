<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\Specialization;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecializationController extends BaseController
{
    protected $model;

    public function __construct(Specialization $model)
    {
    	$this->model = $model;
    }

    public function index(Request $request)
    {
        $this->filterTable($request->all());
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

    // 
    public function store(Request $request)
    { 
    	//die(var_dump($request->all()));
        $validator = Validator::make($request->all(), [
            "name"                      => "bail|required|unique:specializations"
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

            $message = "Specialization has been successfully created!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name." has been added to specialization.";
            AuditTrailService::createAuditTrail('Added Specialization', $description, null ,$new_data,$this->model->id,'App\Models\Specialization');
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
            $description = $request->name." specialization has been ". strtolower($response).".";
            AuditTrailService::createAuditTrail($response.' Specialization', $description, $old_data ,$new_data,$id,'App\Models\Specialization');
            return $this->archiveData($this->model, "specialization", $id);
        }

        $validator = Validator::make($request->all(), [

            "name"                  => "bail|required|unique:specializations,name,".$id,
        ]);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $this->model = $this->model->find($id);
            $this->model = $this->model->fill([
            'name' => $request->name]);
            $this->model->save();

            $message = "Specialization has been successfully updated!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." specialization has been updated.";
            AuditTrailService::createAuditTrail('Update Specialization', $description, $old_data ,$new_data,$id,'App\Models\Specialization');
        }
        
        return response($message, $code);
    }
}
