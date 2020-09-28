<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelActions\Member\MemberActionID;
use App\Models\ModelActions\Member\MemberConfinement;

class Member extends Model
{
    // protected $with = ['cal_member.cal'];
    // protected $appends = ['age', 'fullname', 'current_balance','per_year_confinement','per_month_confinement'];
    protected $appends = ['age', 'fullname','company','current_balance'];
    private $_memberData = array();

    protected $fillable = [
        'universal_id' , 'carewell_id', 'first_name' , 'middle_name','last_name' , 'mothers_maiden_name','birth_date' , 'gender','marital_status' , 'contact_number','email' , 'permanent_address','present_address' , 'company_id','company_deployment_id' , 'payment_mode_id','coverage_plan_id' , 'sss_number','tin' , 'philhealth_number','pagibig_number','company_ref_id' , 'is_archived',
    ];	

    public function dependents()
    {
        return $this->hasMany(MemberDependent::class, "member_id");
    }

    public function member_company()
    {
        return $this->hasMany(MemberCompany::class);
    }

    public function getCompanyAttribute()
    {
        return $this->hasMany(MemberCompany::class)->with(['company','company_deployment','coverage_plan'])->latest()->first();
        // return $this->member_company->last();
    }

    public function availment()
    {
        return $this->hasMany(Availment::class);
    }

    private $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        // 'birth_date' => 'required',
        // 'company_id' => 'required',
        // 'company_deployment_id ' => 'required',
        // 'payment_mode_id ' => 'required',
        // 'coverage_plan_id ' => 'required',
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

    public function getAgeAttribute()
    {
        $dob = new \DateTime($this->birth_date);
        $now = new \DateTime();
        $difference = $now->diff($dob);
        $age = $difference->y;
        return $age;
    }

    public function getFullNameAttribute()
    {
        return ucwords("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function setId(array $memberData)
    {
        $this->_memberData = $memberData;
    }

    public function modelAction($type)
    {
        $model_action = new MemberActionID($this);
        return $model_action->{$type}($this->_memberData);
    }

    public function getCurrentBalanceAttribute()
    {
        // return 123;
        $availment = $this->hasMany(Availment::class)->get();
    //    return count($this->availment);
    // return 0;
        if($this->company)
        {
            if(count($availment) == 0) return $this->company->coverage_plan->maximum_benefit_limit;
    
            return $this->company->coverage_plan->maximum_benefit_limit - collect($availment)->filter(function ($value,$key){
                return $value->status === "closed";
            })->sum('grand_total');

        }
    }

    public function getPerYearConfinementAttribute()
    {
        $model_action = new MemberConfinement($this);
        return $model_action->memberNumberOfConfinement(date('Y'));
    }

    public function getPerMonthConfinementAttribute()
    {
        $model_action = new MemberConfinement($this);
        return $model_action->memberNumberOfConfinement(date('Y-m'));
    }

    public function cal_member()
    {
        return $this->hasMany(CalMember::class);
    }

    public function testing(){
        return $this->hasOne(MemberCompany::class)->latest();
    }
    
}
