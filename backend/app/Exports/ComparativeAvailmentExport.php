<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ComparativeAvailmentExport implements FromView, ShouldAutoSize
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
        // dd($this->data);
    }
    public function view(): View
    {
        // $data = $this->data;
        // dd($data);
        return view('exports.reports.comparative_availment_export_template', $this->data);
    }
}
