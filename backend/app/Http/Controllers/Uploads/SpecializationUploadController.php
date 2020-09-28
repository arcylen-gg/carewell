<?php

namespace App\Http\Controllers\Uploads;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Exports\SpecializationExport;
use Illuminate\Validation\Rule;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Validator;
use Excel;

class SpecializationUploadController extends Controller
{
    public function export(){
        return Excel::download(new SpecializationExport, 'Specialization_Template.xlsx');
    }

    public function import(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:specializations'
        ]);

        if($validator->fails())
        {
            $errors = $validator->errors();

            return Self::message('red', $errors->first('name'), $request->name);
        }
        else
        {
            Specialization::create([
                'name' => $request->name
            ]);

            return Self::message('green', 'SUCCESS', $request->name);
        }
    }

    public function message($color, $remarks, $name){
        return [
            'color' => $color,
            'remarks' => $remarks,
            'name' => $name
        ];
    }
}