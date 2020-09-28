<?php

namespace App\Models\ModelActions\Member;

use App\Models\Member;

class MemberActionID
{
    protected $_member;

    public function __construct(Member $member)
    {
        $this->_member = $member;
    }
    
    private function firstCharacter($data)
    {
        $data = strtoupper(substr($data,0,1));
        return $data;
    }

    private function addZeroes($id)
    {
        $length = strlen($id);
        $zero = "";
        for($i = $length; $i <= 3; $i++)
        {
            $zero .= "0";   
        }
        return $zero.$id;
    }

    public function generateCarewellId(array $memberData)
    {
        $generated_Id = "";
        $member = new Member();
        $last_id = $member->latest()->first();
        $id = $last_id ? $last_id->id + 1 : 1;
        $id = isset($memberData['member_id']) ? $memberData['member_id'] : $id;
        $counter = $this->addZeroes($id);
        $code = $memberData['company_code']  ? strtoupper($memberData['company_code']) : "CAREWELL";
        $date = $memberData['billing_date'] ? $memberData['billing_date'] : date("mdy");
        $generated_Id = $code."-".$date."-".$counter;
        return $generated_Id;
    }

    public function generateUniversalId(array $memberData)
    {
        $generated_Id = "";
        if(!$memberData['universal_id'])
        {
            $member = new Member();
            $last_id = $member->latest()->first();
            // $last_id = $this->_member->latest()->first();
            $id = $last_id ? $last_id->id + 1 : 1;
            $id = isset($memberData['member_id']) ? $memberData['member_id'] : $id;
            $first_name = $this->firstCharacter($memberData['first_name']);
            $middle_name = $memberData['middle_name'] ? $this->firstCharacter($memberData['middle_name']) : null;
            $last_name = $this->firstCharacter($memberData['last_name']);
            $birthdate = $memberData['birth_date'] ? preg_replace('/[^A-Za-z0-9\-]/', '', $memberData['birth_date']) : date("mdy");
            $counter = $this->addZeroes($id);

            $generated_Id = $first_name.$middle_name.$last_name."-".$birthdate."-".$counter;
        }
        else
        {
            $generated_Id = $memberData['universal_id'];
        }
        
        return $generated_Id;
    }

    public function memberNumberOfConfinement($date)
    {
        $availment = collect($this->_member->availment)->pluck('id');
        $coverage_plan = [];
        $coverage_plan = $this->_member->coverage_plan->coverage_plan_benefits[0]->coverage_plan_procedures;
        foreach ($coverage_plan as $key => $value) {
            $coverage_plan[$key]->no_of_confinement = \DB::table('availment_procedures')->whereIn('availment_id',$availment)->where('procedures_id',$value['procedure_id'])->where('created_at','like','%'.$date.'%')->count(); 
        }
        return $coverage_plan;
    }
}