<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AvailmentPerTypeExport implements FromView,ShouldAutoSize,WithEvents
{
    public $data;
    public $month;
    public $year;
    public $last_row;
    public function __construct($data)
    {
        $per_type_data = [];
        $count = 0;
        foreach ($data as $key => $value) {
            $arrayData = [];
            $arrayData['company_name'] = $value->name;
            $arrayData['patient_count'] = $value->patient_count;
            $arrayData['total_per_benefit'] = $value->total_per_benefit;
            $arrayData['total_patients']  = $value->total_patients;
            $arrayData['total_availments_amount'] = $value->total_availments_amount;
            array_push($per_type_data, $arrayData);
            if($value->month_select != 0)
            {
                $this->month = strtoupper($value->month_selected);
            }
            else
            {
                $this->month = "ALL MONTH";
            }
            $this->year = $value->year_selected;
            $count = $key;
        }
        $this->data = $per_type_data;
        $this->last_row = $count + 5;
    }
    
    public function view(): View
    {
        $data['data'] = $this->data;
        $data['month'] = $this->month;
        $data['year'] = $this->year;
        return view('exports.reports.availment_per_type_per_company_export_template', $data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $event->sheet->getDelegate()->mergeCells('A1:S1');
                $event->sheet->getDelegate()->mergeCells('A2:S2');
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    )
                );
                $event->sheet->getDelegate()->getStyle("A1:S1")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A2:S2")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A3:S3")->applyFromArray($style);
                
                $alphas = range('B', 'S');

                for ($i = 0; $i < 18; $i++) {
                    if($i%2 == 0)
                    {
                        self::columnFormat($event, $alphas[$i].'4:'.$alphas[$i].$this->last_row, "number");
                    }
                    else
                    {
                        self::columnFormat($event, $alphas[$i].'4:'.$alphas[$i].$this->last_row, "amount");
                    }
                    self::setTotalFormulae($event, $alphas[$i].$this->last_row, $alphas[$i].'4:'.$alphas[$i].($this->last_row - 1));
                }
            }
        ];
    }

    private function columnFormat($event, $column, $type)
    {
        if($type == "amount")
        {
            return $event->sheet->getDelegate()->getStyle($column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }
        return $event->sheet->getDelegate()->getStyle($column)->getNumberFormat()->setFormatCode('#,##0');
    }

    private function setTotalFormulae($event, $column, $sum_column)
    {
        $event->sheet->getDelegate()->setCellValue($column, '=SUM('.$sum_column.')');

        $styleArray = [
            'font' => [
                'bold' => true,
            ] 
        ];

        $event->sheet->getDelegate()->getStyle($column)->applyFromArray($styleArray);
    }
}