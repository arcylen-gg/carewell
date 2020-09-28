<?php

namespace App\Http\Controllers;

use App\Models\BenefitType;
use Illuminate\Http\Request;

class BenefitTypeController extends BaseController
{
    private $model;

    public function __construct(BenefitType $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        // $this->model = $this->model->with(['user_position_accesses']);
        return $this->getResultList($this->model);
    }
}
