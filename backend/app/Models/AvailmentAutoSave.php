<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailmentAutoSave extends Model
{
    protected $guarded = [];

    protected $appends = ['deserialized_data'];

    public function getDeserializedDataAttribute(){
        return json_decode($this->data,true);
    }
}
