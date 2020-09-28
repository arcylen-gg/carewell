<?php

namespace App\Http\Controllers\ModelQueryController;
use App\Models\Member;
use App\Models\Company;
use Illuminate\Http\Request;

class MemberQueryController extends FilterQueryController
{
    public function show(Request $request, $id){
        $member = Member::with(['dependents']);
        // $member = Member::with(['dependents','member_company.company_deployment','member_company.coverage_plan']);
        return $member->find($id);
    }

    public function getTransactionHistory($id){
        $member = Member::where('id',$id)->with([
            'availment.availment_procedure',
            'availment.benefit_type',
            'availment.diagnosis',
            'availment.provider',
            'cal_member.cal']);
        return $member->find($id);
    }

    public function getCompanies(Request $request){
        $company = Company::with(['company_deployment','company_coverage_plan.coverage_plan']);
        return $this->filterBaseTableModel($company, $request->all());
    }
}