<?php
namespace App\Http\Controllers;

use App\Models\CalPaidCount;

use Illuminate\Http\Request;

class CalPaidCountController extends Controller
{
    private $model;

    public function __construct(CalPaidCount $model)
    {
        $this->model = $model;
    }

    public function store(Request $request)
    {
        $start_all = isset($request->period_count_date[0]['start_paid']) ? $request->period_count_date[0]['start_paid'] : date('Y-m-d');
        $end_all = isset($request->period_count_date[0]['end_paid']) ? $request->period_count_date[0]['end_paid'] : date('Y-m-d');
        foreach($request->period_count_date as $key => $value)
        {
            $this->model = CalPaidCount::create([
                'cal_id'            => $request->cal_id,
                'cal_member_id'     => $request->id,
                'start_date'        => $value['start_paid'] ?? $start_all,
                'end_date'          => $value['end_paid'] ?? $end_all,
                'amount'            => $value['amount'],
            ]);
        }
        $message = 'Collection active list member successfully update period count!';
        $code = 200;
        return response($message, $code);
    }

    public function show(Request $request, $id)
    {
        $this->model = $this->model;
        return $this->model->where('cal_id',$id)->get();
    }

    public function update(Request $request, $id)
    {
        $this->model = $this->model;

        $validateThis = [
            'cal_id'            => $id,
            'cal_member_id'     => $request->member_id,
        ];
        $this->model->where($validateThis)->delete();

        foreach($request->period_count_date as $key => $value)
        {
            $this->model = CalPaidCount::create([
                'cal_id'            => $request->cal_id,
                'cal_member_id'     => $request->member_id,
                'start_date'        => $value['start_paid'],
                'end_date'          => $value['end_paid'],
                'amount'            => $value['amount'],
            ]);
        }

        $message = 'Collection active list member successfully update period count!';
        $code = 200;
        return response($message, $code);
    }
}
