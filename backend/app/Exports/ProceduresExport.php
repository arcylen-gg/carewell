<?php

namespace App\Exports;

use App\Models\Procedure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProceduresExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return [
            '#',
            'procedure_type',
            'name',
        ];
    }
    public function collection()
    {
        return Procedure::all();
    }
}
