<?php

namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Uploads;
use App\Http\Controllers\Controller;
use App\Exports\ProcedureExport;
use App\Exports\ProceduresExport;
use App\Imports\ProceduresImport;
use App\Exports\fail_procedures;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Procedure;
use App\Models\ProcedureType;
use Validator;
use Illuminate\Validation\Rule;

class ProcedureUploadController extends Controller
{
    public function exportFail(Request $request)
    {
        return Excel::download(new fail_procedures(json_decode($request->input('data'))), 'Fail_Procedure_Template.xlsx');
        //die(var_dump($request->all()));
        // $export = new ProcedureExport();
        // $filename = 'Procedure_Fail_Import.csv';       
        // header("Content-type: text/csv");       
        // header("Content-Disposition: attachment; filename=$filename");       
        // $output = fopen("php://output", "w");       
        // $header = $export->headings();       
        // fputcsv($output, $header);    
        // $failData = json_decode($request->fail_data);
        // // return $failData;
        // foreach ($failData as $key => $fail_data) 
        // {
        //     // return $fail_data;
        //     // fputcsv($output, $key+1);
        //     fputcsv($output, $fail_data);
        //     // fputcsv($output, $fail_data->rate_rvs);
        //     // fputcsv($output, $fail_data->tel_number);
        //     // fputcsv($output, $fail_data->contact_person);
        //     // fputcsv($output, $fail_data->contact_number);
        //     // fputcsv($output, $fail_data->email);
        //     // fputcsv($output, $fail_data->address);
        //     // fputcsv($output, $fail_data->payee);
        // }
        
        // //return Excel::download($output); 
        // fclose($output);  
        
    }

    public function export()
    {  
        return Excel::download(new ProcedureExport, 'Procedure_Template.xlsx');      
    }

    
    // public function export(Excel $excel, ProcedureExport $export)
    // {
    //     return 123;

    //     $export = new ProcedureExport();
    //     $filename = 'Provider_Template.csv';       
    //     header("Content-type: text/csv");       
    //     header("Content-Disposition: attachment; filename=$filename");       
    //     $output = fopen("php://output", "w");       
    //     $header = $export->headings();       
    //     fputcsv($output, $header);       
    //     fclose($output);       
    // }

    public function import(Request $request) 
    {

        $path = $request->file->storeAs('uploads',$request->file->getClientOriginalName());
        $import = new ProceduresImport();
        $array = $import->toArray($path);
        foreach ($array[0] as $key => $value) 
        {
            $remarks = '';
            $color = 'red';
            if($value['name'] && $value['procedure_type'])
            {
                $procedure = new Procedure();
                $procedure_type = new ProcedureType();
                $chk = $procedure->where('name',$value['name'])->first();
                if($chk)
                {
                    $chk_type = $procedure_type->where('name',$value['procedure_type'])->first();
                    if($chk_type)
                    {
                        $check_procedure_pertype = $procedure->where('name',$value['name'])->where('procedure_type_id',$chk_type->id)->first();
                        if($check_procedure_pertype)
                        {
                            $array[0][$key]['remarks'] = "Name already exist.";
                            $array[0][$key]['color'] = $color;
                            continue;
                        }
                    }
                }
                else
                {
                    $procedure_type = new ProcedureType();
                    $chk_type = $procedure_type->where('name',$value['procedure_type'])->first();
                    if($chk_type)
                    {
                        $chk_type_id = $chk_type->id;
                    }
                    else
                    {
                        $procedure_type->name = $value['procedure_type'];
                        $procedure_type->save();
                        $chk_type_id = $procedure_type->id;
                    }
                    $procedure->name                = $value['name']; 
                    $procedure->procedure_type_id   = $chk_type_id; 
                    $procedure->save();
                    
                    $array[0][$key]['remarks'] = 'Procedure Added.';
                    $array[0][$key]['color'] = 'green';
                    continue;
                }
            }
            else
            {
                $array[0][$key]['remarks'] = "No Procedure Name/Procedure Type.";
                $array[0][$key]['color'] = $color;
                continue;
            }
        }
        
        return $array;
    }
}