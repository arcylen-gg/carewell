<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Validator;
use Illuminate\Validation\Rule;

class UserController extends BaseController
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){
        $this->model = $this->model->with([
            'user_position'
        ]); //lazy loader
    }

    public function index(Request $request)
    {
        $this->loadWithRelatedModel();
        $this->filterTable($request->all());
        return $this->getResultList($this->model->latest());
    }

    public function filterTable()
    { 
        $related_fields             = [];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name","first_name", "middle_name","last_name","email"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function store(Request $request)
    {
        $validator = $this->validateRequestInput(request()->all(), $this->model->requestRules());

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $user                   = $this->model;
            $user->user_position_id = $request->user_position_id;
            $user->first_name       = $request->first_name;	 
            $user->middle_name      = $request->middle_name;	 
            $user->last_name        = $request->last_name;	 
            $user->email            = $request->email;	 
            $user->password         = Hash::make($request->password); 
            $user->crypt            = Crypt::encrypt($request->password);
            $user->save();
            $message = 'User admin has been successfully created!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." has been added to user.";
            AuditTrailService::createAuditTrail('Added User', $description, null ,$new_data,$this->model->id,'App\User');
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
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." user has been ".strtolower($response).".";
            AuditTrailService::createAuditTrail($response.' User', $description, $old_data ,$new_data,$id,'App\User');
            return $this->archiveData($this->model, "user", $id);
        }

        $rules = $this->model->requestRules();
        $rules["email"] = $rules["email"] . ",email," . $id;
        $validator = $this->validateRequestInput(request()->all(), $rules);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $user                   = $this->model->find($id);
            $user->user_position_id = $request->user_position_id;
            $user->first_name       = $request->first_name;	 
            $user->middle_name      = $request->middle_name;	 
            $user->last_name        = $request->last_name;	 
            $user->email            = $request->email;	 
            $user->password         = Hash::make($request->password); 
            $user->crypt            = Crypt::encrypt($request->password);
            $user->save();
            $message = 'User admin has been successfully updated!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->first_name." ".$request->middle_name." ".$request->last_name." user has benn updated.";
            AuditTrailService::createAuditTrail('Update User', $description, $old_data ,$new_data,$id,'App\User');
        }

        return response($message, $code);
    
    }

    protected function AuthenticatedUserInfo(Request $request)
    {
        $user = $this->model->find(Auth::id());
        return response($user, 200);
    }
}
