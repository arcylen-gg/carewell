<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SpecializationExport implements FromView,ShouldAutoSize
{
    public function view(): View
    {
        return view('exports.specialization_export_template');
    }
}