<?php

namespace App\Http\Controllers\Uploads; 

use App\Http\Controllers\Controller;
use App\Exports\MemberAvailmentsHistoryExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Availment;
use App\Models\Member;

use Excel;

class MemberAvailmentHistoryController extends Controller
{
    public function export(Request $request)
    {
        $member = Member::find($request->member_id);

        //dynamic wheres
        $availment = Availment::with(['company'])->when($request->all(), function ($query,$data){
            foreach($data as $key => $value)
            {
                //search text "_id" on $key 
                if(strpos($key,'_id'))
                {
                    if(is_numeric($value))
                    {
                        $query->where($key,$value);
                    }
                }
                else
                {
                    $query->where($key,'like','%'.$value.'%');
                }
                
            }
        });

        $data = $availment->get();
        return Excel::download(new MemberAvailmentsHistoryExport($data), $member->full_name.'_availment_history.csv');  
    }
}