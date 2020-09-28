<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberDependent extends Model
{
    protected $fillable = [
        'full_name', 'birth_date', 'relationship'
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
