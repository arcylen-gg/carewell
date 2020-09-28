<?php
namespace App\Globals;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
class AuditTrailService
{
    public static function getDataForAuditTrail($model,$id)
    {
        $data = $model::find($id);
        return $data;
    }
    public static function createAuditTrail($remarks,$description,$old_data,$new_data,$sourceable_id,$sourceable_type)
    {
        AuditTrail::create([
            'user_id' => Auth::id(),
            'remarks' => $remarks,
            'description' => $description,
            'old_data' => $old_data,
            'new_data' => $new_data,
            'sourceable_id' => $sourceable_id,
            'sourceable_type' => $sourceable_type,
        ]);
    }
}