<?php

namespace App\Http\Controllers\ModelQueryController;
use App\Models\CoveragePlan;
use Illuminate\Http\Request;

class FilterQueryController
{
    protected $per_page_limit = 5;
    protected $except_fields = ["limit","showAll"];

    public function filterWhereModel($model,$request)
    {
        foreach ($request as $key => $value) 
        {
            if(!in_array($key,$this->except_fields))
            {
                $model = $model->where($key,$value);
            }
        }
        
        return $model;
    }

    public function filterBaseTableModel($model,$request)
    {
        $model = $this->filterWhereModel($model,$request);

        if(isset($request['limit'])){
            $this->per_page_limit = (int)$request['limit'];
            return $model->paginate($this->per_page_limit);
        }

        return $model->get();
    }

    
}