<?php

namespace App\Http\Controllers\ModelQueryController;
use App\Models\Company;
use Illuminate\Http\Request;

use DB;
class CompanyQueryController
{
    public function show(Request $request, $id){
        $data = Company::with([
            'company_contact_person',
            'company_deployment',
            'company_coverage_plan.coverage_plan',
            'coverage_plan'
        ]);
        return $data->find($id);
    }

    public function getAllCompanies(Request $request)
    {
        $is_archived = $request->is_archived;
        $company = DB::table('companies')->where('is_archived',$is_archived)->get();
        return $company;
    }
}