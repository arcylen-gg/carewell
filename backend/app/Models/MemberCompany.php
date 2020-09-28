<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberCompany extends Model
{
    // protected $with = ['company', 'coverage_plan.coverage_plan_benefits.benefit_type','coverage_plan.coverage_plan_benefits.coverage_plan_procedures.procedures' ,'payment_mode', 'company_deployment'];

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function coverage_plan()
    {
        return $this->belongsTo(CoveragePlan::class, "coverage_plan_id");
    }
    public function payment_mode()
    {
        return $this->belongsTo(PaymentMode::class, "payment_mode_id");
    }
    public function company_deployment()
    {
        return $this->belongsTo(CompanyDeployment::class, "company_deployment_id");
    }

    public function member(){
        return $this->belongsTo(Member::class);
    }
}
