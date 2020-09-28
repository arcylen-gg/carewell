<?php

namespace App\Models;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public $timestamps = false;

    protected $appends = ['sub_page_count', 'children', 'code', 'accesses'];

    public function getAccessesAttribute()
    {
        $user = User::where('id', Auth::id())->first();
        
        $accesses = UserPositionAccess::where('page_id', $this->id)->where('user_position_id', $user->user_position_id)->get();
        
        $data = [];
        
        foreach ($accesses as $key => $access) 
        {
            $types = unserialize($access->types);

            if(count($types))
            {
                foreach ($types as $k => $v) 
                {
                    $data[$v] = 1;
                }
            }
        }

        return $data;
    }
    
    public function getSubPageCountAttribute()
    {
        $count = $this->where('parent_id', $this->id)->count();
        return "{$count}";
    }

    public function getChildrenAttribute()
    {
        return $this->where("parent_id", $this->id)->get();
    }

    public function getCodeAttribute()
    {
        return str_replace(" ", "-", strtolower(str_replace("'", "", $this->name)));
    }

    public function user_position_access()
    {
        return $this->hasOne(UserPositionAccess::class);
    }
}