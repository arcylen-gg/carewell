<?php

namespace App\Http\Controllers\ModelQueryController;
use App\Models\Payable;
use App\Models\Availment;
use App\Models\Provider;
use Illuminate\Http\Request;

use DB;

class PayableQueryController extends FilterQueryController
{
    public function getProviderAvailment(Request $request, $id)
    {
        $request['provider_id'] = $id;
        $availment = Availment::with(['benefit_type','diagnosis','provider','member','availment_doctor.doctor','availment_procedure.procedure']);
        return $this->filterBaseTableModel($availment, $request->all());
    }

    public function getProvider(Request $request)
    {
        $provider = DB::table('providers')
                        ->selectRaw('id, name')
                        ->where('is_archived', $request->is_archived)
                        ->orderBy('providers.name','ASC')
                        ->get();
        return $provider;
    }
    public function getProviderCreate(Request $request)
    {
        $provider = DB::table('providers')
                        ->selectRaw('providers.id as id, name, count(availments.id) as availment_ctr')
                        ->leftjoin('availments','provider_id','providers.id')
                        ->where('is_archived', $request->is_archived)
                        ->orderBy('providers.name','ASC')
                        ->groupBy('providers.id')
                        ->get();
        $ret = [];
        $ctr = 0;
        foreach ($provider as $key => $value) 
        {
            if($value->availment_ctr > 0)
            {
                $ret[$ctr] = $value;
                $ctr++;
            }
        }

        return $ret;
    }
}