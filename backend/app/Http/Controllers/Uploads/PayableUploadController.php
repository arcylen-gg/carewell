<?php
namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payable;
use PDF;
use stdClass;
class PayableUploadController extends Controller
{
    public function export(Request $request, Payable $model)
    {
        // return $model->with([
        //     'payable_availment.availment.benefit_type',
        //     'payable_availment.availment.diagnosis',
        //     'payable_availment.availment.provider',
        //     'payable_availment.availment.member',
        //     'payable_availment.availment.availment_doctor.doctor',
        //     'payable_availment.availment.availment_procedure.procedure',
        //     'provider',
        //     'payment_term',
        //     'user'
        //     ])->find($request->id);
        $data['data'] = $model->with([
            'payable_availment.availment.benefit_type',
            'payable_availment.availment.diagnosis',
            'payable_availment.availment.provider',
            'payable_availment.availment.member',
            'payable_availment.availment.availment_doctor.doctor',
            'payable_availment.availment.availment_procedure.procedure',
            'provider',
            'payment_term',
            'user'
            ])->find($request->id);
            
            $path = "";
            if($request['report-type'] === "Print for Internal Reports")
            {
                $path = 'exports.payable.payable_internal_report_export';
            }
            elseif($request['report-type'] === "Print for External Reports")
            {
                $path = 'exports.payable.payable_external_report_export';
            }
            elseif($request['report-type'] === "Print for Hospital/ Provider")
            {
                $path = 'exports.payable.payable_hospital_provider_export';
            }
            elseif($request['report-type'] === "Print for Doctors")
            {
                $path = 'exports.payable.payable_doctor_export';
            }
            elseif($request['report-type'] === "Version-1")
            {
                $path = 'exports.payable.payable_soa_with_payable_to_hospital_and_per_doctor';
                // $data['data']->other->doctors = [
                //     'doctors' => $this->soaWithPayableToHospitalAndPerDoctor($data['data'])
                // ];
                $data['data']['other'] = $this->soaWithPayableToHospitalAndPerDoctor($data['data']);
            }
            elseif($request['report-type'] === "Version-2")
            {
                $path = 'exports.payable.v2';
                // $data['data']->other->doctors = [
                //     'doctors' => $this->soaWithPayableToHospitalAndPerDoctor($data['data'])
                // ];
                $data['data']['other'] = $this->soaWithPayableToHospitalAndPerDoctor($data['data']);
            }
            elseif($request['report-type'] === "Version-3")
            {
                $path = 'exports.payable.v3';
                // $data['data']->other->doctors = [
                //     'doctors' => $this->soaWithPayableToHospitalAndPerDoctor($data['data'])
                // ];
                $data['data']['other'] = $this->soaWithPayableToHospitalAndPerDoctor($data['data']);
            }
            elseif($request['report-type'] === "Version-4")
            {
                $path = 'exports.payable.v4';
                // $data['data']->other->doctors = [
                //     'doctors' => $this->soaWithPayableToHospitalAndPerDoctor($data['data'])
                // ];
                $data['data']['other'] = $this->soaWithPayableToHospitalAndPerDoctor($data['data']);
            }
            
            // dd($data['data']['other']);
            return view($path, $data);

        $pdf = PDF::loadView($path,$data)->setPaper('A4', 'landscape')->setWarnings(false);
        return $pdf->stream();
    }

    private function soaWithPayableToHospitalAndPerDoctor($data){
        $array = array();

        foreach ($data->payable_availment as $key => $value) {
            // array_push($array,$value->availment->availment_doctor);
            foreach ($value->availment->availment_doctor as $key_doctor => $value_doctor) {
                
                $id = $value_doctor->doctor->id;

                // array_push($array, $id);
                
                
                // if(array_key_exists($id, $array)) {
                //     $array[$id]["name"] = $value_doctor->doctor->name;
                //     $array[$id]["total"] = $array[$id]['total'] + $value_doctor->doctors_fee;
                // } else {
                //     $array[$id] = [
                //         'name' => $value_doctor->doctor->name,
                //         'total' => $value_doctor->doctors_fee
                //     ];
                // }
                if(array_key_exists($id, $array)) 
                {
                    // $array[$id]->name = $value_doctor->doctor->name;
                    // $array[$id]->total = $array[$id]->total + $value_doctor->doctors_fee;
                    $array[$id] = [
                        'name' => $value_doctor->doctor->name,
                        'tax' => $value_doctor->doctor->tax,
                        'total' => $array[$id]['total'] + $value_doctor->doctors_fee
                    ];

                } else {
                    
                    // $array[$id] = new stdClass;
                    // $array[$id]->name = $value_doctor->doctor->name;
                    // $array[$id]->total = $value_doctor->doctors_fee;

                    $array[$id] = [
                        'name' => $value_doctor->doctor->name,
                        'tax' => $value_doctor->doctor->tax,
                        'total' => $value_doctor->doctors_fee
                    ];
                    // $array[$id]['name'] = $value_doctor->doctor->name;
                    // $array[$id]['total'] = $value_doctor->doctors_fee;
                }

               
            }
        }
        // dd($array);
        // return [
        //     'doctors' => $array
        // ];
        return [
            'doctors' => $array
        ];

    }
}