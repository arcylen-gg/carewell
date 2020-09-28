<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $guarded = [];

    protected $appends = ['transaction'];

    public function sourceable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getTransactionAttribute()
    {
        return $this->find($this->id)->sourceable;
    }

}
