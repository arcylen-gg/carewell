<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Globals\ReportService;
use App\Exports\AvailmentSummaryExport;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;
use Excel;

class AvailmentSummary implements GetReportDataInterface
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
        $availment_summary = [];
        $availment_summary['data'] = $data;
        // // use return view(path.of.blade.file, $data);
        return Excel::download(new AvailmentSummaryExport($availment_summary['data']), 'AVAILMENT SUMMARY REPORT.xlsx');
    }

    public function exportToPDF()
    {
        $data = $this->index();
        $arrayData = [];
        $availment_summary = [];
        $year_selected = "";
        for ($i = 0; $i < 12; $i++) { 
            $count = 0;
            foreach ($data as $key => $value) {
                $count += $value->no_of_availments[$i];
                $year_selected = $value->year_selected;
            }
            array_push($arrayData,$count);
        }
        $availment_summary['data'] = $data;
        $availment_summary['total_no_of_availments'] = $arrayData;
        $availment_summary['overall_total_of_availments'] = array_reduce($arrayData, function($a, $b) {
            return $a + $b;
        }, 0);
        $availment_summary['year'] = $year_selected;
        // dd($availment_summary);
        return view('exports.reports.availment_summary_pdf_template', $availment_summary);
    }

    private function getQueryResult($request)
    {
        $limit = $request['limit'] ?? 'showAll';
        $request['year'] = $request['year'] ?? date('Y');
        if($limit === 'showAll')
        {
            if($request['company_id'])
            {
                $data = json_decode($request['company_id']);
                $query = Company::whereIn('id',$data)->get();
            }
            else
            {
                $query = Company::get();
            }
            return ReportService::getAvailmentSummary($query, $request);
        }
        
        return Company::paginate($limit);
    }
}