<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderPayee extends Model
{
    protected $fillable = ['provider_id','name'];

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
}
