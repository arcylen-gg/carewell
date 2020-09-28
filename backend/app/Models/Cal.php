<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cal extends Model
{
    protected $fillable = [
         'cal_number','company_id','deployment_id','payment_mode_id','payment_start_date','payment_end_date','status','reference_number','payment_term_id','payment_cal_date','payment_due_date',
    ];
    protected $appends = ['files','aging','cal_member_count'];

    private $rules = [
        'company_id' => 'sometimes|required',
    ];


    private $tags = [];
    
    public function requestRules()
    {
        return $this->rules;
    }

    public function getCalMemberCountAttribute(){
        return $this->hasMany(CalMember::class, 'cal_id')->count();
    }

    public function requestTags()
    {
        return $this->tags;
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function company_deployment()
    {
        return $this->belongsTo(CompanyDeployment::class, 'deployment_id');
    }
    public function payment_mode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }
    public function cal_member()
    {
        return $this->hasMany(CalMember::class, 'cal_id');
    }
    public function cal_status()
    {
        return $this->hasOne(CalStatus::class, 'cal_id');
    }
    public function getFilesAttribute()
    {
        $files = Document::where("sourceable_type", "App\Models\CalStatus")->where("sourceable_id", $this->id)->get();
        return $files;
    }
    public function payment_term()
    {
        return $this->belongsTo(PaymentTerm::class, 'payment_term_id');
    }

    public function getAgingAttribute()
    {
        $due_date = new \DateTime($this->payment_due_date);
        $date_now = new \DateTime(date("y-m-d"));
        return "{$due_date->diff($date_now)->format("%d")} day(s) before due date";
    }
}
