<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    // protected $with = ['doctorProvider.provider'];

    protected $appends = ['name'];
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'contact_number', 'email', 'is_archived'
    ];
    
    public function doctorProvider(){
        return $this->hasMany(DoctorsProvider::class);
    }

    private $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        // 'email' => 'required|unique:doctors'
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

    public function getNameAttribute()
    {
        return ucwords("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
