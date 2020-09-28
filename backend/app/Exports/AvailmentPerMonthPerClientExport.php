<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AvailmentPerMonthPerClientExport implements FromView,ShouldAutoSize,WithEvents
{
    public $data;
    public $overall_monthly_count;
    public $overall_monthly_total;
    public $overall_grand_total;
    public $year;
    public $last_row;
    public function __construct($data)
    {
        $company_array = [];
        $company_array['company'] = [];
        $count = 0;
        foreach ($data['item'] as $key => $value) {
            array_push($company_array['company'], $value);
            $count = $key;
        }
        $this->overall_monthly_count = $data['overall_monthly_count'];
        $this->overall_monthly_total = $data['overall_monthly_total'];
        $this->overall_grand_total = $data['overall_grand_total'];
        $this->data = $company_array['company'];
        $this->year = $data['year_selected'];
        $this->last_row = $count + 5;
    }
    
    public function view(): View
    {
        $data['data'] = $this->data;
        $data['year'] = $this->year;
        return view('exports.reports.availment_per_month_per_client_export_template', $data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class =>function(AfterSheet $event){
                $event->sheet->getDelegate()->mergeCells('A1:Z1');
                $event->sheet->getDelegate()->mergeCells('A2:Z2');
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    )
                );
                $event->sheet->getDelegate()->getStyle("A1:Z1")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A2:Z2")->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle("A3:Z3")->applyFromArray($style);
                
                $alphas = range('B', 'Z');

                for ($i = 0; $i < 25; $i++) {
                    if($i%2 == 0 && $i != 24)
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