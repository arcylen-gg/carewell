<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorsProvider extends Model
{
    protected $guarded=[];
    
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

    public function provider(){
        return $this->belongsTo(Provider::class);
    }

    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
}
