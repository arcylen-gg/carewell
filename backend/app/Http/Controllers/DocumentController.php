<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Globals\DocumentService;

use Illuminate\Http\Request;

class DocumentController extends BaseController
{
    private $model;

    public function __construct(Document $model)
    {
        $this->model = $model;
    }

    public function filterTable()
    {
        $related_fields             = [];
        $inexact_fields             = [];
        $search_inexact_fields      = [];

        $this->model = $this->searchExactTable($this->model, $inexact_fields, $related_fields);
        
        $this->model = $this->searchTable($this->model, $search_inexact_fields);

        $this->model = $this->searchInRelatedTable($this->model, $related_fields);

        $this->model = $this->searchDataDateRange($this->model, "between");
    }

    public function index(Request $request)
    {
        return $this->getResultList($this->model);
    }

    public function store(Request $request)
    {
        return DocumentService::upload();
    }

}
