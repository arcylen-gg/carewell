<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayableAvailment extends Model
{
    protected $guarded = [];

    public function availment(){
        return $this->belongsTo(Availment::class);
    }
}
