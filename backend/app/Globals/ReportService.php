<?php
namespace App\Globals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Member;
use App\Models\Availment;

use DB;
use DateTime;
use Carbon\Carbon;

class ReportService
{
    public static function getCompanyAvailment($filter_by, $db_table, $select_column, $date_from = null, $date_to = null, $today = null, $company_id = null)
    {
        $company_ids = Self::filters($filter_by, $db_table, $select_column, $date_from, $date_to, $today);
        $company = [];
        foreach ($company_ids as $key => $value) {
            $query = DB::table('companies')
                ->where('id', $value->company_id)
                ->first();
            array_push($company, $query);
        }
        foreach ($company as $key => $value) {
            $grand_total_amount = 0;
            $charged_to_carewell = 0;
            $company_availment = Self::filters($filter_by, $db_table, $select_column, $date_from, $date_to, $today, $value->id);
            
            foreach ($company_availment as $keys => $data) {
                $member_name = Self::tableQueryByID('members','id',$data->member_id);
                $data->member_name = $member_name->first_name.' '.$member_name->middle_name.' '.$member_name->last_name;
                $coverage_plan = Self::tableQueryByID('coverage_plans','id',$data->coverage_plan_id);
                $data->coverage_plan_name = $coverage_plan->name;
                $diagnosis = Self::tableQueryByID('diagnosis_lists','id',$data->diagnosis_id);
                $data->diagnosis_name = Self::substrwords($diagnosis->name,30);
                $data->chief_complaint = Self::substrwords($data->chief_complaint,30);
                $data->initial_diagnosis = Self::substrwords($data->initial_diagnosis,30);
                $data->final_diagnosis = Self::substrwords($data->final_diagnosis,30);

                $grand_total_amount += $data->grand_total;
                $charged_to_carewell += $data->procedures_carewell_charged_total;
            }
            $value->availments = $company_availment;
            $value->grand_total = $grand_total_amount;
            $value->procedure_total = $charged_to_carewell;
        }
        return $company;
    }

    public static function getPolicyData($filters, $db_table, $select_column, $date_from = null, $date_to = null, $today = null, $company_id = null)
    {
        $cal_member = [];
        $policy_data = Self::filters($filters['filter_by'], $db_table, $select_column, $date_from, $date_to, $today);
        foreach ($policy_data as $key => $value) {
            $member_data = Member::with(['testing'])
                ->where('id', $value->member_id)
                ->where('is_archived', 0)
                ->first();
            array_push($cal_member, $member_data);
        }
        $member_record = [];
        foreach ($cal_member as $key => $value) {
            $company_id = $value->testing->company_id;
            $deployment_id = $value->testing->company_deployment_id;
            $payment_id = $value->testing->payment_mode_id;
            $coverage_plan_id = $value->testing->coverage_plan_id;
            $carewell_id = $value->testing->carewell_id;

            $company = Self::tableQueryByID('companies', 'id', $company_id);
            $payment_mode = Self::tableQueryByID('payment_modes', 'id', $payment_id);
            $cal_members = DB::table('cal_members')
                ->where('member_id', $value->id)
                ->where('deployment_id', $deployment_id)
                ->first();
            $cal = Self::tableQueryByID('cals', 'id', $cal_members->cal_id);
            $coverage_plan = Self::tableQueryByID('coverage_plans', 'id', $coverage_plan_id);

            $record = [];
            $record['account_type'] = $company->account_type;
            $record['plan_name'] = $coverage_plan->name;
            $record['policy_number'] = $company->code;
            $record['policy_effective_date'] = $company->policy_effective_date;
            $record['policy_expiry_date'] = $company->policy_expiry_date;
            $record['member_number'] = $carewell_id;
            $record['member_type'] = $coverage_plan->member_type;
            $record['gender'] = $value->gender;
            $record['birth_date'] = date_format(date_create($value->birth_date),"m/d/Y");;
            $record['payment_mode'] = $payment_mode->name;
            $record['member_effective_date'] = date_format(date_create($cal_members->date_paid),"m/d/Y");
            $record['member_expiry_date'] = date('m/d/Y', strtotime($cal_members->date_paid. ' + 365 days'));
            $record['bill_from'] = date_format(date_create($cal->payment_start_date),"m/d/Y");
            $record['bill_to'] = date_format(date_create($cal->payment_end_date),"m/d/Y");
            $record['gross_membership_fee'] = $coverage_plan->monthly_premium;
            $record['net_membership_fee'] = $coverage_plan->monthly_premium - ($coverage_plan->monthly_premium * 0.12);
            array_push($member_record,$record);
        }
        return $member_record;
    }

    public static function getAvailmentMonitoring($model, $request)
    {
        $benefit_model = $model;
        foreach ($benefit_model as $key => $value) {
            $month = [];
            $total_month = [];

            for ($i = 1; $i < 13; $i++) { 
                $filter = [
                    'year' => $request['year'],
                    'month' => $i
                ];

                $query_monthly = Availment::where(function($query) use($filter) {
                    $query->whereMonth('date_avail', '=', $filter['month'])
                    ->whereYear('date_avail', '=', $filter['year']);
                })->where('benefit_type_id', $value->id);
                
                $amount = $query_monthly->sum('grand_total');
                array_push($total_month, $amount);

                $patients = $query_monthly->distinct('member_id')->count('member_id');
                array_push($month, $patients);
            }

            $value->no_of_patient = $month;
            $value->total_month = $total_month;

            $value->total_patient = array_reduce($month, function($a, $b) {
                return $a + $b;
            }, 0);
            $value->total_amount = array_reduce($total_month, function($first, $next) {
                return $first + $next;
            }, 0);
            $value->year_selected = date('Y', strtotime($request['year']));
        }
        return $benefit_model;
    }

