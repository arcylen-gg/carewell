<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    protected $fillable = [
        'name','is_default',
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
}
