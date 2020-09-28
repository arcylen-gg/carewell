<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Crypt;
use Request;
use DB;
use Schema;

class TestController extends Controller
{
    public function test()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $chunk = $collection->forPage(2, 4);


        $data = \App\Models\Procedure::with(['procedure_type'])->get();

        dd(last($data->all()));

        dd(
            \App\Models\Procedure::search('A'),
            head(head($data)),
            data_get($data[0], 'procedure_type.name'),
            $data[0],
            $data,
            gettype($data)
        );
    }

}