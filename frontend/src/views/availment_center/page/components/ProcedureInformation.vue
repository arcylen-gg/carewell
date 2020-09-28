<template>
    <span>
        <v-label class="tab-title"><h3>Procedures</h3></v-label>
        <div class="pull-right">
            <v-flex xs9 class="col-six-l">&nbsp;</v-flex>
            <v-flex xs3 class="col-six-r">
                <!-- <v-select 
                    v-model="procedure_status"
                    :items="status"
                    color="accent" 
                    item-value="value"
                    item-text="name" 
                    label="Select by Status"
                    outline
                /> -->
            </v-flex>
        </div>
        <template>
            <v-data-table
                :headers="headers_procedures"
                :items="procedure_list"
                hide-actions
            >
                <template slot="items" slot-scope="props">
                    <td class="text-xs-center">
                            <v-autocomplete
                            v-model="procedure_list[props.index].procedure" 
                            :rules="requiredRules" 
                            :items="memberListProcedure"
                            :disabled="for_viewing"
                            color="accent" 
                            item-value="id" 
                            item-text="name" 
                            label="Procedure"
                            placeholder="Select Procedure"
                            return-object
                        />
                    </td>
                    <td class="text-xs-center">
                        <!-- <currency-format-v2  
                            v-model="procedure_list[props.index].procedures_gross_amt"
                            :default_amount="procedure_list[props.index].procedures_gross_amt"
                            :readonly="for_viewing" 
                            class="text-amount"
                        ></currency-format-v2> -->
                        <v-text-field 
                            type="Number" 
                            reverse 
                            v-model="procedure_list[props.index].procedures_gross_amt"
                            color="accent"
                            @change="setChargeToCarewell(props.index,'procedures_gross_amt')"
                            :readonly="for_viewing"
                        />
                    </td>
                    <td class="text-xs-center">
                        <!-- <currency-format-v2  
                            v-model="procedure_list[props.index].procedures_phic_amt" 
                            :default_amount="procedure_list[props.index].procedures_phic_amt" 
                            @change="procedureListComputation(props.index)"
                            :readonly="for_viewing"
                            class="text-amount"
                        ></currency-format-v2> -->
                        <v-text-field 
                            type="Number" 
                            reverse 
                            v-model="procedure_list[props.index].procedures_phic_amt" 
                            color="accent" 
                            @change="procedureListComputation(props.index,'procedures_phic_amt')"
                            :readonly="for_viewing"
                        />
                    </td>
                    <td class="text-xs-center">
                        <!-- <currency-format-v2  
                            v-model="procedure_list[props.index].procedures_patient_charge" 
                            :default_amount="procedure_list[props.index].procedures_patient_charge" 
                            @change="procedureListComputation(props.index)"
                            :readonly="for_viewing"
                            class="text-amount"
                        ></currency-format-v2> -->
                        <v-text-field 
                            type="Number" 
                            reverse 
                            v-model="procedure_list[props.index].procedures_patient_charge" 
                            color="accent"
                            @change="procedureListComputation(props.index,'procedures_patient_charge')"
                            :readonly="for_viewing"
                        />
                    </td>
                    <td class="text-xs-center">
                        <!-- <currency-format-v2  
                            v-model="procedure_list[props.index].procedures_carewell_charge" 
                            :default_amount="procedure_list[props.index].procedures_carewell_charge" 
                            @change="procedureListComputation(props.index)"
                            :disabled="procedure_list[props.index].procedures_is_disapproved" 
                            :readonly="for_viewing"
                            class="text-amount"
                        ></currency-format-v2> -->
                        <!-- <v-text-field 
                            type="Number" 
                            reverse 
                            v-model="procedure_list[props.index].procedures_carewell_charge" 
                            color="accent" 
                            :disabled="procedure_list[props.index].procedures_is_disapproved" 
                            @change="procedureListComputation(props.index)"
                            :readonly="for_viewing"
                        /> -->
                        <v-text-field 
                            type="Number" 
                            reverse 
                            v-model="procedure_list[props.index].procedures_carewell_charge" 
                            color="accent" 
                            readonly
                            @change="procedureListComputation(props.index,'procedures_carewell_charge')"
                        />
                    </td>
                    <!-- <td class="text-xs-center" v-if="$route.query.is_adjust"> -->
                    <td class="text-xs-center">
                        <!-- <currency-format-v2  
                            v-model="procedure_list[props.index].procedures_charge_other" 
                            :default_amount="procedure_list[props.index].procedures_charge_other" 
                            @change="procedureListComputation(props.index)"
                            :disabled="procedure_list[props.index].procedures_is_disapproved" 
                            :readonly="for_viewing"
                            class="text-amount"
                        ></currency-format-v2> -->
                        <v-text-field 
                            type="Number" 
                            reverse 
                            v-model="procedure_list[props.index].procedures_charge_other" 
                            color="accent"  
                            @change="procedureListComputation(props.index,'procedures_charge_other')"
                            :readonly="for_viewing"
                        >0</v-text-field>
                    </td>
                    <td class="text-xs-center">
                        <v-text-field
                            v-model="procedure_list[props.index].procedures_remarks"
                            color="accent" 
                            :readonly="for_viewing"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-center" style="text-align:center !important">
                        <v-checkbox 
                            v-model="procedure_list[props.index].procedures_is_disapproved" 
                            @change="disapprovedProcedure(props.index)"
                            :disabled="for_viewing"
                        />
                    </td>
                    <td class="text-xs-center">
                        <!-- <v-icon class="mr-2" @click="procedureAddRow" color="green" > add </v-icon> -->
                        <v-icon 
                            class="mr-2" 
                            @click="procedureListIndex(props.index)" 
                            color="red" 
                            :disabled="for_viewing"
                        > close </v-icon>
                    </td> 
                </template>
                <template slot="footer">
                    <td :colspan="headers_procedures.length" class="text-xs-center">
                        <v-btn
                            :disabled="for_viewing"
                            block 
                            depressed 
                            small 
                            dark 
                            @click="procedureAddRow"
                        ><strong>ADD PROCEDURE</strong></v-btn>
                    </td>
                </template>
                <template slot="no-data">
                    <v-alert  :value="true" color="info" icon="info" outline>
                       <span style="color:black;">Click "<strong>ADD PROCEDURE</strong>" button to add procedure.</span>
                    </v-alert>
                </template>
            </v-data-table>
        </template>
        <v-flex xs12>
            <div class="top-holder">
                <v-label>&nbsp;</v-label>
                    <!-- <div :class="[is_adjust? 'total-table-adjusted' : 'total-table']"> -->
                    <div class="total-table-adjusted">
                        <strong>
                            <v-label> TOTAL GROSS AMOUNT : </v-label><br> 
                            PHP {{ getSumProcedureGross | mixin_change_currency_format }}
                        </strong>
                        <strong><v-label> TOTAL PHIC CHARITY/SWA : </v-label><br> 
                            PHP {{ getSumProcedurePhic | mixin_change_currency_format }}
                        </strong>
                        <strong><v-label> TOTAL CHARGED TO PATIENT : </v-label><br> 
                            PHP {{ getSumProcedurePatient | mixin_change_currency_format}}
                        </strong>
                        <strong><v-label> TOTAL CHARGED TO CAREWELL : </v-label><br> 
                            PHP {{ getSumProcedureCarewell | mixin_change_currency_format}}
                        </strong>
                        <!-- <strong v-if="is_adjust"> -->
                        <strong><v-label> TOTAL CHARGED TO OTHERS : </v-label><br> 
                            PHP {{ getSumProcedureOther | mixin_change_currency_format }}
                        </strong>
                    </div>
            </div>
        </v-flex>
    </span>
