<?php

namespace App\Http\Controllers\Uploads;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Exports\DiagnosisListExport;
use App\Imports\DiagnosisListImport;
use App\Http\Controllers\Uploads;
use Illuminate\Validation\Rule;
use App\Models\DiagnosisList;
use Illuminate\Http\Request;
use Validator;
use Excel;

class DiagnosisListUploadController extends Controller
{
    public function exportFail(Request $request)
    {
        return Excel::download(new fail_procedures(json_decode($request->input('data'))), 'Fail_Procedure_Template.xlsx');
    }

    public function export()
    {  
        return Excel::download(new DiagnosisListExport, 'Diagnosis_List_Template.xlsx');      
    }

    public function import(Request $request) 
    {
        $path = $request->file->storeAs('uploads',$request->file->getClientOriginalName());
        $import = new DiagnosisListImport();
        $array = $import->toArray($path);
        foreach ($array[0] as $key => $value) 
        {
            $status = '';
            $color = 'red';
            $parent_id = '';
            $covered_first_year = '';
            $covered_after_year = '';
            $exclusion = '';
            if($value['name'])
            {
                $chk = DiagnosisList::where('name',$value['name'])->first();
                if($chk)
                {
                    $status = 'Name already exist.';
                    $color = 'red';
                }
                else
                {
                    //die(var_dump($value["covered_first_year"]));

                    $covered_first_year = Self::checkIfValue($value['covered_first_year']);
                    $covered_after_year = Self::checkIfValue($value['covered_after_year']);
                    $exclusion = Self::checkIfValue($value['exclusion']);

                    if($covered_first_year == 'invalid' || $covered_after_year == 'invalid' || $exclusion == 'invalid')
                    {
                        $status = 'Your input in covered first year/covered after year/exclusion is invalid.';
                        $color = 'red';
                    }
                    else
                    {
                        //creation of diagnosis parent if not exist
                        if($value['parent_diagnosis'] != null)
                        {
                            $parent = DiagnosisList::where('name',$value['parent_diagnosis'])->first();
                            if($parent)
                            {
                                $parent_id = $parent->id;
                            }
                            else
                            {
                                $parent                     = new DiagnosisList();
                                $parent->parent_id          = null;
                                $parent->name               = $value['parent_diagnosis'];
                                $parent->description        = $value['description'];
                                $parent->level              = 0;
                                $parent->covered_first_year = 0;
                                $parent->covered_after_year = 0;
                                $parent->exclusion          = 0;
                                $parent->save();

                                $parent_id                  = $parent->id;
                            }
                        }
                        $diagnosisList                      = new DiagnosisList();
                        $diagnosisList->parent_id           = $parent_id ? $parent_id : null; 
                        $diagnosisList->name                = $value['name']; 
                        $diagnosisList->description         = $value['description'] ?? 'n/a'; 
                        $diagnosisList->level               = $parent_id ? $parent->level + 1 : 0;
                        $diagnosisList->covered_first_year  = $value['covered_first_year'] == 'yes' ? 1 : 0; 
                        $diagnosisList->covered_after_year  = $value['covered_after_year'] == 'yes' ? 1 : 0; 
                        $diagnosisList->exclusion           = $value['exclusion'] == 'yes' ? 1 : 0; 
                        $diagnosisList->save();

                        $status = "New Diagnosis Added.";
                        $color = 'green';
                    }
                }
            }
            else
            {
                $status = 'No Diagnosis List Name.';
                $color = 'red';
            }
            
            $array[0][$key]['status'] = $status;
            $array[0][$key]['color'] = $color;
        }
        return $array;
    }

    public static function checkIfValue($value)
    {
        if($value != null)
        {
           $importToLower =  strtolower($value);
           $value = $importToLower == 'yes' ||  $importToLower == 'no' ?  $importToLower : 'no'; 
        }
        else
        {
            $value = 'no';
        }

        return $value;
    }
}