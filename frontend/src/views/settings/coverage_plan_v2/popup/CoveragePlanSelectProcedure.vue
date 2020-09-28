<template>
    <v-dialog v-model="dialog" scrollable persistent max-width="1366px" lazy>
        <v-btn class="ml-0" color="accent" depressed slot="activator" @click="loadDialog" :loading="loading">Select Procedure</v-btn>
        <v-card class="coverage-select-procedure">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Select Procedure - {{ benefitName }}</v-card-title>
            <v-card-text>
                <procedure-description 
                    :for_MBL="getMaxLimitPerIllnessYear" 
                    ref="coverageDescription"
                >
                </procedure-description>
                <procedure-table 
                    ref="coverageProcedure"
                    :benefitIndex="benefitIndex"
                >
                </procedure-table>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="tertiary" depressed dark @click="cancel">Cancel</v-btn>
                <v-btn color="accent" depressed @click="save">Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import { coveragePlanData } from '../js/CoveragePlanStore'

import ProcedureDescription from './sub_component/SelectProcedureDescription';
import ProcedureTable from './sub_component/SelectProcedureTable';

export default {
    components:{
        ProcedureDescription,
        ProcedureTable
    },
    data: () => ({
        dialog:false,
        loading : false,
    }),
    computed:{
        getMaxLimitPerIllnessYear(){
            return coveragePlanData.getters.MAX_LIMIT_PER_ILLNESS_OR_YEAR == 1 ? "Covered" : "Charged to MBL";
        }
    },
    props:{
        benefitName : {
            type: String,
            required: true,
        },
        benefitIndex : {
            type: Number,
            required: true,
        },
        benefitTypeId : {
            type: Number,
            required: true,
        }
    },
    methods:{
        async loadDialog(){   
            this.loading = true;

            this.loading = await false;
        },

        cancel(){
            this.dialog = false;
        },

        validateCoveredAmountProcedures(covered_amount, procedures){
            let check_amount;

            procedures.forEach((data,key) => {
                if(data)
                {
                    check_amount =  data.every(chk => (chk.amount ? parseFloat(chk.amount) : 0) <= parseFloat(covered_amount));
                    // check_amount =  data.every(chk => parseFloat(chk.amount) <= parseFloat(covered_amount));
                }
                else
                {
                    check_amount = false;
                }
            });
            
            return check_amount;
        },

        validateCoveredAmount(covered_amount){
            if(parseFloat(covered_amount) > 0){
                return true;
            }
            return false;
        },

        getSelectedAndCount(benefitTypeWithProcedure){
            let selected = {
                index: this.benefitIndex, 
                form : benefitTypeWithProcedure.form,
                procedures : benefitTypeWithProcedure.procedures
            }

            // console.log({selected})

            coveragePlanData.dispatch('getSelectedAndCount',selected);
        },

        validateForm(covered_amount, procedures){
            let validate = [];
            validate[0] = {
                value : this.validateCoveredAmount(covered_amount) || false,
                message : 'Fill up all fields',
            };
            validate[1] = {
                value : procedures.length > 0 ? true : false,
                message : 'Select at least one Procedure.'
            };
            validate[2] = {
                value : this.validateCoveredAmountProcedures(covered_amount, procedures) || false,
                message : 'Procedure amount must not be greater than the Amount Covered.'
            };

            let validation = [];
            for (let validate_data of validate){
                if(!validate_data.value){
                    validation = validate_data;
                    break;
                }
            }

            return validation;
        },

        save(){
            const { selected : procedures } = this.$refs.coverageProcedure;

            const { form : { covered_amount } } = this.$refs.coverageDescription;

            let validate = this.validateForm(covered_amount,procedures);
            
            if(validate.length == 0)//if all validation is satisfied
            {
                const { form } = this.$refs.coverageDescription;
                form.benefit_type_id = this.benefitTypeId;
                
                const benefitTypeWithProcedure = {form,procedures};

                this.getSelectedAndCount(benefitTypeWithProcedure);

                this.dialog = false;
            }
            else
            {
                this.toaster(validate.message,400);
            }
        },

        setSelectedDescription(){
            let index = this.benefitIndex;

            let checkIndexExist = coveragePlanData.getters.SELECTED_PROCEDURES[index] ? true : false;
    
            if(checkIndexExist)
            {
                let setData = coveragePlanData.getters.SELECTED_PROCEDURES[index].benefits;

                this.$nextTick(()=>{
                    this.$refs.coverageDescription.form = {
                        covered_amount : setData.covered_amount,
                        limit_per_year : setData.limit_per_year,
                        per_confinement : setData.per_confinement,
                        per_confinement_amount : setData.per_confinement_amount,
                        per_month : setData.per_month,
                        limit_per_month : setData.limit_per_month
                    }
                    this.$refs.coverageDescription.chargeMBL = setData.charges;
                })
            }
        },
        
        setSelectedProcedures(){
            let index = this.benefitIndex;

            let checkIndexExist = coveragePlanData.getters.SELECTED_PROCEDURES[index] ? true : false;

            if(checkIndexExist)
            {
                let setData = coveragePlanData.getters.SELECTED_PROCEDURES[index].procedures;

                // NOTE:
                //to avoid error length of null
                setData.forEach( (data, key) => {
                   if(!data)
                   {
                       setData[key] = [];
                   }
                });

                this.$nextTick(()=>{
                    this.$refs.coverageProcedure.selected = setData;
                });
            }
        },

        async setSelectedData(){
            await this.setSelectedDescription();
            await this.setSelectedProcedures();
        },

        async setDefaultSelected(){
            const benefitId = this.benefitTypeId;
            const benefitIndex = this.benefitIndex;

            const benefitIdHaveDefaults = [
                {
                    benefit_id : 1, //Annual Physical Examination (APE)
                    procedure_type : [
                        {
                            procedure_type_id : 1, //APE
                            procedure_id : [1,2,3,4,5] //CHEST XRAY, COMPLETE BLOOD COUNT, URINALYSIS, FECALYSIS, PHYSICAL EXAM
                        },
                    ],
                },
                {
                    benefit_id : 2, //Outpatient Laboratory
                    procedure_type : [
                        {
                            procedure_type_id : 4, //CONF/M.Opn
                            procedure_id : [18] //CONSULTATION
                        }
                    ],
                },
                {
                    benefit_id : 4, //"Minor Operation"
                    procedure_type : [
                        {
                            procedure_type_id : 4, //CONF/M.Opn
                            procedure_id : [25] //MINOR OPERATION
                        }
                    ],
                },

            ];

            let ifBenefitIdExist = benefitIdHaveDefaults.find( item => item.benefit_id ==  benefitId);
            
            if(ifBenefitIdExist && this.$refs.coverageProcedure.selected.length == 0)
            {
                let procedureTypeArray = [];
                const _benefitWithProcedures = coveragePlanData.getters.BENEFIT_WITH_PROCEDURES;

                let findBenefitType = _benefitWithProcedures.find( item => item.id == ifBenefitIdExist.benefit_id );

                ifBenefitIdExist.procedure_type.forEach( (data,key) => {
                    let findProcedureType = findBenefitType.procedure_type.find( item => item.id == data.procedure_type_id);
                    let findProcedureType_index = findBenefitType.procedure_type.findIndex( item => item.id == data.procedure_type_id);

                    let proceduresArray = []
                    data.procedure_id.forEach( (data,key) => {
                        let findProcedure = findProcedureType.procedures.find( item => item.id == data );
                        let findProcedure_index = findProcedureType.procedures.find( item => item.id == data );

                        findProcedure.selected = true;
                        proceduresArray.push(findProcedure);

                        procedureTypeArray[findProcedureType_index] = proceduresArray;
                    });
                });

                this.$refs.coverageProcedure.selected = procedureTypeArray;
            }
        }
    },
    async updated(){
        //to avoid error this.$refs.coverageProcedure 'selected' of undefined
        
        // console.log(typeof this.$refs.coverageProcedure);

        if(typeof this.$refs.coverageProcedure !== "undefined")
        {
            if(this.$route.query.id)
            {
                await this.setSelectedData();
            }

            await this.setDefaultSelected();
        }
    }
}
</script>