</template>
<script>
import { availmentData } from '../../mixins/AvailmentStore.js';
import AvailmentAutoSaveMixins from '../../mixins/AvailmentAutoSaveMixins';

export default {
    mixins : [ AvailmentAutoSaveMixins ],
    data : () => ({
        form : {},
        procedure_status: {name : 'Approved', value: false},
        status: [
            {name : 'All', value: 'all'},
            {name : 'Approved', value: false},
            {name : 'Disapproved', value: true}
        ],
        procedure_list : [],
        requiredRules: [
            v => !!v || "Input is required"
        ],
        procedures:[],
        headers_procedures: [
            {text: 'Procedures', value: 'procedures_id'}, 
            {text: 'Gross Amount',value: 'gross_amount'}, 
            {text: 'PHIC Charity/SWA',value: 'phic_charity_amount'}, 
            {text: 'Charged to Patient',value: 'patient_charged'}, 
            {text: 'Charged to Carewell',value: 'carewell_charged'}, 
            {text: 'Charged to Other',value: 'charge_to_other'},
            {text: 'Remarks',value: 'remarks'},
            {text: 'Disapproved',value: 'disapproved'}, 
            {text: 'Action',value: 'action'}, 
        ],
        is_adjust :false,
        ret_val : 0,
        for_viewing : false,
    }),
    computed  : {
        memberListProcedure : function() {
            let procedure_list = availmentData.getters.getMemberProcedureList;
            let arrayData = [];
            procedure_list.forEach((array, index) => {
                let procedure_data = array.procedures;
                arrayData.push(procedure_data)
            });
            arrayData.sort(function(a, b) {
                var nameA = a.name.toUpperCase(); // ignore upper and lowercase
                var nameB = b.name.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) {
                    return -1;
                }
                if (nameA > nameB) {
                    return 1;
                }
                return 0;
            })
            return arrayData;
        },
        getSumProcedureGross: function(){
            return this.procedure_list.reduce((sum,arr)=>{ 
                sum = parseFloat(+sum + +arr.procedures_gross_amt).toFixed(2)
                    return sum
                }, 0)
        },
        getSumProcedurePhic: function(){
            return this.procedure_list.reduce((sum,arr)=>{
                sum = parseFloat(+sum + +arr.procedures_phic_amt).toFixed(2)
                    return sum
                }, 0)
        },
        getSumProcedurePatient: function(){
            return this.procedure_list.reduce((sum,arr)=>{
                sum = parseFloat(+sum + +arr.procedures_patient_charge).toFixed(2)
                    return sum
                }, 0)
        },
        getSumProcedureCarewell: function(){
            return this.procedure_list.reduce((sum,arr)=>{
                sum = parseFloat(+sum + +arr.procedures_carewell_charge).toFixed(2)
                    return sum
                }, 0)
        },
        getSumProcedureOther: function(){
            return this.procedure_list.reduce((sum,arr)=>{
                sum = parseFloat(+sum + +arr.procedures_charge_other).toFixed(2)
                    return sum
                }, 0)
        },
    },
    watch : {
        memberListProcedure : function(newValue, oldValue) {
            // if(!this.$route.query.id)
            // {
            //     this.procedure_list = new Array;
            //     this.procedureAddRow();
            // }   

            // this.procedure_list = new Array;
            // this.procedureAddRow();
        },
        getSumProcedureCarewell : function(newValue, oldValue) {
            availmentData.dispatch('getTotalProcedureCarewell', newValue);
        }
    },
    methods : {
        procedureAddRow(){
            let data = { procedure : null,
                        procedures_gross_amt : 0,
                        procedures_phic_amt : 0,
                        procedures_patient_charge : 0,
                        procedures_carewell_charge : 0,
                        procedures_charge_other: 0,
                        procedures_remarks: null,
                        procedures_is_disapproved : false }
            this.procedure_list.push(data)
        },
        procedureListIndex(index){
            let limit = 0
            if(this.procedure_list.length > limit)
            {
                this.procedure_list.splice(index, 1);
            }
            else
            {
                this.toaster(`Cannot be less than ${limit}.`, 400);
            }
        },
        procedureListComputation(index,text_field = ""){
            let gross_amount = this.procedure_list[index].procedures_gross_amt;
            let phic_amount = this.procedure_list[index].procedures_phic_amt;
            let patient_charge = this.procedure_list[index].procedures_patient_charge;
            let carewell = this.procedure_list[index].procedures_carewell_charge;
            let others = this.procedure_list[index].procedures_charge_other;

            let total = +gross_amount - ( +phic_amount + +patient_charge + +others );
            
            if(total < 0)
            {
                console.log('error');

                if(text_field === 'procedures_phic_amt')
                {
                    this.procedure_list[index].procedures_phic_amt = phic_amount = 0;
                }
                else if(text_field === 'procedures_patient_charge')
                {
                    this.procedure_list[index].procedures_patient_charge = patient_charge = 0;
                }
                else if(text_field === 'procedures_charge_other')
                {
                    this.procedure_list[index].procedures_charge_other = others = 0;
                }
                
                total = ( +gross_amount - ( +phic_amount + +patient_charge + +others ) );
                // return this.procedure_list[index].procedures_carewell_charge = total;
            }

            this.procedure_list[index].procedures_carewell_charge = total.toFixed(2);
            this.procedure_list[index].procedures_phic_amt = parseFloat(phic_amount).toFixed(2);
            this.procedure_list[index].procedures_patient_charge = parseFloat(patient_charge).toFixed(2);
            this.procedure_list[index].procedures_charge_other = parseFloat(others).toFixed(2);

            return;
        },
        // procedureListComputation(index){
        //     let gross_amount = parseFloat(this.procedure_list[index].procedures_gross_amt)
        //     let phic_amount = this.procedure_list[index].procedures_phic_amt
        //     let patient_charge = this.procedure_list[index].procedures_patient_charge
        //     let carewell = this.procedure_list[index].procedures_carewell_charge
        //     let others = this.procedure_list[index].procedures_charge_other
        //     let bool = true;
        //     let ctr = this.ret_val;
        //     this.ret_val = 0;
        //     let total = +phic_amount + +patient_charge + +carewell + +others
        //     if(gross_amount < total)
        //     {
        //         this.toaster(`Cannot be greater than Gross Amount.`, 400);
        //         bool = false;
        //         ctr = 1;
        //     }
        //     else if (gross_amount > total)
        //     {
        //         this.toaster(`Cannot be less than Gross Amount.`, 400);
        //         bool = false;
        //         ctr = 1;
        //     }
        //     else
        //     {
        //         bool = this.procedureAmountValidation(index)
        //         ctr = !bool ? 1 : 0;
        //     }

        //     this.ret_val = ctr !== 0 ? ctr : 0;
        //     return bool;
        // },
        disapprovedProcedure(index){
            const checked = this.procedure_list[index].procedures_is_disapproved;
            if(checked)
            {
                let patient_charge = this.procedure_list[index].procedures_patient_charge
                let carewell = this.procedure_list[index].procedures_carewell_charge
                this.procedure_list[index].procedures_patient_charge = +patient_charge + +carewell
                this.procedure_list[index].procedures_carewell_charge = 0
                return;
            }
            else
            {
                let gross_amount = this.procedure_list[index].procedures_gross_amt;
                let phic_amount = this.procedure_list[index].procedures_phic_amt;
                let patient_charge = this.procedure_list[index].procedures_patient_charge;
                let carewell = this.procedure_list[index].procedures_carewell_charge;
                let others = this.procedure_list[index].procedures_charge_other;
                
                const total = +gross_amount - (+phic_amount + +others);

                this.procedure_list[index].procedures_patient_charge = 0;
                this.procedure_list[index].procedures_carewell_charge = total;
                return;
            }            
        },
        procedureAmountValidation(index,total){
            let procedure = this.procedure_list[index].procedure;
            let procedure_amount = procedure ? +procedure.amount : 0;

            let carewell = +this.procedure_list[index].procedures_carewell_charge;
            // let bool = true;
            if(procedure_amount < carewell && procedure_amount != 0)
            {
                this.toaster('The "'+procedure.procedures.name+'" amount to be Charged to Carewell must not exceed to '+this.currency_format('₱',procedure_amount)+'!')
                this.procedure_list[index].procedures_carewell_charge = procedure_amount;
                this.procedure_list[index].procedures_patient_charge = +this.procedure_list[index].procedures_gross_amt - +procedure_amount;
                // bool = false;
            }
            else
            {
                this.procedure_list[index].procedures_carewell_charge = total;
            }
            return this.procedureInfo();
            // return bool;
        },
        // procedureAmountValidation(index){
        //     let procedure = this.procedure_list[index].procedure
        //     let procedure_amount = procedure ? +procedure.amount : 0
        //     let carewell = +this.procedure_list[index].procedures_carewell_charge
        //     let bool = true;
        //     if(procedure_amount < carewell && procedure_amount != 0)
        //     {
        //         this.toaster('The "'+procedure.procedures.name+'" amount to be Charged to Carewell must not exceed to '+this.currency_format('₱',procedure_amount)+'!')
        //         bool = false;
        //     }
        //     this.procedureInfo();
        //     return bool;
        // },
        isAdjustHeader(){
            if(this.$route.query.is_adjust)
            {
                this.headers_procedures.splice(5,0,{text: 'Charged to Other',value: 'charge_to_other'});
                this.is_adjust = true;
            }
        },
        procedureInfo(){
            availmentData.dispatch('procedureInformation',this.procedure_list);
        },
        setChargeToCarewell(index,text_field){
                let gross_amount =  parseFloat(this.procedure_list[index].procedures_gross_amt).toFixed(2);
                this.procedure_list[index].procedures_gross_amt = gross_amount;
                this.procedure_list[index].procedures_carewell_charge = gross_amount;
                this.procedureListComputation(index,text_field)
        }
    },
    mounted(){
        // this.isAdjustHeader();
        availmentData.dispatch('viewingAvailment');
        this.for_viewing = availmentData.state.form.viewingAvailment;
        // if(this.procedure_list.length == 0 && !this.$route.query.id)
        // {
        //     this.procedureAddRow();
        // }    

        //this.procedureAddRow();
    },
    updated() {
        this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.procedure_list,'procedureInformation');
    }
}
</script>

<style lang="scss" scoped>
//default is 4 column
//.total-table-adjusted is used when availment is adjust from payable
    .total-table-adjusted
    {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-column-gap: 50px;
        margin-bottom: 10px;
    }
    .text-amount {
        // outline: 1px solid black;
        width : 100%;
        height: 3em;
        border-bottom-style: solid;
        text-align: right;
        font-size: 1em;
        padding-right: 2em;
    }
</style>

