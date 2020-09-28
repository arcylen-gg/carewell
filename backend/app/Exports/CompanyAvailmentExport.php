<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CompanyAvailmentExport implements FromView,ShouldAutoSize,WithEvents
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function view(): View
    {
        $data['data'] = $this->data;
        return view('exports.reports.company_availment_export_template', $data);
    }

    public function registerEvents(): array
    {
        return [];
    }
}