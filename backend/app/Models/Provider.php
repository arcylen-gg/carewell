<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    // protected $with = ['provider_payee','doctor_providers.doctor'];
    protected $guarded = [];

    private $rules = [
        'name' => 'required|unique:providers',
        'rate_rvs' => 'required',
        // 'email' => 'required|email',
    ];

    private $tags = [
        "doctors_providers"
    ];
    
    public function requestRules()
    {
        return $this->rules;
    }

    public function requestTags()
    {
        return $this->tags;
    }
    
    public function provider_payee()
    {
        return $this->hasMany(ProviderPayee::class);
    }

    public function doctors_providers()
    {
        return $this->hasMany(Doctor::class,'doctors_providers');
    }

    public function doctor_providers()
    {
        return $this->hasMany(DoctorsProvider::class);
    }
}