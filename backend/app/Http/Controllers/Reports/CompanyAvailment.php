<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Globals\ReportService;
use App\Exports\CompanyAvailmentExport;

use DB;
use Excel;
use Carbon\Carbon;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;

class CompanyAvailment implements GetReportDataInterface
{
    public function index()
    {
        $request = request()->all();

        return $this->getQueryResult($request);
    }

    public function exportToExcel()
    {
        // I use $this->index() because data get on this function are use in exporting;
        $report_info = $this->index();
        $data = [];
        $data['data'] = [];
        foreach ($report_info as $key => $value) {
            $company = [];
            $company['company'] = $value->name;
            $company['approval_id'] = ' ';
            $company['member_name'] = ' ';
            $company['coverage_plan'] = ' ';
            $company['final_diagnosis'] = ' ';
            $company['charged_to'] = ' ';
            $company['grand_total'] = number_format($value->grand_total,2);
            $company['total_approved'] = number_format($value->procedure_total,2);
            array_push($data['data'],$company);
            foreach ($value->availments as $keys => $info) {
                $diagnosis = ReportService::tableQueryByID('diagnosis_lists','id',$info->diagnosis_id);
                $record = [];
                $record['company'] = ' ';
                $record['approval_id'] = $info->approval_id;
                $record['member_name'] = $info->member_name;
                $record['coverage_plan'] = $info->coverage_plan_name;
                $record['final_diagnosis'] = $info->final_diagnosis;
                $record['charged_to'] = $diagnosis->name;
                $record['grand_total'] = number_format($info->grand_total,2);
                $record['total_approved'] = number_format($info->procedures_carewell_charged_total,2);
                array_push($data['data'],$record);
            }
        }   
        // use return view(path.of.blade.file, $data);
        // return view('exports.reports.company_availment_export_template', $data);
        return Excel::download(new CompanyAvailmentExport($data['data']), 'COMPANY AVAILMENT REPORT.xlsx');
    }

    public function exportToPDF()
    {
        $data = $this->index();
        // $policy_data = [];
        // $policy_data['data'] = $data;

        // $pdf = PDF::loadView('exports.reports.policy_data_pdf_template', $policy_data);

        // return view('exports.reports.policy_data_pdf_template', $policy_data);
        return $data;
    }

    private function getQueryResult($request)
    {
        $limit = $request['limit'] ?? 'showAll';
        $filter_by = $request['filter_by'] ?? 'All Dates';
        if($limit === 'showAll')
        {
            $date_from = $request['date_from'] ?? null;
            $date_to = $request['date_to'] ?? date('Y-m-d');
            $today = date('Y-m-d');
            return ReportService::getCompanyAvailment($filter_by, 'availments', 'company_id', $date_from, $date_to, $today);
        }
        
        return Company::paginate($limit);
    }
}