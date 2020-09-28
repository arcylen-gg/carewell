<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];

    protected $appends = [
        'files','count'
    ];

    private $rules = [
        "name"    => "required|unique:companies",
        "code" => "required",		 	 
        "email" => "required|email|unique:companies",	 
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

    public function company_contact_person()
    {
        return $this->hasMany(CompanyContactPerson::class);
    }

    public function company_deployment()
    {
        return $this->hasMany(CompanyDeployment::class);
    }

    public function company_coverage_plan()
    {
        return $this->hasMany(CompanyCoveragePlan::class);
    }

    public function coverage_plan()
    {
        return $this->belongsToMany(CoveragePlan::class, "company_coverage_plans");
    }
     public function member_company()
    {
        return $this->hasMany(MemberCompany::class);
    }

    public function getFilesAttribute()
    {
        $files = Document::where("sourceable_type", "App\Models\Company")->where("sourceable_id", $this->id)->get();

        return $files;
    }

    public function getCountAttribute()
    {
        return $this->where('is_archived',0)->count();
    }

    public function source()
    {
        return $this->morphMany('App\Models\AuditTrail', 'sourceable');
    }
}
