<?php

namespace App\Models\ModelActions\Availment;

use App\Models\Availment;

class AvailmentActionID
{
    protected $_availment;

    public function __construct(Availment $availment)
    {
        $this->_availment = $availment;
    }
    private function addZeroes($id)
    {
        $length = strlen($id);
        $zero = "";
        for($i = $length; $i <= 4; $i++)
        {
            $zero .= "0";   
        }
        return $zero.$id;
    }
    public function generateApprovalId(array $availmentData)
    {
        $generated_Id = "";
        $last_id = $this->_availment->latest()->first();
        $id = $last_id ? $last_id->id + 1 : 1;
        $counter = $this->addZeroes($id);
        $code = "APP";
        $date = $availmentData['date_avail'] ? $availmentData['date_avail'] : date("md");
        $generated_Id = $code."-".$date."-".$counter;
        return $generated_Id;
    }
}