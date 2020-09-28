<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\Doctor;
use App\Models\DoctorsProvider;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;


class DoctorController extends BaseController
{
    private $model;

    public function __construct(Doctor $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){
        $this->model = $this->model->with([
            'doctorProvider.provider'
        ]); //lazy loader
    }

    public function index(Request $request)
    {
        $this->loadWithRelatedModel();
        $this->filterTable($request->all());
        return $this->getResultList($this->model->orderBy('first_name')->orderBy('middle_name')->orderBy('last_name'));
    }

    public function filterTable()
    { 
        $related_fields             = [""];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name","first_name","middle_name","last_name"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function store(Request $request)
    {
        $validator = $this->validateRequestInput(request()->all(), $this->model->requestRules());

        // $validator = Validator::make($request->all(), [
        //     "first_name"    => "bail|required",
        //     "last_name"     => "bail|required",
        //     "email"         => "bail|unique:doctors,email"
        // ]);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $doctor                 = $this->model;
            $doctor->first_name     = $request->first_name;
            $doctor->middle_name    = $request->middle_name;
            $doctor->last_name      = $request->last_name;
            $doctor->contact_number = $request->contact_number;
            $doctor->email          = $request->email;
            $doctor->tin            = $request->tin;
            $doctor->tax            = $request->tax;
            $doctor->save();

            foreach($request->list as $key => $value)
            {
                DoctorsProvider::create([
                    'doctor_id' => $doctor->id,
                    'provider_id' => $value['name'],
                ]);
            }
            $message = 'Doctor has been successfully created!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." has been added to doctor.";
            AuditTrailService::createAuditTrail('Added Doctor', $description, null ,$new_data,$this->model->id,'App\Models\Doctor');
        }
 
        return response($message, $code);
    }
    public function update(Request $request,$id)
    {
        $old_data = AuditTrailService::getDataForAuditTrail($this->model,$id);

        if($request->for_archive)
        {
            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $new_data->is_archived = $new_data->is_archived ? 0 : 1;
            $response = $new_data->is_archived ? 'Archive' : 'Restore';
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." has been ".strtolower($response).".";
            AuditTrailService::createAuditTrail($response.' Doctor', $description, $old_data ,$new_data,$id,'App\Models\Doctor');
            return $this->archiveData($this->model, "doctor", $id);
        }

        $rules = $this->model->requestRules();
        // $rules["email"] = $rules["email"] . ",email," . $id;
        $validator = $this->validateRequestInput(request()->all(), $rules); 
        
        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $doctor                 = $this->model->find($id);
            $doctor->first_name     = $request->first_name;
            $doctor->middle_name    = $request->middle_name;
            $doctor->last_name      = $request->last_name;
            $doctor->contact_number = $request->contact_number;
            $doctor->email          = $request->email;
            $doctor->tin            = $request->tin;
            $doctor->tax            = $request->tax;
            $doctor->save();
            
            $provider = DoctorsProvider::where("doctor_id", $id)->delete();
            foreach($request->list as $key => $value)
            {
                DoctorsProvider::create([
                    'doctor_id' => $doctor->id,
                    'provider_id' => $value['provider_id'],
                ]);
            }
            
            $message = 'Doctor has been successfully updated!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." doctor has been updated.";
            AuditTrailService::createAuditTrail('Update Doctor', $description, $old_data ,$new_data,$id,'App\Models\Doctor');
        }
 
        return response($message, $code);
    
    }
}
