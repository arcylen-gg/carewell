<?php

namespace App\Http\Controllers;

use App\Globals\AuditTrailService;
use App\Models\Provider;
use App\Models\ProviderPayee;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;


class ProviderController extends BaseController
{
    private $model;

    public function __construct(Provider $model)
    {
        $this->model = $model;
    }

    public function loadWithRelatedModel(){
        $this->model = $this->model->with([
            // 'provider_payee'
        ]); //lazy loader
    }

    public function show(Request $request, $id){
        $data = Provider::with(['provider_payee']);
        return $data->find($id);
    }

    public function index(Request $request)
    {
        $this->loadWithRelatedModel();
        $this->filterTable($request->all());
        return $this->getResultList($this->model->orderBy('name'));
    }

    public function filterTable()
    { 
        $related_fields             = [""];
        $inexact_fields             = ["name"]; 
        $search_inexact_fields      = [["name","name","contact_person","email","tel_number","contact_number"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields, 0);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields, 0);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields, 0);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     "name"    => "bail|required|unique:providers",
        //     "rate_rvs"         => "bail|required",
        // ]);

        $validator = $this->validateRequestInput(request()->all(), $this->model->requestRules());

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $provider                  = $this->model;
            $provider->name            = $request->name; 
            $provider->rate_rvs        = $request->rate_rvs; 
            $provider->tel_number      = $request->tel_number;
            $provider->contact_person  = $request->contact_person;
            $provider->contact_number  = $request->contact_number;
            $provider->email           = $request->email;
            $provider->address         = $request->address;
            $provider->save();

            foreach($request->list as $key => $value)
            {
                ProviderPayee::create([
                    'provider_id' => $provider->id,
                    'name' => $value['name'],
                ]);
            }
            
            $message = 'Provider has been successfully created!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$this->model->id);
            $description = $request->name." has been added to provider.";
            AuditTrailService::createAuditTrail('Added Provider', $description, null ,$new_data,$this->model->id,'App\Models\Provider');
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
            $description = $request->name." provider has been ".strtolower($response)."."; 
            AuditTrailService::createAuditTrail($response.' Provider', $description, $old_data ,$new_data,$id,'App\Models\Provider');
            return $this->archiveData($this->model, "provider", $id);
        }

        $update_tagged = $this->validateArchiveTagged($this->model, "provider_id", $id);

        if($update_tagged)
        {
            return response($update_tagged["message"], $update_tagged["code"]);
        }


        $rules = $this->model->requestRules();
        $rules["name"] = $rules["name"] . ",name," . $id;
        $validator = $this->validateRequestInput(request()->all(), $rules);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;
        }
        else
        {
            $provider                  = $this->model->find($id);
            $provider->name            = $request->name; 
            $provider->rate_rvs        = $request->rate_rvs; 
            $provider->tel_number      = $request->tel_number;
            $provider->contact_person  = $request->contact_person;
            $provider->contact_number  = $request->contact_number;
            $provider->email           = $request->email;
            $provider->address         = $request->address; 
            $provider->save();

            $delete = ProviderPayee::where('provider_id',$id)->delete();

            foreach($request->list as $key => $value)
            {
                ProviderPayee::create([
                    'provider_id' => $id,
                    'name' => $value['name'],
                ]);
            }

            $message = 'Provider has been successfully updated!';
            $code = 200;

            $new_data = AuditTrailService::getDataForAuditTrail($this->model,$id);
            $description = $request->name." provider has been updated.";
            AuditTrailService::createAuditTrail('Update Provider', $description, $old_data ,$new_data,$id,'App\Models\Provider');
        }

 
        return response($message, $code);
    
    }
}
