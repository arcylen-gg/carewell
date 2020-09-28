<?php

namespace App\Http\Controllers;
use App\Models\MemberCompany;

use Illuminate\Http\Request;

class MemberCompanyController extends Controller
{
    private $model;

    public function __construct(MemberCompany $model)
    {
        $this->model = $model;
    }

    public function show($id){
        return $this->model->where('member_id',$id)->with(['company'])->get();
    }
}
