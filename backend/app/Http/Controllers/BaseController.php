<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Globals\ExportService;
use App\Globals\InventoryService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

use Request;
use DB;
use App\Inventory;
use Validator;


class BaseController extends Controller
{
    protected $paginate_limit   = 35;
    protected $except_fields    = ["page", "limit", "created_at", "updated_at", "deleted_at","showAll"];

    public function __construct()
    {
    }

    public function response($data, $message = '', $code = 200)
    {
        $response = array(
            'data' => $data,
            'message' => $message,
            'status_code' => $code,
            'status' => true,
        );

        return response($response, $code);
    }

    protected function getResultList($model)
    {
        if (request()->has('showAll')) {
            if (request()->get('showAll') == '1') {
                $data = $model->get();
                return $this->response($data, 'Returned ' . $data->count() . ' results');
            }
        }

        if (request()->has('limit')) {
            $this->setPaginateLimit((int)request()->get('limit'));
        }

        $data = $model->paginate($this->paginate_limit)->appends(array_merge(request()->all(), ['limit' => $this->paginate_limit]));

        return $this->response($data, 'Returned ' . $data->count() . ' results');
    }

    public function setPaginateLimit($paginate_limit)
    {
        $this->paginate_limit = $paginate_limit;
    }

    protected function archiveData($model, $data, $id)
    {
        $parent = $model->find($id);

        $archive_tagged = $this->validateArchiveTagged($model, $data."_id", $id, $parent->is_archived);

        if($archive_tagged)
        {
            return $this->response($archive_tagged["message"], $archive_tagged["message"], $archive_tagged["code"]);
        }

        $validate_parent = $this->validateIfParent($model, $id);
        
        if($validate_parent)
        {
            return $this->response($validate_parent["message"], $validate_parent["message"], $validate_parent["code"]);
        }

        $model = $model->find($id);

        $status = $model->is_archived ? 0 : 1;

        $model = $model->fill([
            'is_archived'     =>  $status,
        ]);
        
        $model->save();
        
        $d          = str_replace(["_", "-", "+", "@"]," ", $data);
        $type       = $status == 1 ? 'archived' : 'restored';
        $message    = ucfirst($d) . " has been successfully " . $type ."!";
        $code       = 200;

        return $this->response($message, $code);
    }

    protected function searchTable($model, $inexact_fields = [], $archivable = 1)
    {
        foreach (request()->all() as $key => $value) 
        {
            foreach ($inexact_fields as $index => $val) 
            {
                if(in_array($key, $val))
                {
                    $model = $model->where(function ($query) use ($value, $val)
                    {
                        foreach ($val as $i => $v) 
                        {
                            if($i)
                            {
                                $query = $i == 1 ? $query->where($v, "like", "%".$value."%") : $query->orWhere($v, "like", "%".$value."%");
                            }
                        }
                    });
                }
            }
        }
        
        $model = $archivable && !request()->has('is_archived') ? $model->where("is_archived", 0) : $model;

        return $model;
    }

    protected function searchExactTable($model, $inexact_fields = [], $related_fields = [], $archivable = 1)
    {
        $this->except_fields = array_merge($this->except_fields, $related_fields);

        foreach (request()->all() as $key => $value) 
        {
            if(!in_array($key, $this->except_fields) && $value != "all")
            {
                if(!in_array($key, $inexact_fields))
                {
                    $model = $model->where($key, $value);
                }

            }
        }

        $model = $archivable && !request()->has('is_archived') ? $model->where("is_archived", 0) : $model;

        return $model;
    }

    protected function searchInRelatedTable($model, $related_fields = [], $archivable = 1)
    {
        foreach (request()->all() as $key => $value) 
        {
            if(in_array($key, $related_fields))
            { 
                $related_model  = strtok($key, '_');
                $column         = substr($key, strpos($key, "_") + 1); 
                
                $model = $model->whereHas($related_model, function ($query) use ($column, $value) {
                    $query->where($column, 'like', "%".$value."%");
                });
            }
        }
        
        $model = $archivable && !request()->has('is_archived') ? $model->where("is_archived", 0) : $model;

        return $model;
    }

    protected function searchDataDateRange($model, $type = "between")
    {
        if(request()->has('date_from'))
        {
            $model = $model->whereDate("created_at", $type == "between" ? ">=" : ">", request()->get('date_from'));
        }

        if(request()->has('date_to'))
        {
            $model = $model->whereDate("created_at", $type == "between" ? "<=" : "<", request()->get('date_to'));
        }

        return $model;
    }

    protected function uniquePerCompany($table, $store = false)
    {
        if($store)
            return Rule::unique($table)->where("company_id", request()->get('company_id'));
        else
            return Rule::unique($table)->ignore(request()->get('id'))->where("company_id", request()->get('company_id'));
    }

    protected function validateRouteAccess($code, $access)
    {
        $user = Auth::user();

        $data["message"]    = "Forbidden. You don't have permission to access on this page.";
        $data["code"]       = 300;

        $route = \App\Models\UserPositionAccess::where("user_position_id", $user->user_position_id)->where("code", $code)->first();

        if($route)
        {
            $accesses = unserialize($route->types);

            if(in_array($access, $accesses))
            {
                return false;
            }
        }

        return $data;
    }

    protected function validateRequestInput($input, $rules)
    {
        return Validator::make($input, $rules);
    }

    protected function validateArchiveTagged($model, $column, $id, $status = 0)
    {
        if(method_exists($model, 'requestTags') && count($model->requestTags()) && !$status)
        {
            foreach ($model->requestTags() as $key => $value) 
            {
                $archivable = Schema::hasColumn($value, 'is_archived');

                if($archivable)
                {
                    $exist = DB::table($value)->where($column, $id)->where("is_archived", 0)->first();
                }
                else
                {   
                    $exist = DB::table($value)->where($column, $id)->first();
                }

                if($exist)
                {
                    $data["message"]    = "You cannot archive/edit this data because it is still in use by other data.";
                    $data["code"]       = 300;
                    
                    return $data;
                }
            }
        }
        else if(method_exists($model, 'requestSelfTags') && count($model->requestSelfTags()) && $status)
        {
            $related = $model->find($id);

            foreach ($model->requestSelfTags() as $key => $value) 
            {
                $table  = $value."s";
                $col    = $value."_id";

                $archivable = Schema::hasColumn($table, 'is_archived');

                if($archivable)
                {
                    $exist = DB::table($table)->where("id", $related->$col)->where("is_archived", 0)->first();
                }
                else
                {   
                    $exist = DB::table($table)->where("id", $related->$col)->first();
                }

                if(!$exist)
                {
                    $data["message"]    = "You cannot restore this data because the parent of this data is inactive.";
                    $data["code"]       = 300;
                    
                    return $data;
                }
            }
        }
        
    }

    protected function validateIfParent($model, $id)
    {
        $data["message"]    = "You cannot archive/edit this data because it is a parent of a data.";
        $data["code"]       = 300;

        $p = head($model) ? head($model) : [];

        $parent = in_array("parent_id", $p);
        
        if(!$parent)
        {
            return false;
        }

        $children = $model->where("parent_id", $id)->first();

        if(!$children)
        {
            return false;
        }

        return $data;
    }

}
