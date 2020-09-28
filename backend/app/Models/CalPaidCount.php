<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalPaidCount extends Model
{
    protected $fillable = [
        'cal_id','cal_member_id','start_date','end_date','amount',
   ];
}
