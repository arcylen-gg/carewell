<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCoveragePlan extends Model
{
    public function coverage_plan()
    {
        return $this->belongsTo(CoveragePlan::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected $guarded = [];
    
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

}
