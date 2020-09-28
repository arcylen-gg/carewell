<?php

namespace App\Http\Controllers;

use App\Models\UserPosition;
use App\Models\UserPositionAccess;
use Illuminate\Http\Request;

class UserPositionController extends BaseController
{
    private $model;

    public function __construct(UserPosition $model)
    {
        $this->model = $model;
    }

    public function filterTable()
    {
        $related_fields             = ["user_positions_name"];
        $inexact_fields             = ["name"];
        $search_inexact_fields      = [["name", "name"]];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function index(Request $request)
    {
        $this->model = $this->model->with(['user_position_accesses']);
        $this->filterTable();
        return $this->getResultList($this->model->orderBy('name'));
    }

    public function store(Request $request)
    {
        $no_access = $this->validateRouteAccess("access-level", "create");

        if($no_access)
        {
            return response($no_access["message"], $no_access["code"]);
        }

        $validator = $this->validateRequestInput(request()->all(), $this->model->requestRules());

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;

            return response($message, $code);
        }

        $this->model = $this->model->create([
            'name' => $request->name
        ]);

        foreach (request()->get('user_position_accesses') as $key => $value) 
        {
            $this->model->user_position_accesses()->create([
                'page_id'   => $value["id"],
                'code'      => $value["code"],
                'types'     => serialize($value["types"]),
            ]);
        }

        $message = "User position has been successfully created!";
        $code = 200;

        return response($message, $code);
    }

    public function update(Request $request, $id)
    {

        if($request->for_archive)
        {
            $no_access = $this->validateRouteAccess("access-level", "archive");

            if($no_access)
            {
                return $this->response($no_access["message"], $no_access["message"], $no_access["code"]);
            }

            return $this->archiveData($this->model, "user_position", $id);
        }

        $no_access = $this->validateRouteAccess("access-level", "edit");

        if($no_access)
        {
            return response($no_access["message"], $no_access["code"]);
        }

        $rules = $this->model->requestRules();
        $rules["name"] = $rules["name"] . ",name," . $id;
        $validator = $this->validateRequestInput(request()->all(), $rules);

        if($validator->fails())
        {
            $message = $validator->errors()->first();
            $code = 300;

            return response($message, $code);
        }

        $update_tagged = $this->validateArchiveTagged($this->model, "user_position_id", $id);

        if($update_tagged)
        {
            return response($update_tagged["message"], $update_tagged["code"]);
        }

        $this->model = $this->model->find($id);

        $this->model = $this->model->fill([
            'name' => $request->name
        ]);

        $this->model->save();

        foreach (request()->get('user_position_accesses') as $key => $value) 
        {
            $access = UserPositionAccess::find($value["id"]);

            if(gettype($value["types"]) == "array")
            {
                $access = $access->fill([
                    'page_id'   => $value["page_id"],
                    'code'      => $value["code"],
                    'types'     => serialize($value["types"])
                ]);
                $access->save();
            }
        }

        $message = "User position has been successfully updated!";
        $code = 200;

        return response($message, $code);
    }

    
}
