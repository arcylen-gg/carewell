<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AvailmentSummaryExport implements FromView,ShouldAutoSize,WithEvents
{
    public $data;
    public $year;
    public $last_row;
    public function __construct($data)
    {
        $summary_data = [];
        $count = 0;
        foreach ($data as $key => $value) {
            $arrayData = [];
            $arrayData['company_name'] = $value->name;
            $arrayData['no_of_availments'] = $value->no_of_availments;
            $arrayData['total_availments'] = $value->total_availments;
            array_push($summary_data, $arrayData);
            $this->year = $value->year_selected;
            $count = $key;
        }
        $this->data = $summary_data;
        $this->last_row = $count + 5;
    }
    
    public function view(): View
    {
        $data['data'] = $this->data;
        $data['year'] = $this->year;
        return view('exports.reports.availment_summary_export_template', $data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $event->sheet->getDelegate()->mergeCells('A1:N1');
                $event->sheet->getDelegate()->mergeCells('A2:N2');
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    )
                );
                $event->sheet->getDelegate()->getStyle("A1:N1")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A2:N2")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A3:N3")->applyFromArray($style);
                
                $alphas = range('B', 'N');

                for ($i = 0; $i < 13; $i++) {
                    self::columnFormat($event, $alphas[$i].'4:'.$alphas[$i].$this->last_row, "number");
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