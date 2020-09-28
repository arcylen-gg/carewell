<?php

namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\AuditTrailExport;
use Illuminate\Support\Facades\Storage;
use Excel;

class AuditTrailUploadController extends Controller
{
    public function export(Request $request) 
    {
        // return $request->all();
        $data['archive'] = $request->is_archived;
        $data['date_from'] = $request->date_from;
        $data['date_to'] = $request->date_to;

        if($request->file_type === "excel")
        {
            return (new AuditTrailExport($data))->download('audit_trail.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }
        else
        {
            return (new AuditTrailExport($data))->download('audit_trail.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
    }
}