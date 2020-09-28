<?php

namespace App\Http\Controllers\Uploads;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Exports\DoctorExport;
use Illuminate\Validation\Rule;
use App\Models\Doctor;
use App\Models\DoctorsProvider;
use App\Models\Provider;
use Illuminate\Http\Request;
use Validator;
use Excel;

class DoctorUploadController extends Controller
{
    public function export(){
        return Excel::download(new DoctorExport, 'Doctor_Template.xlsx');
    }

    public function import(Request $request){
        // return $request->all();

        //Validator custom messages
        $messages = [
            'first_name.required' => 'No first name.',
            'last_name.required' => 'No last name.',
            'email.email' => 'Check email format.',
            'tax.required' => 'No tax.',
            'provider.required' => 'No provider.'
        ];

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email',
            'tax' => 'required',
            'provider' => 'required'
        ],$messages);
        

        if($validator->fails())
        {
            $errors = $validator->errors();

            return Self::setReturnData('red',$errors->first(),$request->all());
        }
        else
        {
            $provider = Provider::where('name',$request->provider)->select('id')->first();

            if(!$provider)
            {
                return Self::setReturnData('red','Provider not found.',$request->all());
            }

            $check_if_doctor_exist =    Doctor::where('first_name',$request->first_name)
                                        ->where('middle_name',$request->middle_name)
                                        ->where('last_name',$request->last_name)
                                        ->first();
            if($check_if_doctor_exist)
            {

                $check_if_doctor_provider_exist =   DoctorsProvider::where('doctor_id',$check_if_doctor_exist->id)
                                                    ->where('provider_id', $provider->id)
                                                    ->first();
                
                if(!$check_if_doctor_provider_exist)
                {

                    Self::insertDoctorProvider($check_if_doctor_exist->id, $provider->id);
        
                    return Self::setReturnData('green','Add tagged provider.',$request->all());
                }

                return Self::setReturnData('red','Doctor-provider exists.',$request->all());

            }
            else
            {
                $doctor                 = new Doctor();
                $doctor->first_name     = $request->first_name;
                $doctor->middle_name    = $request->middle_name;
                $doctor->last_name      = $request->last_name;
                $doctor->contact_number = $request->contact_number;
                $doctor->email          = $request->email;
                $doctor->tin            = $request->tin;
                $doctor->tax            = $request->tax;
                $doctor->save();
    
                Self::insertDoctorProvider($doctor->id, $provider->id);
    
                return Self::setReturnData('green','New Doctor Added.',$request->all());
            }
        }
    }

    public function setReturnData($color, $remarks, $data){
        return [
            'color' => $color,
            'remarks' => $remarks,
            'first_name' => $data['first_name'] ?? '',
            'middle_name' => $data['middle_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'] ?? '',
            'contact_number' => $data['contact_number'] ?? '',
            'tin' => $data['tin'] ?? '',
            'tax' => $data['tax'] ?? '',
            'provider' => $data['provider'] ?? '',
        ];
    }

    public function insertDoctorProvider($doctor_id, $provider_id){
        DoctorsProvider::create([
            'doctor_id' => $doctor_id,
            'provider_id' => $provider_id
        ]);
    }
}