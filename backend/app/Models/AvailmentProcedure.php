<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailmentProcedure extends Model
{
    public function procedure()
    {
        return $this->belongsTo(Procedure::class,'procedures_id');
    }
}
