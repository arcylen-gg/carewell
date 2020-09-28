import { availmentData } from '../mixins/AvailmentStore.js';
import callerInformation from '../page/components/CallerInformation';
import memberInformation from '../page/components/MemberInformation';
import availmentInformation from '../page/components/AvailmentInformation';
import procedureInformation from '../page/components/ProcedureInformation';
import physicianInformation from '../page/components/PhysicianInformation';
import payeeInformation from '../page/components/PayeeInformation';
export default{
    components:{
        callerInformation,
        memberInformation,
        availmentInformation,
        procedureInformation,
        physicianInformation,
        payeeInformation,
    },
    methods : {
        validateInnerLimit(data){
            if(data.procedure)
            {
                let procedure = data.procedure
                let procedure_amount = procedure ? +procedure.amount : 0
                let carewell = +data.procedures_carewell_charge
                let availmentRecords = availmentData.getters.getSelectedMemberAvailment;
                let amountAvail = 0;

                availmentRecords.map(item => {
                    item.availment_procedure.map(info => {
                        if(info.procedures_id == procedure.procedure_id)
                        {
                            amountAvail += info.carewell_charged;
                        }
                    });
                });
                let allTotal = carewell + amountAvail;
                let innerBalance = procedure_amount != 0 ? procedure_amount - amountAvail : 0;
                console.log(procedure_amount,amountAvail, carewell,allTotal)
                if(procedure_amount < allTotal && procedure_amount != 0)
                {
                    this.toaster('The remaining balance for '+procedure.procedures.name+' is '+
                        this.currency_format('',innerBalance)+'. The member already used '+
                        this.currency_format('',amountAvail)+' out of '+
                        this.currency_format('',procedure_amount)+' and the amount for this procedure is '+
                        this.currency_format('',carewell));
                    return false;
                }
                return true;
            }
        },
        validateProcedure(){
            let bool = true;
            if(!this.validateLimits()) 
            {
                bool = false
            }
            else
            {
                let procedureRecord = this.$refs.procedureInformation.procedure_list;
                procedureRecord.every((data,key)=>{
                    if(data.procedures_gross_amt === 0)
                    {
                        this.toaster("No Gross Amount.")
                        bool = false;
                        return bool;
                    }
                    else
                    {
                        let gross_amount = parseFloat(data.procedures_gross_amt)
                        let phic_amount = parseFloat(data.procedures_phic_amt)
                        let patient_charge = parseFloat(data.procedures_patient_charge)
                        let carewell = parseFloat(data.procedures_carewell_charge)
                        let other_charge = parseFloat(data.procedures_other_charge)
                        let total = +phic_amount + +patient_charge + +carewell + +other_charge
                        if(gross_amount < total)
                        {
                            this.toaster(`Cannot be greater than Gross Amount.`, 400);
                            bool = false;
                        }
                        else if (gross_amount > total)
                        {
                            this.toaster(`Cannot be less than Gross Amount.`, 400);
                            bool = false;
                        }
                        else
                        {
                            bool = this.validateInnerLimit(data)
                        }
                        return bool;
                    }
                })
                console.log(bool)
                if(bool)
                {
                    let procedureList = availmentData.state.form.getSelectedMemberBenefitType;
                    let amountCovered = procedureList.covered_amount;
                    let benefitType = procedureList.benefit_type_id;
                    let current_balance = this.validateMaxLimitPer();
                    let amountConfinement = procedureList.per_confinement_amount;
                    let amountCarewell = parseFloat(this.$refs.procedureInformation.getSumProcedureCarewell);
                    //validate balance
                    console.log(current_balance)
                    bool = this.validateBalance(benefitType,amountCarewell,amountCovered,amountConfinement,current_balance);
                }
            }
            return bool
        },
        validatePhysician(){
            let bool = true;
            if(!this.validateLimits())
            {
                bool = false
            }
            else
            {
                let physicianRecord = this.$refs.physicianInformation.physician_list;
                physicianRecord.every((arr,ind)=>{
                    if(arr.doctor_fee == 0)
                    {
                        this.toaster("No Gross Amount.")
                        bool = false;
                    }
                    else
                    { 
                        bool = this.$refs.physicianInformation.physicianListComputation(ind);
                    }
                })

                let physicianList = availmentData.state.form.getSelectedMemberBenefitType;
                let amountCovered = physicianList.covered_amount;
                let benefitType = physicianList.benefit_type_id;
                let amountConfinement = physicianList.per_confinement_amount;
                let current_balance = this.validateMaxLimitPer();
                console.log(current_balance)
                let amountCarewell = parseFloat(this.$refs.physicianInformation.getSumPhysicianCarewell);
                //validate balance
                console.log(current_balance)
                bool = this.validateBalance(benefitType,amountCarewell,amountCovered,amountConfinement,current_balance);
            }
            return bool
        },
        validateBothTable(){
            let bool = true;
            if(!this.validateLimits())
            {
                bool = false;
            }
            else
            {
                let procedureRecord = this.$refs.procedureInformation.procedure_list;
                let physicianRecord = this.$refs.physicianInformation.physician_list;
                
                physicianRecord.every((arr,ind)=>{
                    if(arr.doctor_fee == 0)
                    {
                        this.toaster("No Gross Amount.")
                        bool = false;
                        return bool;
                    }
                    else
                    { 
                        bool = this.$refs.physicianInformation.physicianListComputation(ind)
                        return bool;
                    }
                })

                procedureRecord.every((data,key)=>{
                    if(data.procedures_gross_amt === 0)
                    {
                        this.toaster("No Gross Amount.")
                        bool = false;
                        return bool;
                    }
                    else
                    {
                        let gross_amount = parseFloat(data.procedures_gross_amt)
                        let phic_amount = parseFloat(data.procedures_phic_amt)
                        let patient_charge = parseFloat(data.procedures_patient_charge)
                        let carewell = parseFloat(data.procedures_carewell_charge)
                        let other_charge = parseFloat(data.procedures_other_charge)
                        let total = +phic_amount + +patient_charge + +carewell + +other_charge
                        if(gross_amount < total)
                        {
                            this.toaster(`Cannot be greater than Gross Amount.`, 400);
                            bool = false;
                        }
                        else if (gross_amount > total)
                        {
                            this.toaster(`Cannot be less than Gross Amount.`, 400);
                            bool = false;
                        }
                        else
                        {
                            bool = this.validateInnerLimit(data)
                        }
                        return bool;
                    }
                })
                
                let benefitTypeList = availmentData.state.form.getSelectedMemberBenefitType; 
                let amountCovered = benefitTypeList.covered_amount;
                let benefitType = benefitTypeList.benefit_type_id;
                let amountConfinement = benefitTypeList.per_confinement_amount;
                let current_balance = this.validateMaxLimitPer();
                let grand_total = parseFloat(this.$refs.payeeInformation.getGrandTotal);
                //then validate balance
                console.log(current_balance)
                bool = this.validateBalance(benefitType,grand_total,amountCovered,amountConfinement,current_balance);
            }
            return bool
        },
        validateLimits(){

            let months = 0;
            let years = 0;
            let bool = true;
            let monthly_limit = this.$refs.availmentInformation.form.benefitType.limit_per_month;
            let yearly_limit = this.$refs.availmentInformation.form.benefitType.limit_per_year;
            let member_availment = availmentData.getters.getSelectedMemberAvailment;
            let location = window.location.pathname === '/availment-center/create' ? true : false;
            let dateSelected = new Date(this.$refs.availmentInformation.form.date_avail);
            let benefitSelected = this.$refs.availmentInformation.form.benefitType.benefit_type_id;
            let month_selected = ('0' + (dateSelected.getMonth() + 1)).slice(-2)+'-'+(dateSelected.getFullYear());
            let year_selected = dateSelected.getFullYear();
            
            if(location && member_availment.length !== 0)
            {
                member_availment.forEach((arr,ind) => {
                    if(arr.benefit_type_id === benefitSelected)
                    {
                        let dateAvail = new Date(arr.date_avail);
                        let month_availed = ('0' + (dateAvail.getMonth() + 1)).slice(-2)+'-'+(dateAvail.getFullYear());
                        let year_availed = dateAvail.getFullYear();
                        if(month_availed === month_selected)
                        {
                            months += 1;
                        }

                        if(year_selected === year_availed)
                        {
                            years += 1; 
                        }
                    }
                });
            }

            if(location)
            {
                if(yearly_limit !== 0 && yearly_limit > monthly_limit)
                {
                    if(years >= yearly_limit)
                    {
                        this.toaster("The member already reach the limit of availment per year.");
                        bool = false;
                    }

                    if(months >= monthly_limit && monthly_limit !== 0)
                    {
                        this.toaster("The member already reach the limit of availment per month.");
                        bool = false;
                    }
                }
                else
                {
                    if(monthly_limit !== 0 && months >= monthly_limit)
                    {
                        this.toaster("The member already reach the limit of availment per month.");
                        bool = false;
                    }
                }

                bool = this.validateIBR(benefitSelected);
                console.log(bool)
            }
            
            return bool
        },
        validateMaxLimitPer(){
            let benefit_id = this.$refs.availmentInformation.form.benefitType.benefit_type_id;
            let diagnosis_id = this.$refs.availmentInformation.form.diagnosis_id;
            let member_availment = availmentData.state.form.getMemberAvailmentByBenefit;
            let location = window.location.pathname === '/availment-center/create' ? true : false;
            let maximum_benefit_limit = availmentData.getters.maximumBenefitLimit;
            let remaining_balance = availmentData.state.form.remaining_balance;
            let amount_covered = availmentData.state.form.amount_covered;
            let limitation = availmentData.state.form.getSelectedMember.company.coverage_plan.max_limit_per;
            let dateSelected = new Date(this.$refs.availmentInformation.form.date_avail);
            let year_selected = dateSelected.getFullYear();
            let availmentAmount = 0;
            let current_balance = 0;
            let current_amount = 0;
            let yearly = '';

            if(location && member_availment.length !== 0)
            {
                if(limitation == "1")
                {
                    member_availment.forEach((arr,ind)=>{
                        availmentAmount += arr.grand_total;
                        let dateAvail = new Date(arr.date_avail);
                        yearly = dateAvail.getFullYear();
                    })
                }
                else
                {
                    member_availment.forEach((arr,ind)=>{
                        if(arr.diagnosis_id == diagnosis_id)
                        {
                            availmentAmount += arr.grand_total;
                            let dateAvail = new Date(arr.date_avail);
                            yearly = dateAvail.getFullYear();
                        }
                    })
                }
            }
            else
            {
                let dateAvail = new Date(this.$refs.availmentInformation.form.date_avail);
                yearly = dateAvail.getFullYear();
            }
            //current amount
            if(maximum_benefit_limit == amount_covered && year_selected == yearly)
            {
                current_amount = remaining_balance;
            }
            else if((amount_covered > remaining_balance && remaining_balance < availmentAmount) && year_selected == yearly)
            {
                current_amount = amount_covered;
            }
            else if((amount_covered > remaining_balance && remaining_balance > availmentAmount) && year_selected == yearly)
            {
                current_amount = remaining_balance;
            }
            else
            {
                current_amount = amount_covered;
            }
            //current balance
            if(limitation == "1")
            {
                if(year_selected == yearly)
                {
                    current_balance = current_amount - availmentAmount;
                }
                else if(year_selected != yearly)
                {
                    current_balance = current_amount
                }
                else
                {
                    current_balance = 0;
                }
            }
            else if(limitation == "2")
            {
                if(benefit_id != null)
                {
                    current_balance = current_amount - availmentAmount;
                }
                else
                {
                    current_balance = current_amount
                }
            }
            else 
            {
                current_balance = 0;
            }
            console.log(current_amount, availmentAmount, current_balance)
            return current_balance;
        },
        validateBalance(benefitType, amountCarewell, amountCovered, amountConfinement, currentBalance){
            let bool = true;
            if(benefitType != 7)
            {
                if(amountCarewell > amountCovered)
                {
                    this.toaster('The Amount Covered to be Charged to Carewell must not exceed to '+this.currency_format('',amountCovered)+'!');
                    bool = false;
                }

                if(amountCarewell > currentBalance)
                {
                    this.toaster('The total amount exceed to the balance of the member, The current balance is '+this.currency_format('',currentBalance)+'.');
                    bool = false;
                }
            }
            else
            {
                if(amountCarewell > amountConfinement && amountConfinement != 0)
                {
                    this.toaster('The Per Confinement to be Charged to Carewell must not exceed to '+this.currency_format('',amountConfinement)+'!');
                    bool = false;
                }

                if(amountCarewell > amountCovered)
                {
                    this.toaster('The Amount Covered to be Charged to Carewell must not exceed to '+this.currency_format('',amountCovered)+'!');
                    bool = false;
                }

                if(amountCarewell > currentBalance)
                {
                    this.toaster('The total amount exceed to the balance of the member, The current balance is '+this.currency_format('',currentBalance)+'.');
                    bool = false;
                }
            }
            return bool;
        },
        validateIBR(benefitID){
            let ctr = 0;
            let bool = true;
            let procedureList = this.$refs.procedureInformation.procedure_list;
            procedureList.forEach((arr,ind) =>{
                if(benefitID == 6)
                {
                    if(arr.procedure.procedures.name == "NATURAL DEATH" || arr.procedure.procedures.name == "ACCIDENTAL DEATH")
                    {
                        ctr += 1;
                    }
                }
            });

            if(ctr > 1)
            {
                this.toaster('You cannot avail more than one Insurance Benefit Rider.');
                bool = false;
            }
            return bool;
        },
        validatePhysicianProcedure(procedure_list,physician_list){
            let bool = true;
            if(procedure_list.length == 0)
            {
                physician_list.forEach((arr,ind) => {
                    if(!arr.doctor_procedure_id)
                    {
                        this.toaster('You cannot avail without procedure selected.');
                        bool = false;
                    }
                })
            }
            return bool;
        }
    },
}