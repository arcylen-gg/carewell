<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends BaseController
{

    private $model;

    public function __construct(Page $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $this->model = $this->model->with(['user_position_access']);
        return $this->getResultList($this->model);
    }
}