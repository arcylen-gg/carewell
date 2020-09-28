<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    protected $with = ['procedure_type'];

    protected $fillable = [
         'name' , 'is_archived','procedure_type_id','is_default',
    ];

    private $rules = [
        'name' => 'sometimes|required',
    ];

    private $tags = [
        'coverage_plan_procedures',
    ];
    
    public function requestRules()
    {
        return $this->rules;
    }

    public function requestTags()
    {
        return $this->tags;
    }

    public function procedure_type()
    {
        return $this->belongsTo(ProcedureType::class, "procedure_type_id");
    }

    public function coverage_plan_procedures()
    {
        return $this->hasMany(CoveragePlanProcedure::class, "procedure_id");
    }
} 
