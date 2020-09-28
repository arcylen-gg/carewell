<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class MemberExportation implements  FromView,ShouldAutoSize,WithEvents,WithColumnFormatting
{
    public $data;
    private $row_number = 1048576;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $data['data'] = $this->data;
        return view('exports.member_exportation',$data); 
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $gender_column = 'I2:I'.$this->row_number;
                $gender = '"Male,Female"';
                self::forValidation($event,$gender_column,$gender);
                $marital_status_column = 'J2:J'.$this->row_number;
                $marital_status = '"Single,Married,Divorced,Separated,Widowed"';
                self::forValidation($event,$marital_status_column,$marital_status);
                $payment_column = 'T2:T'.$this->row_number;
                $payment = '"Semi Monthly,Monthly,Quarterly,Semestral,Annually"';
                self::forValidation($event,$payment_column,$payment);
                $status_column = 'Z2:Z'.$this->row_number;
                $status = '"Active, Inactive"';
                self::forValidation($event,$status_column,$status);
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_TEXT
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
}
