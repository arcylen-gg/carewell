<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyDeployment;
use App\Models\Member;

class CompanyDeploymentController extends BaseController
{
    private $model;

    public function __construct(CompanyDeployment $model)
    {
        $this->model = $model;
    }
    
    public function loadWithRelatedModel(){
        // $this->model = $this->model->with([
        //     'company_contact_person',
        //     'company_deployment',
        //     'company_coverage_plan.coverage_plan',
        //     'coverage_plan',
        // ]); //lazy loader
        $this->model = $this->model->with([
            // 'company_contact_person',
        ]); //lazy loader
    }


    public function index(Request $request)
    {
        $this->loadWithRelatedModel();
        $this->filterTable($request->all());
        return $this->getResultList($this->model->orderBy('name'));
    }
    public function filterTable()
    { 
        $related_fields             = [""];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name","name","code","address","email","contact_number"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }
    public function update(Request $request,$id)
    {
        $hasTagged = new Member();
        $hasTagged = $hasTagged->where('company_deployment_id',$id)->get()->toArray();
        if($hasTagged){
            $message = 'You cannot remove this because it is tag to other data.';
            $code = 300;
        }else{
            $message = 'Okay';
            $code = 200;
        }
        return response($message, $code);
    }
}
