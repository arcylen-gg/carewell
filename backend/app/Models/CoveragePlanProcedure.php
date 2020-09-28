<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoveragePlanProcedure extends Model
{
    protected $fillable = [
        'procedure_type_id', 'procedure_id', 'coverage_plan_benefit_id', 'amount',
    ];

    private $rules = [
        'name' => 'sometimes|required',
    ];

    private $tags = [];
    
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
        return $this->belongsTo(Procedure::class, "procedure_id");
    }
}
