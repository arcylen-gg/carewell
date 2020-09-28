<?php

namespace App\Http\Controllers\Uploads;

use App\Http\Controllers\Controller;

use App\Exports\ProviderExport;
use App\Imports\ProviderImport;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Provider;
use App\Models\ProviderPayee;
use Validator;

class ProviderUploadController extends Controller
{
    public function exportFail(Request $request)
    {
        $export = new ProviderExport();
        $filename = 'Provider_Fail_Import.xlsx';       
        header("Content-type: text/xlsx");       
        header("Content-Disposition: attachment; filename=$filename");       
        $output = fopen("php://output", "w");       
        $header = $export->headings();       
        fputcsv($output, $header);    
        $failData = json_decode($request->fail_data);
        // return $failData;
        foreach ($failData as $key => $fail_data) 
        {
            // return $fail_data;
            // fputcsv($output, $key+1);
            fputcsv($output, $fail_data);
            // fputcsv($output, $fail_data->rate_rvs);
            // fputcsv($output, $fail_data->tel_number);
            // fputcsv($output, $fail_data->contact_person);
            // fputcsv($output, $fail_data->contact_number);
            // fputcsv($output, $fail_data->email);
            // fputcsv($output, $fail_data->address);
            // fputcsv($output, $fail_data->payee);
        }
        
        fclose($output);  

    }
    
    public function export()
    {
        return Excel::download(new ProviderExport, 'Provider_Template.xlsx');      
    }

    public function import(Request $request) 
    {
        $path = $request->file->storeAs('uploads',$request->file->getClientOriginalName());
        $import = new ProviderImport();
        $array = $import->toArray($path);
        
        foreach ($array[0] as $key => $value) 
        {
            $status = '';
            $color = 'red';

            if($value['name'] && $value['rate_rvs'])
            {
                if($value['rate_rvs'] == '2001' || $value['rate_rvs'] == '2009')
                {
                    $provider = new Provider();
                    $provider_payee = new ProviderPayee();
                    $chk = $provider->where('name',$value['name'])->first();
                    if($chk)
                    {
                        $status = "Provider Found and Payee Added.";
                        $chk_payee = $provider_payee->where('name',$value['payee'])->where('provider_id',$chk->id)->first();
                        if(!$chk_payee)
                        {
                            $provider_payee->provider_id = $chk->id;    
                            $provider_payee->name = $value['payee'];
                            $provider_payee->save();
                            $color = 'green';
                        }
                        else
                        {
                            $status = "Provider and Payee Already Exist.";
                        }
                    }
                    else
                    {
                        if($value['payee'])
                        { 
                            $provider->name            = $value['name']; 
                            $provider->rate_rvs        = $value['rate_rvs']; 
                            $provider->tel_number      = $value['tel_number'];
                            $provider->contact_person  = $value['contact_person'];
                            $provider->contact_number  = $value['contact_number'];
                            $provider->email           = $value['email'];
                            $provider->address         = $value['address'];
                            $provider->save();
    
                            $provider_payee->provider_id = $provider->id;    
                            $provider_payee->name = $value['payee'];
                            $provider_payee->save();
    
                            $status = "New Provider and Payee Added.";
                            $color = 'green';
                        }
                        else
                        {
                            $status = "Provider Has No Payee.";
                        }
                    }
                }
                else
                {
                    $status = "This Rate RVS is not accepted: ".$value['rate_rvs'];
                }
               
            }
            else
            {
                $status = "Check Provider and Rate RVS.";
            }
            
            $array[0][$key]['status'] = $status;
            $array[0][$key]['color'] = $color;
        }

        return $array;
    }
}