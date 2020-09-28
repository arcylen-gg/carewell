<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Cal;
use App\Models\CalMember;
use App\Models\Company;
use App\Models\CoveragePlan;
use App\Models\Member;
use App\Models\PaymentMode;
use App\Globals\ReportService;
use App\Exports\PolicyDataExport;

use DB;
use Excel;
use PDF;
use Carbon\Carbon;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;

class PolicyData implements GetReportDataInterface
{
    public function index()
    {
        $request = request()->all();

        return $this->getQueryResult($request);
    }

    public function exportToExcel()
    {
        // I use $this->index() because data get on this function are use in exporting;
        $data = $this->index();
        $policy_data = [];
        $policy_data['data'] = $data;
        // use return view(path.of.blade.file, $data);
        // return view('exports.reports.policy_data_export_template', $policy_data);
        return Excel::download(new PolicyDataExport($policy_data['data']), 'POLICY DATA REPORT.xlsx');
    }

    public function exportToPDF()
    {
        $data = $this->index();
        $policy_data = [];
        $policy_data['data'] = $data;

        // $pdf = PDF::loadView('exports.reports.policy_data_pdf_template', $policy_data);

        return view('exports.reports.policy_data_pdf_template', $policy_data);
        // return $pdf->download('test.pdf');
    }

    private function getQueryResult($request)
    {
        $limit = $request['limit'] ?? '';
        $filter_by = $request['filter_by'] ?? 'All Dates';
        $policy_info = [];
        if($limit)
        {
            $request['filter_by'] = $filter_by;
            $date_from = $request['date_from'] ?? null;
            $date_to = $request['date_to'] ?? date('Y-m-d');
            $today = date('Y-m-d');
            return ReportService::getPolicyData($request, 'cal_members', 'member_id', $date_from, $date_to, $today);
        }
    }
}