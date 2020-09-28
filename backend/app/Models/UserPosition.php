<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPosition extends Model
{
    protected $fillable = [
        'name', 'is_archived'
    ];

    protected $appends = [
        'user_count'
    ];

    private $rules = [
        'name' => 'required|unique:user_positions',
    ];

    private $tags = [
        'users'
    ];
    
    public function requestRules()
    {
        return $this->rules;
    }

    public function requestTags()
    {
        return $this->tags;
    }

    public function user_position_accesses()
    {
        return $this->hasMany(UserPositionAccess::class);
    }

    public function users()
    {
        return $this->hasMany(\App\User::class);
    }

    public function getUserCountAttribute()
    {
        return \App\User::where('user_position_id', $this->id)->where('is_archived', 0)->count();
    }

}
