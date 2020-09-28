<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyContactPerson extends Model
{
    protected $fillable =[
        'company_id',		 
        'first_name',		 
        'middle_name',		 
        'last_name',		 
        'position',		 
        'contact_number',
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