    public static function getAvailmentSummary($model, $request)
    {
        $company_summary = $model;
        foreach ($company_summary as $key => $value) {
            $month = [];
            $total_month = [];

            for ($i = 1; $i < 13; $i++) { 
                $filter = [
                    'year' => $request['year'],
                    'month' => $i
                ];

                $query_monthly = Availment::where(function($query) use($filter) {
                    $query->whereMonth('date_avail', '=', $filter['month'])
                    ->whereYear('date_avail', '=', $filter['year']);
                })->where('company_id', $value->id);
                
                $availment = $query_monthly->distinct('approval_id')->get();
                array_push($month, count($availment));
            }
            $value->no_of_availments = $month;

            $value->total_availments = array_reduce($month, function($a, $b) {
                return $a + $b;
            }, 0);
            
            $value->year_selected = date('Y', strtotime($request['year']));
        }
        return $company_summary;
    }
    
    public static function getAvailmentPerType($model, $request)
    {
        $company_model = $model;
        foreach ($company_model as $key => $value) {
            $company_benefit_availment = [];
            $total_amount = [];
            $count_availment = [];
            $count_patient = [];

            $benefit_model = DB::table('benefit_types')->get();
            foreach ($benefit_model as $key => $data) {
                if($request['month'] != 0)
                {
                    $query_benefit = Availment::where('company_id', $value->id)
                        ->where('benefit_type_id', $data->id)
                        ->whereMonth('date_avail', '=', $request['month'])
                        ->whereYear('date_avail', '=', $request['year']);
                }
                else
                {
                    $query_benefit = Availment::where('company_id', $value->id)
                        ->where('benefit_type_id', $data->id)
                        ->whereYear('date_avail', '=', $request['year']);
                }
                array_push($total_amount, $query_benefit->sum('grand_total'));
                array_push($count_patient, $query_benefit->distinct('member_id')->count('member_id'));
                array_push($count_availment, $query_benefit->count());
                array_push($company_benefit_availment, $query_benefit->get());
            }
            $value->patient_count = $count_patient;
            $value->record_of_availments = $company_benefit_availment;
            $value->no_of_availments = $count_availment;
            $value->total_per_benefit = $total_amount;
            $value->total_availments_amount = array_reduce($total_amount, function($a, $b) {
                return $a + $b;
            }, 0);
            $value->total_availments = array_reduce($count_availment, function($a, $b) {
                return $a + $b;
            }, 0);
            $value->total_patients = array_reduce($count_patient, function($a, $b) {
                return $a + $b;
            }, 0);
            if($request['month'] != 0)
            {
                $dateObj   = DateTime::createFromFormat('!m', $request['month']);
                $value->month_selected = $dateObj->format('F');
            }
            else
            {
                $value->month_selected = $request['month'];
            }
            $value->year_selected = date('Y', strtotime($request['year']));
        }
        return $company_model;
    }

    public static function substrwords($text, $maxchar, $end='...') 
    {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);      
            $output = '';
            $i      = 0;
            while (1) {
                $length = strlen($output)+strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } 
                else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } 
        else {
            $output = $text;
        }
        return $output;
    }
    
    public static function tableQueryByID($table_name, $select_column, $column_query)
    {
        $query = DB::table($table_name)
            ->where($select_column, $column_query)
            ->first();
        return $query;
    }

    public static function filters($filter_by, $db_table, $select_column, $date_from = null, $date_to = null, $today = null, $select_id = null)
    {
        $query = DB::table($db_table);

        if($filter_by == 'All Dates')
        {
            if($select_id)
            {
                $query = $query->where($select_column, $select_id)
                ->get();
            }
            else
            {
                $query = $query->select($select_column)
                ->distinct()
                ->get();
            }
            return $query;
        }
        else if($filter_by == 'Custom')
        {
            $query = $query->where('updated_at','>',$date_from)
            ->where('updated_at','<', $date_to);
        }
        else if($filter_by == 'Today')
        {
            $query = $query->where('updated_at', $today);
        }
        else if($filter_by == 'This Week')
        {
            Carbon::setWeekStartsAt(Carbon::SUNDAY);
            
            $query = $query->whereBetween('updated_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
        }
        else if($filter_by == 'This Month')
        {
            $query = $query->whereMonth('updated_at','=',date('m'))
            ->whereYear('updated_at','=',date('Y'));
        }
        else if($filter_by == 'This Quarter')
        {
            $curMonth = date("m", time());
            $curQuarter = floor($curMonth/3);
            $start = new Carbon('first day of January');
            $end = $start->copy()->endOfMonth();
            $start_quarter = $start->addMonth(3*$curQuarter)->format('Y-m-d');
            $end_quarter = $end->addMonth(3*($curQuarter +1) -1)->format('Y-m-d');

            $query = $query->whereDate('updated_at', '>=', $start_quarter)
            ->whereDate('updated_at', '<', $end_quarter);
        }
        else if($filter_by == 'This Year')
        {
            $query = $query->whereYear('updated_at', '=', date('Y'));
        }

        if($select_id)
        {
            $query = $query->where($select_column, $select_id)
            ->get();
        }
        else
        {
            $query = $query->select($select_column)
            ->distinct()
            ->get();
        }
        return $query;
    }
}