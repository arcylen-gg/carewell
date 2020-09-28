<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\BenefitType;
use App\Globals\ReportService;
use App\Exports\AvailmentMonitoringExport;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;
use Excel;

class AvailmentMonitoring implements GetReportDataInterface
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
        $availment_monitoring = [];
        $availment_monitoring['data'] = $data;
        // use return view(path.of.blade.file, $data);
        return Excel::download(new AvailmentMonitoringExport($availment_monitoring['data']), 'AVAILMENT MONITORING REPORT.xlsx');
        // return $data;
    }

    public function exportToPDF()
    {
        $data = $this->index();
        $total_patient_per_month = [];
        $total_availment_amount = [];
        $availment_monitoring = [];
        $year_selected = "";
        for ($i = 0; $i < 12; $i++) { 
            $patient_count = 0;
            $amount_total = 0;
            foreach ($data as $key => $value) {
               $patient_count += $value->no_of_patient[$i];
               $amount_total += $value->total_month[$i];
               $year_selected = $value->year_selected;
            }
            array_push($total_patient_per_month, $patient_count);
            array_push($total_availment_amount, $amount_total);
        }
        $availment_monitoring['data'] = $data;
        $availment_monitoring['total_patient_per_month'] = $total_patient_per_month;
        $availment_monitoring['total_availment_amount'] = $total_availment_amount;
        $availment_monitoring['overall_patient_count'] = array_reduce($total_patient_per_month, function($a, $b) {
            return $a + $b;
        }, 0);
        $availment_monitoring['overall_availment_amount'] = array_reduce($total_availment_amount, function($a, $b) {
            return $a + $b;
        }, 0);
        $availment_monitoring['year'] = $year_selected;

        return view('exports.reports.availment_monitoring_pdf_template', $availment_monitoring);
        // return $data;
    }

    private function getQueryResult($request)
    {
        $limit = $request['limit'] ?? 'showAll';
        $request['year'] = $request['year'] ?? date('Y');
        if($limit === 'showAll')
        {
            return ReportService::getAvailmentMonitoring(BenefitType::get(),  $request);
        }
        return ReportService::getAvailmentMonitoring(BenefitType::paginate($limit), $request);
    }
}