<?php

namespace App\Models\ModelActions\Member;

use App\Models\Member;

class MemberConfinement
{
    protected $_member;

    public function __construct(Member $member)
    {
        $this->_member = $member;
    }

    public function memberNumberOfConfinement($date)
    {
        
        $availment = collect($this->_member->availment)->pluck('id');

        $coverage_plan_benefits = [];
        $coverage_plan_benefits = collect($this->_member->company->coverage_plan->coverage_plan_benefits)
        ->map( function( $coveragePlanProcedure , $key)
        {
            return collect($coveragePlanProcedure->coverage_plan_procedures)->map( function($procedures, $proceduresKey) 
            {
                return $procedures->procedures->id;
            });
        });

        // $coverage_plan_benefits = collect($this->_member->company->coverage_plan->coverage_plan_benefits)->map( 
        //                             function( $coveragePlanProcedure , $key)
        //                             {
        //                                 return $coveragePlanProcedure->coverage_plan_procedures;
        //                             } 
        //                         );
        // $procedure = [];

        // $procedure = collect($coverage_plan_benefits)->map( 
        //     function( $procedure, $key)
        //     {
        //         return $procedure->procedures->id;
        //     }
        // );
        

        return $coverage_plan_benefits;

        


        // $availment = collect($this->_member->availment)->pluck('id');
        // $coverage_plan = [];
        // $coverage_plan = $this->_member->coverage_plan->coverage_plan_benefits[0]->coverage_plan_procedures;
        // foreach ($coverage_plan as $key => $value) {
        //     $coverage_plan[$key]->no_of_confinement = \DB::table('availment_procedures')->whereIn('availment_id',$availment)->where('procedures_id',$value['procedure_id'])->where('created_at','like','%'.$date.'%')->count(); 
        // }
        // return $coverage_plan;
    }
}