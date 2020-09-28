<?php

namespace App\Http\Controllers\ModelQueryController;
use App\Models\CoveragePlan;
use Illuminate\Http\Request;

class CoveragePlanQueryController extends FilterQueryController
{
    protected $model;
    public function __construct(CoveragePlan $model)
    {
        $this->model = $model;
    }
    
    public function getAll(Request $request)
    {
        // $this->model = $this->model->with(['pre_existing']);
        // $this->model = $this->model->setAppends(['qwe']);
        // return $this->model;
        return $this->filterBaseTableModel($this->model,$request->all());
    }
}