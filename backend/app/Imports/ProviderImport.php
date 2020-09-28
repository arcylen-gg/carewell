<?php

namespace App\Imports;

use App\Models\Provider;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

use Illuminate\Validation\Rule;


class ProviderImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation,SkipsOnError
{
    use Importable,SkipsErrors;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Provider([
            'name'            => $row['name'], 
            'rate_rvs'        => $row['rate_rvs'], 
            'tel_number'      => $row['tel_number'],
            'contact_person'  => $row['contact_person'],
            'contact_number'  => $row['contact_number'],
            'email'           => $row['email'],
            'address'         => $row['address'],
            'payee'         => $row['payee'],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:providers',
            'rate_rvs' => Rule::in(['2001']),
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
