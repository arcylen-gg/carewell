<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\Bank;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankController extends BaseController
{
    protected $model;

    public function __construct(Bank $model)
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
            "name"                      => "bail|required|unique:banks"
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

            $message = "Bank has been successfully created!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name." has been added to bank.";
            AuditTrailService::createAuditTrail('Added Bank', $description, null ,$new_data,$this->model->id,'App\Models\Bank');
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
            $description = $request->name." bank has been ".strtolower($response).".";
            AuditTrailService::createAuditTrail($response.' Bank', $description, $old_data ,$new_data,$id,'App\Models\Bank');
            return $this->archiveData($this->model, "bank", $id);
        }

        $validator = Validator::make($request->all(), [

            "name"                  => "bail|required|unique:banks,name,".$id,
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

            $message = "Bank has been successfully updated!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." bank has been updated.";
            AuditTrailService::createAuditTrail('Update Bank', $description, $old_data ,$new_data,$id,'App\Models\Bank');
        }
        
        return response($message, $code);
    }
}
