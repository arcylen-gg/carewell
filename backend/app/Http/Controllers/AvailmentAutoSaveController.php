<?php

namespace App\Http\Controllers;

use App\Models\AvailmentAutoSave;
use Illuminate\Http\Request;

class AvailmentAutoSaveController extends Controller
{

    public function store(Request $request)
    {
        $availment_auto_save = AvailmentAutoSave::updateOrCreate(
            [
                'user_id' => $request->user_id
            ],
            [
                'data' => json_encode($request->all())
            ]
        );

        $request['id'] = $availment_auto_save->id;
        return $request->all();
    }

    public function show($id)
    {
        return AvailmentAutoSave::find($id);
    }
}
