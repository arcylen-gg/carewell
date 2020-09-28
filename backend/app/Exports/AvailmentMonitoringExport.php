<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AvailmentMonitoringExport implements FromView,ShouldAutoSize,WithEvents
{
    public $data;
    public $year;
    public function __construct($data)
    {
        $monitoring_data = [];
        foreach ($data as $key => $value) {
            $arrayData = [];
            $arrayData['benefit_name'] = $value->name;
            $arrayData['no_of_patient'] = $value->no_of_patient;
            $arrayData['total_month'] = $value->total_month;
            $arrayData['total_patient'] = $value->total_patient;
            $arrayData['total_amount'] = $value->total_amount;
            array_push($monitoring_data, $arrayData);
            $this->year = $value->year_selected;
        }
        $this->data = $monitoring_data;
    }
    
    public function view(): View
    {
        $data['data'] = $this->data;
        $data['year'] = $this->year;
        return view('exports.reports.availment_monitoring_export_template', $data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $event->sheet->getDelegate()->mergeCells('A1:AA1');
                $event->sheet->getDelegate()->mergeCells('A2:AA2');
                $event->sheet->getDelegate()->mergeCells('A3:AA3');
                $event->sheet->getDelegate()->mergeCells('A4:AA4');
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    )
                );
                $event->sheet->getDelegate()->getStyle("A1:AA1")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A2:AA2")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A3:AA3")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A4:AA4")->applyFromArray($style);
                
                $alphas = range('B', 'Z');
                array_push($alphas, 'AA');

                for ($i = 0; $i < 26; $i++) {
                    if($i%2 == 0)
                    {
                        self::columnFormat($event, $alphas[$i].'6:'.$alphas[$i].'14', "number");
                    }
                    else
                    {
                        self::columnFormat($event, $alphas[$i].'6:'.$alphas[$i].'14', "amount");
                    }
                    self::setTotalFormulae($event, $alphas[$i].'14', $alphas[$i].'6:'.$alphas[$i].'13');
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