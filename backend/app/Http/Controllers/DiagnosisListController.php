<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\DiagnosisList;
use Illuminate\Http\Request;
use Validator;

class DiagnosisListController extends BaseController
{ 
	private $model;

    public function selectExceptThisId(Request $request)
    {
        if($request->id)
        {
            $ret = $this->getResultList($this->model->where('is_archived',$request->is_archived)
                        ->where('id','!=',$request->id)
                        ->latest());
        }
        else
        {
            $ret = $this->getResultList($this->model->where('is_archived',$request->is_archived)
                        ->whereNull('parent_id')->where('level',0)
                        ->latest());            
        }
        return $ret;
        // return $this->getResultList($this->model->where('company_id',$request->company_id)->whereNull('parent_id')->where('id','!=',$request->id)->latest());
    }
    public function __construct(DiagnosisList $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel()
    {
    }
    public function index(Request $request)
    {
        $this->filterTable($request->all());
        return $this->getResultList($this->model->orderBy('name'));
    }

    public function filterTable()
    { 
        $related_fields             = [""];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name","name","description"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }
    public function store(Request $request)
    {
       // / die(var_dump($request->all()));
        $validator = Validator::make($request->all(), [
            "name"          => "bail|required|unique:diagnosis_lists",
            "description"   => "bail|required",
        ]);
        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {

           $this->model = $this->model->create([
                'name'                 => $request->name,
                'description'          => $request->description,
                'parent_id'            => $request->is_sub_diagnosis ? $request->sub_diagnosis : null,
                'covered_first_year'   => $request->covered_first_year ? 1 : 0,
                'covered_after_year'   => $request->covered_after_year ? 1 : 0,
                'exclusion'            => $request->exclusion ? 1 : 0,
            ]);

            // /die(var_dump($request->sub_diagnosis));

            if($request->is_sub_diagnosis == true && isset($request->sub_diagnosis)){
                $getDiagnosis = $this->model::where('id',$request->sub_diagnosis)->first();
                // /die(var_dump($getDiagnosis->level + 1));

                $this->model = $this->model
                    ->find($this->model->id)
                    ->fill([
                        'level' => $getDiagnosis->level + 1,
                    ]); 
                    $this->model->save();
                }

            $message = "Diagnosis list has been successfully created!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name."  has been added to diagnosis list.";
            AuditTrailService::createAuditTrail('Added Diagnosis list', $description, null ,$new_data,$this->model->id,'App\Models\DiagnosisList');
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
            $description = $request->name." diagnosis list has been ". strtolower($response). ".";
            AuditTrailService::createAuditTrail($response.' Diagnosis list', $description, $old_data ,$new_data,$id,'App\Models\DiagnosisList');
            return $this->archiveData($this->model, "diagnosis_list", $id);
        }

        $validator = Validator::make($request->all(), [
            "name"         => "bail|required|unique:diagnosis_lists,name,".$id,
            "description"   => "bail|required|",
        ]);

        $update_parent = $this->validateIfParent($this->model, $id);
        
        if($update_parent)
        {
            return response($update_parent["message"], $update_parent["code"]);
        }

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $parent = DiagnosisList::find($request->parent_id);
            $this->model = $this->model->find($id)->fill([
               'name'                  => $request->name,
                'description'          => $request->description,
                'parent_id'            => $request->is_sub_diagnosis ? $request->parent_id : null,
                'covered_first_year'   => $request->covered_first_year ? 1 : 0,
                'covered_after_year'   => $request->covered_after_year ? 1 : 0,
                'exclusion'            => $request->exclusion ? 1 : 0,
            ]);
             $this->model->save();

            if($request->is_sub_diagnosis == true)
            {
                if($request->list_parent[0] != 'is_null')
                {
                    if(!isset($request->parent_id)){
                        $this->model->fill(['level' => 0]);
                        $this->model->save();
                    }
                    else if($request->list_parent[0] != $request->list_parent[1])
                    {
                        $getDiagnosis = $this->model::where('id',$request->list_parent[1])->first();
                            $new = $this->model
                            ->find($this->model->id)
                            ->fill([
                                'level' => $getDiagnosis->level + 1,
                            ]);
                        $new->save(); 
                    }
                }
                else if ($request->list_parent[0] == 'is_null' && isset($request->list_parent[1])){
                        $getDiagnosis = $this->model::where('id',$request->list_parent[1])->first();
                            $new = $this->model
                            ->find($this->model->id)
                            ->fill(
                                ['level' => $getDiagnosis->level + 1,]
                            );
                        $new->save(); 
                    }
            }else
            {
                $this->model->fill(['level' => 0]);
                $this->model->save();
            }
            $message = "Diagnosis list has been successfully updated!";
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." diagnosis list has been updated.";
            AuditTrailService::createAuditTrail('Update Diagnosis List', $description, $old_data ,$new_data,$id,'App\Models\DiagnosisList');
        }
        return response($message, $code);
    
    }

}
