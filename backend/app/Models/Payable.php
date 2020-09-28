<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    protected $guarded = [];

    private $rules = [
        "provider_id"   => 'required',
        "payment_term_id"   => 'required',
        "soa_number"    => 'required',
        "received_date" => 'required',
        "due_date"  => 'required',
    ];

    public function requestRules()
    {
        return $this->rules;
    }

    public function payable_availment()
    {
        return $this->hasMany(PayableAvailment::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function payable_provider()
    {
        return $this->hasMany(PayableProvider::class);
    }

    public function payable_doctor()
    {
        return $this->hasMany(PayableDoctor::class);
    }

    public function payment_term()
    {
        return $this->belongsTo(PaymentTerm::class);
    }
}
