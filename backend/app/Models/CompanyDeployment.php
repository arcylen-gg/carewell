<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDeployment extends Model
{
    protected $fillable =[
        'company_id',        
        'name',        
    ];
    protected $guarded = [];
    
    private $rules = [
        'name' => 'sometimes|required',
    ];

    private $tags = [
        'member_companies',
    ];
    
    public function requestRules()
    {
        return $this->rules;
    }

    public function requestTags()
    {
        return $this->tags;
    }
}
