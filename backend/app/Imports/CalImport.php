<?php

namespace App\Imports;

use Illuminate\Validation\Rule;

use App\Models\CalMember;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class CalImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation,SkipsOnError
{
    use Importable,SkipsErrors;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
    	// /die(var_dump($row));
        // return new CalMember([
        //     'compn'            => $row['name'], 
        //     'procedure_type'  => $row['procedure_type'], 
            
        // ]);
    }
    public function rules(): array
    {
        return [
            'name' => 'required|unique:procedures',
        ];
    }


    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
