<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPositionAccess extends Model
{
    protected $fillable = [
        'page_id', 'user_position_id', 'code', 'types'
    ];

    protected $appends = [
        'accesses'
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

    public function getAccessesAttribute()
    {
        return unserialize($this->types);
    }

}
