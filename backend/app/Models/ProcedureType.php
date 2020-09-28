<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcedureType extends Model
{
  	protected $fillable = [
         'name' , 'is_archived','is_default',
    ];

    private $rules = [
        'name' => 'sometimes|required',
    ];

    private $tags = [
        'coverage_plan_procedures', 'procedures'
    ];
    
    public function requestRules()
    {
        return $this->rules;
    }

    public function requestTags()
    {
        return $this->tags;
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function coverage_plan_procedures()
    {
        return $this->hasMany(CoveragePlanProcedure::class);
    }

}
