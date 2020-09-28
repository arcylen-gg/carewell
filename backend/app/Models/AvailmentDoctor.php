<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailmentDoctor extends Model
{
    public function doctor()
    {
        return $this->belongsTo(Doctor::class,'doctor_id');
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class,'procedures_id');
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class,'specialization_id');
    }
}
