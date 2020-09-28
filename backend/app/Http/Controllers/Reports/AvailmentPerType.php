<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Globals\ReportService;
use App\Exports\AvailmentPerTypeExport;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;
use Excel;
use PDF;
use DB;

class AvailmentPerType implements GetReportDataInterface
{
    public function index()
    {
        $request = request()->all();

        return $this->getQueryResult($request);
    }

    public function exportToExcel()
    {
        // // I use $this->index() because data get on this function are use in exporting; 
        $data = $this->index();
        $availment_per_type = [];
        $availment_per_type['data'] = $data;
        // // use return view(path.of.blade.file, $data);
        return Excel::download(new AvailmentPerTypeExport($availment_per_type['data']), 'AVAILMENT PER TYPE PER COMPANY REPORT.xlsx');
        // // return $data;
    }

    public function exportToPDF()
    {
        $data = $this->index();
        $benefit_data = DB::table('benefit_types')->get();
        $total_patient_count = [];
        $total_availment_amount = [];
        
        // dd($data[1]);
        foreach ($benefit_data as $key => $value) {
            $ctr_patient = 0;
            $ctr_amount = 0;
            foreach ($data as $index => $info) {
                $ctr_patient += $info->patient_count[$key];
                $ctr_amount += $info->total_per_benefit[$key];
            }
            array_push($total_patient_count,$ctr_patient);
            array_push($total_availment_amount, $ctr_amount);
        }
        $availment_per_type = [];
        $availment_per_type['data'] = $data;
        $availment_per_type['total_patient_count'] = $total_patient_count;
        $availment_per_type['total_availment_amount'] = $total_availment_amount;
        $availment_per_type['overall_patient_count'] = array_reduce($total_patient_count, function($a, $b) {
            return $a + $b;
        }, 0);
        $availment_per_type['overall_availment_amount'] = array_reduce($total_availment_amount, function($a, $b) {
            return $a + $b;
        }, 0);

        // // $pdf = PDF::loadView('exports.reports.policy_data_pdf_template', $policy_data);

        return view('exports.reports.availment_per_type_per_company_pdf_template', $availment_per_type);
        // return $data;
    }

    private function getQueryResult($request)
    {
        $limit = $request['limit'] ?? 'showAll';
        $request['year'] = $request['year'] ?? date('Y');
        if($limit === 'showAll')
        {
            return ReportService::getAvailmentPerType(Company::get(),  $request);
        }
        return ReportService::getAvailmentPerType(Company::paginate($limit), $request);
    }
}