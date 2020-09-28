<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\BenefitType;
use App\Models\Availment;
use App\Models\Company;
use App\Exports\AvailmentPerMonthPerClientExport;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;
use Excel;
use DB;
class ActiveMemberPerMonth implements GetReportDataInterface
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

    private function getData($model, $request)
    {
        $get_all = DB::table('companies')->where('is_archived', 0)->get();
        $_total = null;
        $_raw_total = array();
        $return = null;
        foreach ($get_all as $key => $value) 
        {
            $return['active_member'][$key] = $value;
            $return['active_member'][$key]->premium = $this->company_premium($value->id);
            $return['active_member'][$key]->per_month_count = $this->all_months($value->id, $request['year']);
            $return['active_member'][$key]->total_per_client = array_sum($return['active_member'][$key]->per_month_count);
            $_raw_total[$key] = $return['active_member'][$key]->per_month_count;
        }
        for ($j=1; $j < 13 ; $j++) 
        { 
            $_total[$j] = 0;
            for($i=0; $i <= count($_raw_total) - 1; $i++) 
            { 
                $_total[$j] += $_raw_total[$i][$j];
            }
        }
        $return['total_per_month'] = $_total;
        $return['grand_total'] = array_sum($_total);
        return $return;
    }
    public function company_premium($company_id)
    {
        $ret = '';
        $company_coverage = DB::table('company_coverage_plans')->where('company_id', $company_id)
                                                               ->leftjoin('coverage_plans','coverage_plans.id','coverage_plan_id')
                                                               ->get();
        foreach ($company_coverage as $key => $value) 
        {
            $ret .= $value->name.' - '.number_format($value->monthly_premium, 2).',';
        }
        return $ret;
    }
    public function all_months($company_id, $year)
    {
        $ctr = null;
        for ($i = 1; $i < 13 ; $i++) 
        {
            $ctr[$i] = DB::table('cal_members')->leftjoin('cals','cal_id','cals.id')
                                               ->whereMonth('payment_start_date',$i)
                                               ->whereYear('payment_start_date',$year)
                                               ->where('cal_members.status',0)
                                               ->where('company_id', $company_id)
                                               ->groupBy('cals.id')
                                               ->groupBy('cals.company_id')
                                               ->count('member_id');
        }
        return $ctr;
    }
}