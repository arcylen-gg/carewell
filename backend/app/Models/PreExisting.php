<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreExisting extends Model
{
    protected $guarded=[];

    private $rules = [
        'name' => 'sometimes|required',
    ];

    private $tags = [
        'coverage_plans'
    ];
    
    public function requestRules()
    {
        return $this->rules;
    }

    public function requestTags()
    {
        return $this->tags;
    }

    public function coverage_plans()
    {
        return $this->hasMany(CoveragePlan::class);
    }
}
