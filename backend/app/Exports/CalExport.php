<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CalExport implements FromView,ShouldAutoSize,WithEvents
{
    use Exportable;
    
    public $data;
    private $company;
    private $company_deployment;
    private $company_deployment_array;
    private $company_coverage;
    private $payment_mode;
    private $row_number = 1048576;

    public function __construct($data)
    {
        $this->data = self::filtering_all_member($data['member']);
        $this->company = $data['company'];

        $company_deploy = [];
        foreach($data['company']->company_deployment as $val)
        {
           array_push($company_deploy,$val->name);
        }
        $this->company_deployment_array = $company_deploy;
        $this->company_deployment = self::companyValidation($data['company']->company_deployment);
        $this->company_coverage = self::companyValidation($data['company']->coverage_plan);
        $this->payment_mode = self::paymentMode($data['payment_id']);
    }

    public function view(): View
    {
        $data['data'] = $this->data;
        return view('exports.cal_export_template',$data);
    }

    private function companyValidation($path)
    {
        $array = [];
        foreach($path as $key => $val)
        {
           array_push($array,$val->name);
        }
        $array_string = implode(",", $array);
        return $array_string;
    }
    
    private function setValue($event, $column, $insert)
    {
        $validation = $event->sheet->setCellValue($column, $insert);
    }

    private function filtering_all_member($member_data)
    {
        foreach ($member_data as $key => $value) {
            $value->monthly_premium = self::monthlyPremiumCalculation($value->monthly_premium, $value->payment_mode_id);
            $value->birth_date = $value->birth_date ? date_format(date_create($value->birth_date),"m/d/Y")  : '';
        }
        return $member_data;
    }

    private function monthlyPremiumCalculation($monthlyPremium, $paymentMode)
    {
        $calPayment = 0;
        if($paymentMode == 1)
        {
            $calPayment = $monthlyPremium / 2 ;
        }
        else if($paymentMode == 2)
        {
            $calPayment = $monthlyPremium;
        }
        else if($paymentMode == 3)
        {
            $calPayment = $monthlyPremium * 3;
        }
        else if($paymentMode == 4)
        {
            $calPayment = $monthlyPremium * 6;
        }
        else if($paymentMode == 5)
        {
            $calPayment = $monthlyPremium * 12;
        }
        return $calPayment;
    }
    
    private function paymentMode($id)
    {
        $payment = "";
        if($id == 1)
        { $payment = "Semi Monthly"; }
        
        if($id == 2)
        { $payment = "Monthly"; }

        if($id == 3)
        { $payment = "Quarterly"; }

        if($id == 4)
        { $payment = "Semestral"; }

        if($id == 5)
        { $payment = "Annually"; }
        return $payment;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $payment_column = 'G2:G'.$this->row_number;
                self::forValidation($event,$payment_column,'"'.$this->payment_mode.'"');
                $status_column = 'K2:K'.$this->row_number;
                $status = '"Active,Inactive"';
                self::forValidation($event,$status_column,$status);

                foreach ($this->company_deployment_array as $key => $deployment)
                {
                    $deploy_column = 'AZ' . ($key + 1);
                    $this->setValue($event, $deploy_column, $deployment);
                }
                $company_deploy_column = 'I2:I'.$this->row_number;
                self::forValidation($event,$company_deploy_column,'AZ1:AZ' . count($this->company_deployment_array));

                // $company_deploy_column = 'I2:I'.$this->row_number;
                // self::forValidation($event,$company_deploy_column,'"'.$this->company_deployment.'"');
                $coverage_column = 'F2:F'.$this->row_number;
                self::forValidation($event,$coverage_column,'"'.$this->company_coverage.'"');
                $birthdate_column = 'E2:E'.$this->row_number;
                $event->sheet->getDelegate()->getStyle('E')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                self::forNotification($event,$birthdate_column);
            }
        ];
    }

    private function forValidation($event, $column, $condition)
    {
        $validation = $event->sheet->getDataValidation($column);
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');
        $validation->setFormula1($condition);
    }

    private function forNotification($event, $column)
    {
        $validation = $event->sheet->getDataValidation($column);
        $validation->setShowInputMessage(true);
        $validation->setPromptTitle('Date Format');
        $validation->setPrompt('MM/dd/yyyy [US Date Format (e.g. 01/15/2001)]');
    }
}
