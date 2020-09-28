<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AvailmentExport implements FromView,ShouldAutoSize,WithEvents
{
    private $coverage_name = "";
    private $companies = "";
    private $benefits = "";
    private $deployment_name = "";
    private $row_number = 1048576;

    public function view(): View
    {
        return view('exports.availment.availment_template'); 
    }

    public function __construct($data)
    {
        $array = [];
        $deployment_array = [];
        foreach($data['benefit'] as $key => $val)
        {
           array_push($array,$val->name);
        }
        foreach ($data['deployment'] as $key => $value) {
            array_push($deployment_array, $value->name);
        }
        $array_string = implode(",", $array);
        $this->coverage_name = implode(",", $data['coverage_plan']);
        $this->benefits = $array_string;
        $this->deployment_name = implode(",",$deployment_array);
        $this->companies = $data['company']->name;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $approval_title = 'Approval Format';
                $approval_message = 'APP-mmdd-availment number [Approval Format (e.g APP-0101-00015)]';
                $date_title = 'Date Format';
                $date_message = 'MM/dd/yyyy [US Date Format (e.g. 01/15/2001)]';

                $approval_column = 'A2:A'.$this->row_number;
                self::forNotification($event, $approval_column, $approval_title, $approval_message);
                
                $benefit_column  = 'O2:O'.$this->row_number;
                self::forValidation($event,$benefit_column,'"'.$this->benefits.'"');
                
                $coverage_column  = 'M2:M'.$this->row_number;
                self::forValidation($event,$coverage_column,'"'.$this->coverage_name.'"');
                
                $disapproved_column = 'AD2:AD'.$this->row_number;
                $disapprove = '"Yes,No"';
                self::forValidation($event,$disapproved_column,$disapprove);
                
                $rvs_column = 'AG2:AG'.$this->row_number;
                $rate_rvs = '"2001,2009"';
                self::forValidation($event,$rvs_column,$rate_rvs);
                
                $company_column = 'K2:K'.$this->row_number;
                self::forValidation($event,$company_column,'"'.$this->companies.'"');

                $deployment_column = 'L2:L'.$this->row_number;
                self::forValidation($event,$deployment_column,'"'.$this->deployment_name.'"');

                $caller_date_column = 'E2:E'.$this->row_number;
                $event->sheet->getDelegate()->getStyle('E')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                self::forNotification($event,$caller_date_column, $date_title, $date_message);
                
                $availment_date_column = 'P2:P'.$this->row_number;
                $event->sheet->getDelegate()->getStyle('P')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                self::forNotification($event,$availment_date_column, $date_title, $date_message);

                $discharged_date_column = 'Q2:Q'.$this->row_number;
                $event->sheet->getDelegate()->getStyle('Q')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                self::forNotification($event,$discharged_date_column, $date_title, $date_message);
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

    private function forNotification($event, $column, $message_title, $message)
    {
        $validation = $event->sheet->getDataValidation($column);
        $validation->setShowInputMessage(true);
        $validation->setPromptTitle($message_title);
        $validation->setPrompt($message);
    }
}