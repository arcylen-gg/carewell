<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Availment;
use App\Models\Company;
use App\Models\BenefitType;

use App\Exports\ComparativeAvailmentExport;
use Excel;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;

class ComparativeAvailment implements GetReportDataInterface
{
    public function index()
    {
        $request = request()->all();

        // return $request;

        return $this->getQueryResult($request);
    }

    public function exportToExcel()
    {
        $data['data'] = $this->index();
        dd($data);
        $data['request'] = request()->all();

        return Excel::download(new ComparativeAvailmentExport($data), 'COMPARATIVE AVAILMENT REPORT.xlsx');
    }

    public function exportToPDF()
    {
        $data['data'] = $this->index();
        $data['request'] = request()->all();

        return view('exports.reports.comparative_availment_export_template', $data);
    }

    private function getQueryResult($request)
    {
        $limit = $request['limit'] ?? 'showAll';
       
        if($limit === 'showAll')
        {
            return $this->getData(Company::where('is_archived', 0)->select('id', 'name')->get(), $request);
        }
        
        return $this->getData(Company::where('is_archived', 0)->select('id', 'name')->paginate($limit), $request);
    }

    private function getData($model, $request)
    {
        $year_from = $request['year_from'];
        $year_to = $request['year_to'];

        $company = $model;

        foreach ($company as $key => $value) {
            $month_from = [];
            $month_to = [];

            for ($i=1; $i < 13; $i++) { 
                $query_monthly_from = Availment::where(function($q) use ($year_from, $i) {
                        $q->whereYear('date_avail', '=', $year_from)
                        ->whereMonth('date_avail', '=', $i);
                    })->where('company_id', $value->id);

                $count_from = $query_monthly_from->sum('grand_total');

                array_push($month_from, $count_from);
                
                $query_monthly_to = Availment::where(function($q) use ($year_to, $i) {
                        $q->whereYear('date_avail', '=', $year_to)
                        ->whereMonth('date_avail', '=', $i);
                    })->where('company_id', $value->id);
                
                $count_to = $query_monthly_to->sum('grand_total');

                array_push($month_to, $count_to);
            }

            $value->count_monthly_from = $month_from;

            $value->count_monthly_to = $month_to;

            $value->count_year_from = Availment::whereYear('date_avail', $year_from)->where('company_id', $value->id)->count();

            $value->count_year_to = Availment::whereYear('date_avail', $year_to)->where('company_id', $value->id)->count();
        }

        $total_month_from = [];
        $total_month_to = [];

        for ($i=1; $i < 13; $i++) { 
            $query_monthly_from = Availment::where(function($q) use ($year_from, $i) {
                $q->whereYear('date_avail', '=', $year_from)
                ->whereMonth('date_avail', '=', $i);
            });

            $total_count_from = $query_monthly_from->sum('grand_total');

            $query_monthly_to = Availment::where(function($q) use ($year_to, $i) {
                $q->whereYear('date_avail', '=', $year_to)
                ->whereMonth('date_avail', '=', $i);
            });

            $total_count_to = $query_monthly_to->sum('grand_total');

            array_push($total_month_from, $total_count_from);

            array_push($total_month_to, $total_count_to);
        }

        $data['item'] = $company;
        $data['from'] = $total_month_from;
        $data['to'] = $total_month_to;
        $data['total_from'] = array_sum($total_month_from);
        $data['total_to'] = array_sum($total_month_to);

        return $data;
    }
}