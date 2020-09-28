<?php

namespace App\Http\Controllers;

use App\Models\CoveragePlan;
use App\Models\CoveragePlanBenefit;
use App\Models\CoveragePlanProcedure;
use App\Globals\AuditTrailService;
use Illuminate\Http\Request;

class CoveragePlanController extends BaseController
{
    private $model, $submodel, $procedure_model;

    public function __construct(CoveragePlan $model, CoveragePlanBenefit $sub_model, CoveragePlanProcedure $procedure_model)
    {
        $this->model                = $model;
        $this->sub_model            = $sub_model;
        $this->procedure_model      = $procedure_model;
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

    public function show(Request $request, $id)
    {
        $this->model = $this->model->with(['coverage_plan_benefits', 'coverage_plan_benefits.coverage_plan_procedures']);
        return $this->model->find($id);
    }

    public function index(Request $request)
    {
        // $this->model = $this->model->with(['coverage_plan_benefits', 'coverage_plan_benefits.coverage_plan_procedures']);
        $this->filterTable();
        return $this->getResultList($this->model->orderBy('name'));
    }

    public function store(Request $request)
    {
        // return $request->all();
        $no_access = $this->validateRouteAccess("coverage-plan", "create");

        if($no_access)
        {
            return response($no_access["message"] ?: 0, $no_access["code"]);
        }

        $validator = $this->validateRequestInput(request()->all(), $this->model->requestRules());

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;

            return response($message, $code);
        }
        $this->model->pre_existing_id           = $request->pre_existing_id;
        $this->model->name                      = str_replace(",", " ", $request->name);
        $this->model->age_bracket               = serialize($request->age_bracket) ?: 0;
        $this->model->processing_fee            = $request->processing_fee ?: 0;
        $this->model->hospital_income_benefit   = $request->hospital_income_benefit ?: 0;
        $this->model->monthly_premium           = $request->monthly_premium ?: 0;
        $this->model->handling_fee              = $request->handling_fee ?: 0;
        $this->model->card_fee                  = $request->card_fee ?: 0;
        $this->model->maximum_benefit_limit     = $request->maximum_benefit_limit ?: 0;
        $this->model->max_limit_per             = $request->max_limit_per ?: 0;
        $this->model->member_type               = $request->member_type;
        $this->model->annual_benefit_limit      = $request->annual_benefit_limit ?: 0;
        $this->model->save();

        if($request->coverage_plan_benefits)
        {
            foreach ($request->coverage_plan_benefits as $key => $value) {
                if($value){
                    $value_benefits = $value["benefits"];
                    $this->sub_model = $this->sub_model->create([
                        'coverage_plan_id' => $this->model->id,
                        'benefit_type_id' => $value_benefits["benefit_type_id"],
                        'charges' => isset($value_benefits["charges"]) ? $value_benefits["charges"] : 0,
                        'covered_amount' => isset($value_benefits["covered_amount"]) ? $value_benefits["covered_amount"] : 0,
                        'per_confinement' => isset($value_benefits["per_confinement"]) ? $value_benefits["per_confinement"] : 0,
                        'per_confinement_amount' => isset($value_benefits["per_confinement_amount"]) ? $value_benefits["per_confinement_amount"] : 0,
                        'limit_per_year' => isset($value_benefits["limit_per_year"]) ? $value_benefits["limit_per_year"] : 0,
                        'limit_per_month' => isset($value_benefits["limit_per_month"]) ? $value_benefits["limit_per_month"] : 0
                    ]);

                    if(isset($value["procedures"]) && count($value["procedures"]))
                    {  
                        foreach ($value["procedures"] as $k => $v) 
                        {
                            if($v && count($v))
                            {
                                foreach ($v as $k2 => $v2) {
                                    $this->procedure_model = $this->procedure_model->create([
                                        'procedure_type_id' => $v2["procedure_type_id"],
                                        'procedure_id' => $v2["id"],
                                        'coverage_plan_benefit_id' => $this->sub_model->id,
                                        'amount' => isset($v2["amount"]) ? $v2["amount"] : 0,
                                    ]);
                                }
                            }
                        }

                    }
                }
            }
        }

        $message = "Coverage plan has been successfully added!";
        $code = 200;

        $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
        $description = $request->name." has been added in Coverage Plan.";
        AuditTrailService::createAuditTrail('Added Coverage Plan', $description, null ,$new_data,$this->model->id,'App\Models\CoveragePlan');

        return response($message, $code); 
    }

    public function update(Request $request, $id)
    {
        // return $request->all();
        $old_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
        if($request->for_archive)
        {
            $no_access = $this->validateRouteAccess("coverage-plan", "archive");

            if($no_access)
            {
                return $this->response($no_access["message"], $no_access["message"], $no_access["code"]);
            }

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $new_data->is_archived = $new_data->is_archived ? 0 : 1;
            $response = $new_data->is_archived ? 'Archive' : 'Restore';
            $description = $request->name." (coverage plan) has been ".strtolower($response)."."; 
            AuditTrailService::createAuditTrail($response.' Coverage Plan', $description, $old_data ,$new_data,$id,'App\Models\CoveragePlan');

            return $this->archiveData($this->model, "coverage_plan", $id);
        }

        $no_access = $this->validateRouteAccess("coverage-plan", "edit");

        if($no_access)
        {
            return response($no_access["message"] ?: 0, $no_access["code"]);
        }

        $rules = $this->model->requestRules();
        $rules["name"] = $rules["name"] . ",name," . $id;
        $validator = $this->validateRequestInput(request()->all(), $rules);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;

            return response($message, $code);
        }
        $update_tagged = $this->validateArchiveTagged($request->coverage_plan_benefits, "coverage_plan_id", $id);
        if($update_tagged)
            {
                return response($update_tagged["message"], $update_tagged["code"]);
            }

