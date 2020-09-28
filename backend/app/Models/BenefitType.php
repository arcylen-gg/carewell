<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BenefitType extends Model
{
    protected $appends = [
        'item_count'
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

    public function getItemCountAttribute()
    {
        return 0;
    }
}
