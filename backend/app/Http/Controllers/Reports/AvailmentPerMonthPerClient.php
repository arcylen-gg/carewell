<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\BenefitType;
use App\Models\Availment;
use App\Models\Company;
use App\Exports\AvailmentPerMonthPerClientExport;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;
use Excel;

class AvailmentPerMonthPerClient implements GetReportDataInterface
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
        $availment_per_month_per_client = [];
        $availment_per_month_per_client['data'] = $data;
        // // use return view(path.of.blade.file, $data);
        return Excel::download(new AvailmentPerMonthPerClientExport($availment_per_month_per_client['data']), 'AVAILMENT PER MONTH PER CLIENT REPORT.xlsx');
        // return $data;
    }

    public function exportToPDF()
    {
        $data = $this->index();
        $availment_month = [];
        $availment_month['data'] = $data['item'];
        $availment_month['overall_monthly_count'] = $data['overall_monthly_count'];
        $availment_month['overall_monthly_total'] = $data['overall_monthly_total'];
        $availment_month['overall_grand_total'] = $data['overall_grand_total'];
        $availment_month['year'] = $data['year_selected'];
        return view('exports.reports.availment_per_month_per_client_pdf_template', $availment_month);
    }

    private function getQueryResult($request)
    {
        $limit = $request['limit'] ?? 'showAll';
        $request['year'] = $request['year'] ?? date('Y');

        if($limit === 'showAll')
        {
            $company = Availment::whereYear('date_avail', $request['year'])->select('company_id')->distinct()->get();
            $data = Company::whereIn('id',$company)->get();
            return $this->getData($data, $request);
        }

        $company = Availment::whereYear('date_avail', $request['year'])->select('company_id')->distinct()->paginate($limit);
        $data = Company::whereIn('id',$company)->get();
        return $this->getData($data, $request);
    }

    private function getData($model, $request){
        $overall_monthly_count = [];
        $overall_monthly_total = [];

        foreach ($model as $key => $value) {
            $monthly_count = [];
            $monthly_total = [];

            for ($i=1; $i < 13; $i++) { 
                $month = Availment::where(function($query) use ($i, $request){
                    $query->whereYear('date_avail', $request['year'])
                    ->whereMonth('date_avail', $i);
                });

                array_push($monthly_count, $month->where('company_id', $value->id)->count());
                array_push($monthly_total, $month->where('company_id', $value->id)->sum('grand_total'));
            }

            $value->monthly_count = $monthly_count;
            $value->monthly_total = $monthly_total;
            $value->year_total = array_sum($monthly_total);
        }

        for ($i=1; $i < 13; $i++) { 
            $month = Availment::where(function($query) use ($i, $request){
                $query->whereYear('date_avail', $request['year'])
                ->whereMonth('date_avail', $i);
            });

            array_push($overall_monthly_count, $month->count());
            array_push($overall_monthly_total, $month->sum('grand_total'));
        }

        return [
            'item' => $model,
            'overall_monthly_count' => $overall_monthly_count,
            'overall_monthly_total' => $overall_monthly_total,
            'overall_grand_total' => array_sum($overall_monthly_total),
            'year_selected' => date('Y',strtotime($request['year']))
        ];
    }
}