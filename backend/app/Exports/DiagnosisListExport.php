<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DiagnosisListExport implements FromView,ShouldAutoSize,WithEvents
{
    private $row_number = 1048576;

     public function view(): View
    {
        return view('exports.diagnosis_list_template');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $covered_first_year_column = 'D2:D'.$this->row_number;
                $data_validate = '"Yes,No"';
                self::forValidation($event,$covered_first_year_column,$data_validate);
                $covered_after_year_colum = 'E2:E'.$this->row_number;
                self::forValidation($event,$covered_after_year_colum,$data_validate);
                $exclusion_column = 'F2:F'.$this->row_number;
                self::forValidation($event,$exclusion_column,$data_validate);
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
}
