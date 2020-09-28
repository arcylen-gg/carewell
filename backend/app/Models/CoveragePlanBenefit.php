<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Report\Xml\Coverage;

class CoveragePlanBenefit extends Model
{
    protected $fillable = [
        'coverage_plan_id', 'benefit_type_id', 'charges', 'covered_amount', 'per_confinement', 'per_confinement_amount', 'limit_per_year', 'limit_per_month'
    ];
    
    private $rules = [
        'name' => 'sometimes|required',
    ];

    protected $appends = [
        'item_count', 'per_month'
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

    public function coverage_plan_procedures()
    {
        return $this->hasMany(CoveragePlanProcedure::class);
    }

    public function getItemCountAttribute()
    {
        return $this->hasMany(CoveragePlanProcedure::class)->count();
        // return count($this->coverage_plan_procedures);
    }

    public function getPerMonthAttribute()
    {
        return $this->limit_per_month ?: 0;
    }

    public function benefit_type()
    {
        return $this->belongsTo(BenefitType::class);
    }
}
