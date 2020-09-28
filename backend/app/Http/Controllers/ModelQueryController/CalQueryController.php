<?php

namespace App\Http\Controllers\ModelQueryController;
use App\Models\Cal;
use Illuminate\Http\Request;

class CalQueryController extends FilterQueryController
{
    public function show(Request $request,$id){
        $cal = Cal::with(['cal_member.member', 'cal_status']);
        return $cal->find($id);
    }
}