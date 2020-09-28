<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMode;

class PaymentModeController extends BaseController
{
   	public function __construct(PaymentMode $model)
    {
    	$this->model = $model;
        //die(var_dump($this->model));
    }

    public function index(Request $request)
    {
    	$test = PaymentMode::all();
    	return $test;
    }
    
    public function filterTable()
    {
        $related_fields             = [];
        $inexact_fields             = ["name"];
        $search_inexact_fields      = [["name","name"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }
    public function loadWithRelatedModel(){
        $this->model = $this->model->with(['']); //lazy loader
    }
}
