<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MemberExport implements FromView,ShouldAutoSize,WithEvents
{
    private $company;
    private $company_name;
    private $company_deployment;
    private $company_deployment_array;
    private $company_coverage;
    private $row_number = 1048576;

    public function view(): View
    {
        return view('exports.member_template'); 
    }

    public function __construct($data)
    {
        $this->company = $data;
        $this->company_name = $data->name;
        $company_deploy = [];
        $coverage = [];
        foreach($data->company_deployment as $val)
        {
           array_push($company_deploy,$val->name);
        }
        $this->company_deployment_array = $company_deploy;
        $this->company_deployment = implode(",", $company_deploy);
        foreach($data->coverage_plan as $val)
        {
           array_push($coverage,$val->name);
        }
        $this->company_coverage = implode(",", $coverage);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $gender_column = 'H2:H'.$this->row_number;
                $gender = '"MALE,FEMALE"';
                self::forValidation($event,$gender_column,$gender);
                $marital_status_column = 'I2:I'.$this->row_number;
                $marital_status = '"Single,Married,Divorced,Separated,Widowed"';
                self::forValidation($event,$marital_status_column,$marital_status);
                $payment_column = 'P2:P'.$this->row_number;
                $payment = '"Semi Monthly,Monthly,Quarterly,Semestral,Annually"';
                self::forValidation($event,$payment_column,$payment);
                $status_column = 'W2:W'.$this->row_number;
                $status = '"Active,Inactive"';
                self::forValidation($event,$status_column,$status);
                $company_column = 'N2:N'.$this->row_number;
                self::forValidation($event,$company_column,'"'.$this->company_name.'"');

                foreach ($this->company_deployment_array as $key => $deployment)
                {
                    $deploy_column = 'AZ' . ($key + 1);
                    $this->setValue($event, $deploy_column, $deployment);
                }
                $company_deploy_column = 'O2:O'.$this->row_number;
                self::forValidation($event,$company_deploy_column,'AZ1:AZ' . count($this->company_deployment_array));

                $coverage_column = 'Q2:Q'.$this->row_number;
                self::forValidation($event,$coverage_column,'"'.$this->company_coverage.'"');
                $birthdate_column = 'G2:G'.$this->row_number;
                $event->sheet->getDelegate()->getStyle('G')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                self::forNotification($event,$birthdate_column);
                
                
            }
        ];
    }

    private function setValue($event, $column, $insert)
    {
        $validation = $event->sheet->setCellValue($column, $insert);
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