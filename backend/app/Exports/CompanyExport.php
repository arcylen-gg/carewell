<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CompanyExport implements FromView,ShouldAutoSize,WithEvents
{
    private $company_name = [];
    private $companies = "";
    private $row_number = 1048576;

    public function view(): View
    {
        return view('exports.company_template'); 
    }

    public function __construct($data)
    {
        foreach($data as $key=>$value)
        {
            $company_name[$key] = $value['name'];
        }
        $this->companies = implode(',', $company_name);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $company_column = 'N2:N'.$this->row_number;
                self::forValidation($event,$company_column,'"'.$this->companies.'"');
                $account_type_column = 'D2:D'.$this->row_number;
                $account_type = '"Individual, Group, Corporate, Family"';
                self::forValidation($event, $account_type_column, $account_type);
                $policy_effective_column = 'G2:G'.$this->row_number;
                $event->sheet->getDelegate()->getStyle('G')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                self::forNotification($event,$policy_effective_column);
                $policy_expiry_column = 'H2:H'.$this->row_number;
                $event->sheet->getDelegate()->getStyle('H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                self::forNotification($event,$policy_expiry_column);
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
