<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;

use Illuminate\Http\Request;

class AuditTrailController extends BaseController
{
    private $model;

    public function __construct(AuditTrail $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){
        $this->model = $this->model->with([
            'user'
        ]); //lazy loader
    }

    public function index(Request $request)
    {
        $this->loadWithRelatedModel();
        $this->filterTable($request->all());
        return $this->getResultList($this->model->latest());
    }

    public function filterTable()
    { 
        $related_fields             = [""];
        $inexact_fields             = ["name","date_from","date_to"]; 
        $search_inexact_fields      = [["name","remarks"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }
}
