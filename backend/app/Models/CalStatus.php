<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalStatus extends Model
{
    protected $fillable = [
         'cal_id','pending_date','pending_remarks','collection_date','check_number','check_date','or_number','bank_id',
    ];
}
