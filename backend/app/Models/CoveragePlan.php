<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoveragePlan extends Model
{
    protected $fillable = [
        'pre_existing_id', 'name', 'age_bracket', 'processing_fee', 'hospital_income_benefit', 'monthly_premium', 'handling_fee', 'card_fee', 'annual_benefit_limit', 'maximum_benefit_limit', 'max_limit_per', 'is_archived',
    ];

    protected $appends = [
        'age_bracket_from', 'age_bracket_to', 'is_used_in_company'
    ];

    private $rules = [
        'name'              => 'required|unique:coverage_plans',
        'monthly_premium'   => 'required',
        'pre_existing_id'   => 'required',
    ];

    private $tags = [
        'member_companies',
        'company_coverage_plans',
    ];
    
    public function requestRules()
    {
        return $this->rules;
    }

    public function requestTags()
    {
        return $this->tags;
    }

    public function getAgeBracketFromAttribute()
    {
        $age = unserialize($this->age_bracket);
        return $age[0];
    }

    public function getAgeBracketToAttribute()
    {
        $age = unserialize($this->age_bracket);
        return $age[1];
    }

    public function coverage_plan_benefits()
    {
        return $this->hasMany(CoveragePlanBenefit::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function company_coverage_plans()
    {
        return $this->hasOne(CompanyCoveragePlan::class);
    }

    public function getIsUsedInCompanyAttribute()
    {
        // $is_used = CompanyCoveragePlan::find($this->id);
        $is_used = CompanyCoveragePlan::where('coverage_plan_id',$this->id)->first();
        return $is_used ? true : false;
    }

    public function pre_existing()
    {
        return $this->belongsTo(PreExisting::class);
    }
}
