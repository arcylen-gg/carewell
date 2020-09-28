<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelActions\Availment\AvailmentActionID;

class Availment extends Model
{
    protected $guarded = [];
    // protected $with = ['benefit_type','diagnosis','provider'];

    private $_availmentData = array();

    private $rules = [
        "member_id"         => "required",
        "carewell_id"       => "required",
        "provider_id"       => "required",
        "benefit_type_id"   => "required", 
        "date_avail"        => "required",
        "chief_complaint"   => "required", 
        "initial_diagnosis" => "required",
        "final_diagnosis"   => "required",
        "diagnosis_id"      => "required",
        "provider_payee_id" => "required",
        "doctor_id"         => "required"
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

    public function availment_doctor()
    {
        return $this->hasMany(AvailmentDoctor::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function provider_payee()
    {
        return $this->belongsTo(ProviderPayee::class);
    }

    public function coverage_plan()
    {
        return $this->belongsTo(CoveragePlan::class);
    }

    public function setId(array $availmentData)
    {
        $this->_availmentData = $availmentData;
    }

    public function modelAction($type)
    {
        $model_action = new AvailmentActionID($this);
        return $model_action->{$type}($this->_availmentData);
    }
    
    public function availment_procedure()
    {
        return $this->hasMany(AvailmentProcedure::class);
    }
    
    public function benefit_type()
    {
        return $this->belongsTo(BenefitType::class);
    }

    public function diagnosis()
    {
        return $this->belongsTo(DiagnosisList::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
