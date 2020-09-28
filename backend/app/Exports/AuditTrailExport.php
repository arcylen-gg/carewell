<?php

namespace App\Exports;

use App\Models\AuditTrail;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class AuditTrailExport implements FromView, ShouldAutoSize, WithHeadingRow, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    private $row_count = 0;

    public function __construct(Array $data)
    {
        $this->archive = $data['archive'];
        $this->date_from = isset($data['date_from']) ? $data['date_from'].' 00:00:00' : date('Y-m-d').' 00:00:00';
        $this->date_to = isset($data['date_to']) ? $data['date_to'].' 23:59:59' : date('Y-m-d').' 23:59:59';
    }

    public function view(): View
    {
        $query = AuditTrail::with('user')->where('is_archived',$this->archive)
                ->whereBetween('created_at',[$this->date_from, $this->date_to])->get();

        $this->row_count = $query->count() + 1;
        $data = ['query' => $query];

        return view( 'exports.audit_trail.audit_trail_list_export_template', $data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getPageSetup()->setHorizontalCentered(true);
                $event->sheet->getPageSetup()->setVerticalCentered(true);
                $event->sheet->getPageMargins()->setTop(0.75);
                $event->sheet->getPageMargins()->setRight(0.75);
                $event->sheet->getPageMargins()->setLeft(0.75);
                $event->sheet->getPageMargins()->setBottom(0.75);
                $event->sheet->getPageSetup()->setFitToWidth(1);

                $cellRange = 'A1:D1'; // All headers
                $dataRange = 'A2:D'.$this->row_count;

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ];
                
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle($dataRange)->applyFromArray($styleArray);
            },
           
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT
        ];
    }
}
