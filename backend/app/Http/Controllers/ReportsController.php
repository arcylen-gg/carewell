<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;
use App\Http\Controllers\Reports\ReportMainController;
use App\Globals\ReportService;

use Illuminate\Http\Request;
use DB;

class ReportsController extends Controller
{
    public function index($report_type,$export_type)
    {
        // $report_type must be the name of the class that is going to be used ex
        // use '-' to separate words
        // use PascalCase in naming classes

        // $export_type is how the data show (index, excel, pdf)

        $controller = new ReportMainController();
        return $controller->getData($report_type, $export_type);
    }

    public function companies()
    {
        $companies = ReportService::getCompanyAvailment('All Dates', 'availments', 'company_id');
        return $companies;
    }
}