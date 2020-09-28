<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\PayableProvider;
use App\Models\PayableDoctor;
use App\Models\Availment;

use Illuminate\Http\Request;

class PayableMainController extends BaseController
{
    //
    private $main_model, $provider_model, $doctor_model;


    public function __construct(Payable $main_model, PayableProvider $provider_model, PayableDoctor $doctor_model){
        $this->main_model = $main_model;
        $this->provider_model = $provider_model;
        $this->doctor_model = $doctor_model;
    }

    public function show($id)
    {
        return $this->main_model->with([
            'provider',
            'user',
            'payable_availment.availment.provider.provider_payee',
            'payable_availment.availment.provider.doctor_providers.doctor',
            'payable_provider',
            'payable_doctor'
        ])->find($id);
    }

    public function store(Request $request)
    {
        $msg = ["Successfully create mark as close payable.","Payable saved as draft."];
        $data = Self::save($request,$msg);
        return response($data->message, $data->code);
        
    }

    public function update(Request $request)
    {
        $msg = ["Successfully update mark as close payable.","Payable saved as draft."];
        $data =  Self::save($request,$msg);
        return response($data->message, $data->code);
    }

    private function save($request, array $msg)
    {
        $save = Self::savePayable($request);
        
        if($request->saving === "save")
        {
            //get all availment id  and update status to close
            $availment_id = collect($request->payable_availment)->pluck('availment_id');
            Availment::whereIn('id',$availment_id)->update(['status'=>'close']);

            $message = $msg[0];
        }
        else
        {
            $message = $msg[1];
        }

        $code = 200;

        return (object) ['message' => $message, 'code' => $code];
    } 

    private static function savePayable($request)
    {
        // return $request->all();

        foreach($request->provider_payee as $key => $value)
        {
            $provider_payee = PayableProvider::updateOrCreate([
                'id' => isset($value["table_id"]) ? $value["table_id"] : null
            ],[
                "payable_id"                 => $request->id,
                "payee_id"                   => $value["id"],
                "bank_id"                    => isset($value["bank_id"]) ? $value["bank_id"] : null,
                "release_date"               => isset($value["release_date"]) ? $value["release_date"] : null,
                "check_number"               => isset($value["check_number"]) ? $value["check_number"] : null,
                "check_date"                 => isset($value["check_date"]) ? $value["check_date"] : null,
                "check_voucher_number"       => isset($value["cv_number"]) ? $value["cv_number"] : null,
                "amount"                     => isset($value["total_amount"]) ? $value["total_amount"] : 0,
                "received_by"                => isset($value["received_by"]) ? $value["received_by"] : null,
                "reference_approval_numbers" => isset($value["approval_id"]) ? $value["approval_id"] : null,
                "save_as_draft"              => $request->saving === "save" ? 0 : 1,
            ]);
        }

        foreach($request->provider_doctor as $key => $value)
        {
            $provider_doctor = PayableDoctor::updateOrCreate([
                'id' => isset($value["table_id"]) ? $value["table_id"] : null
            ],[
                "payable_id"                 => $request->id,
                "doctor_id"                  => $value["id"],
                "bank_id"                    => isset($value["bank_id"]) ? $value["bank_id"] : null,
                "release_date"               => isset($value["release_date"]) ? $value["release_date"] : null,
                "check_number"               => isset($value["check_number"]) ? $value["check_number"] : null,
                "check_date"                 => isset($value["check_date"]) ? $value["check_date"] : null,
                "check_voucher_number"       => isset($value["cv_number"]) ? $value["cv_number"] : null,
                "amount"                     => isset($value["total_amount"]) ? $value["total_amount"] : 0,
                "received_by"                => isset($value["received_by"]) ? $value["received_by"] : null,
                "reference_approval_numbers" => isset($value["approval_id"]) ? $value["approval_id"] : null,
                "save_as_draft"              => $request->saving === "save" ? 0 : 1,
            ]);
        }

        $payable                   = Payable::find($request->id);
        $payable->status           = $request->saving === "save" ? "close" : "draft" ;	 
        $payable->save();
    }
}
