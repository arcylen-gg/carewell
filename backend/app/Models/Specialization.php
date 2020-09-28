<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $fillable = [
         'name' , 'is_archived'
    ];

    private $rules = [
        'name' => 'sometimes|required',
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

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function source()
    {
        return $this->morphOne('App\Models\AuditTrail', 'sourceable');
    }
}
