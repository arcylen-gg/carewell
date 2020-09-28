<?php

namespace App\Http\Controllers\Reports\Interfaces;

interface GetReportDataInterface 
{
    public function index();
    public function exportToExcel();
    public function exportToPDF();
}