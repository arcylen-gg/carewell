<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $with = ['user_position'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'user_position_id',
        'middle_name',
        'last_name',
        'email',
        'password',
        'crypt',
        'is_archived',
        'is_default',
    ];

    public function requestRules()
    {
        return $this->rules;
    }

    private $rules = [
        "first_name"    => "required",
        "last_name"     => "required",
        "email"         => "required|email|unique:users",
        "password"      => "required|confirmed|min:6",
    ];

    private $tags = [];

    private $self_tags = ['user_position'];

    public function requestTags()
    {
        return $this->tags;
    }

    public function requestSelfTags()
    {
        return $this->self_tags;
    }

    protected $appends = ['decrypt_password','full_name'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function user_position()
    {
        return $this->belongsTo(Models\UserPosition::class);
    }

    public function getDecryptPasswordAttribute()
    {
        return Crypt::decrypt($this->crypt);
    }

    public function getFullNameAttribute()
    {
        return ucwords(strtolower("{$this->first_name} {$this->middle_name} {$this->last_name}"));
    }


}