        $this->model                            = $this->model->find($id);
        $this->model->pre_existing_id           = $request->pre_existing_id;
        $this->model->name                      = $request->name;
        $this->model->age_bracket               = serialize($request->age_bracket) ?: 0;
        $this->model->processing_fee            = $request->processing_fee ?: 0;
        $this->model->hospital_income_benefit   = $request->hospital_income_benefit ?: 0;
        $this->model->monthly_premium           = $request->monthly_premium ?: 0;
        $this->model->handling_fee              = $request->handling_fee ?: 0;
        $this->model->card_fee                  = $request->card_fee ?: 0;
        $this->model->maximum_benefit_limit     = $request->maximum_benefit_limit ?: 0;
        $this->model->max_limit_per             = $request->max_limit_per ?: 0;
        $this->model->member_type               = $request->member_type;
        $this->model->annual_benefit_limit      = $request->annual_benefit_limit ?: 0;
        $this->model->save();
        

        if($request->coverage_plan_benefits)
        {
            //get benefit type that still in coverage plan
            $keep_benefit_ids = [];
            $keep_benefits = [];
            foreach($request->coverage_plan_benefits as $key => $value)
            {
                if($value['benefits']['item_count'] != 0)
                {
                    array_push($keep_benefit_ids, $value['benefits']['benefit_type_id']);
                    array_push($keep_benefits,$value);
                }
            }
            
            //remove benefit types that are on previous coverage plan and not on this updated coverage plan
            $delete_benefit_id = $this->sub_model->where("coverage_plan_id", $id)->whereNotIn("benefit_type_id", $keep_benefit_ids)->pluck('id')->all();
            $this->sub_model->destroy($delete_benefit_id);

            //update or create coverage plan benefit type
            foreach ($keep_benefits as $key => $value) {
                $value_benefits = $value['benefits'];
                $value_procedureData = $value['procedures'];

                $benefit_type = CoveragePlanBenefit::updateOrCreate(
                    [
                        'coverage_plan_id' => $id,
                        'benefit_type_id' => $value_benefits['benefit_type_id']
                    ],
                    [
                        'charges' => isset($value_benefits["charges"]) ? $value_benefits["charges"] : 0,
                        'covered_amount' => isset($value_benefits["covered_amount"]) ? $value_benefits["covered_amount"] : 0,
                        'per_confinement' => isset($value_benefits["per_confinement"]) ? $value_benefits["per_confinement"] : 0,
                        'per_confinement_amount' => isset($value_benefits["per_confinement_amount"]) ? $value_benefits["per_confinement_amount"] : 0,
                        'limit_per_year' => isset($value_benefits["limit_per_year"]) ? $value_benefits["limit_per_year"] : 0,
                        'limit_per_month' => isset($value_benefits["limit_per_month"]) ? $value_benefits["limit_per_month"] : 0
                    ]
                );
                
                //get all procedures per procedure types and put in one array
                $merge_procedure_types_procedures = [];
                foreach($value_procedureData as $key_procedure_type => $value_procedure_type){
                    if(is_array($value_procedure_type))
                    {
                        foreach($value_procedure_type as $key_procedure => $value_procedure){
                            array_push($merge_procedure_types_procedures, $value_procedure);
                        }
                    }
                }

                $collect_procedure_ids = collect($merge_procedure_types_procedures)->pluck('id')->all();

                //get all procedures not in $collect_procedure_ids then delete it
                $delete_procedure_ids = $this->procedure_model->where('coverage_plan_benefit_id', $benefit_type->id)
                                        ->whereNotIn('procedure_id',$collect_procedure_ids)
                                        ->pluck('id')->all();
                $this->procedure_model->destroy($delete_procedure_ids);

                foreach($merge_procedure_types_procedures as $key_procedure => $value_procedure){
                    $procedures = CoveragePlanProcedure::updateOrCreate(
                        [
                            'procedure_type_id' => $value_procedure['procedure_type_id'],
                            'procedure_id' => $value_procedure['id'],
                            'coverage_plan_benefit_id' => $benefit_type->id
                        ],
                        [
                            'amount' => isset($value_procedure['amount']) ? $value_procedure['amount'] : 0
                        ]
                    );
                }
            }
        }

        $message = "Coverage plan has been successfully updated!";
        $code = 200;

        $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
        $description = $request->name." (coverage plan) has been updated.";
        AuditTrailService::createAuditTrail('Update Coverage Plan', $description, $old_data ,$new_data,$id,'App\Models\CoveragePlan');

        return response($message, $code); 
    }
}
