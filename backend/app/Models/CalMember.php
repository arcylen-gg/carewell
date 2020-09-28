<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalMember extends Model
{
    protected $fillable = [
         'cal_id','member_id','deployment_id','carewell_id','monthly_premium','period_count','date_paid','paid_amount','status', 
    ];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function coverage_plan()
    {
        return $this->belongsTo(CoveragePlan::class);
    }
    public function deployment()
    {
        return $this->belongsTo(CompanyDeployment::class);
    }
    public function cal(){
        return $this->belongsTo(Cal::class);
    }
}
