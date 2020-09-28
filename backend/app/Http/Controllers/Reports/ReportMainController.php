<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Reports\Interfaces\GetReportDataInterface;

use Illuminate\Http\Request;

class ReportMainController
{
    public function getData($report_type, $export_type){
        // $class = class name that is going to be used and instantiate
        $class = $this->setClassInstantiate($report_type);

        if(class_exists($class))
        {
            switch ($export_type) 
            {
                case 'excel':
                    return $this->setClassInterfaceExcel(new $class);
                break;

                case 'pdf':
                    return $this->setClassInterfacePDF(new $class);
                break;
                
                default:
                    // index
                    return $this->setClassInterface(new $class);
                break;
            }
        }
        else
        {
            return 'Class not found.';
        }
    }

    public function setClassInstantiate($report_type)
    {
        $report_type = explode('-',$report_type);
        foreach ($report_type as $key => &$value) {
            $value = ucfirst($value);
        }

        // all classes related to reports must be on this folder
        // App\Http\Controllers\Reports\
        $report_type = "App\\Http\\Controllers\\Reports\\".join($report_type);

        return $report_type;
    }

    public function setClassInterface(GetReportDataInterface $interface)
    {
        // $interface = class that is going to be interfaced.
        // $interface must have all the function inside GetReportDataInterface

        // if their is an error check if GetReportDataInterface and the $interface class
        // have the same function or methods
        return $interface->index();
    }

    public function setClassInterfaceExcel(GetReportDataInterface $interface)
    {
        // $interface = class that is going to be interfaced.
        // $interface must have all the function inside GetReportDataInterface
        
        // if their is an error check if GetReportDataInterface and the $interface class
        // have the same function or methods
        return $interface->exportToExcel();
    }

    public function setClassInterfacePDF(GetReportDataInterface $interface)
    {
        // $interface = class that is going to be interfaced.
        // $interface must have all the function inside GetReportDataInterface
        
        // if their is an error check if GetReportDataInterface and the $interface class
        // have the same function or methods
        return $interface->exportToPDF();
    }
}