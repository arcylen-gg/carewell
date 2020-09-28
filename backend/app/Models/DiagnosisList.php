<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosisList extends Model
{
    protected $fillable = [
         'name','description','parent_id','level','covered_first_year','covered_after_year','exclusion','is_archived',
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
