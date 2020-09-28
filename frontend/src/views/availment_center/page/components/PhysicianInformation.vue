<template>
    <span>
        <v-label class="tab-title"><h3> Physician Information</h3></v-label>
        <div class="my-0 py-0">
            <v-flex xs12>
                <template>
                    <v-data-table 
                        :headers="headers_physician" 
                        :items="physician_list"
                        hide-actions
                    >
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-center">
                                <v-autocomplete
                                    v-model="physician_list[props.index].doctor_id"  
                                    :rules="requiredRules" 
                                    :items="providerListDoctor"
                                    :disabled="for_viewing"
                                    color="accent" 
                                    item-value="id" 
                                    item-text="name" 
                                    label="Doctor"
                                    placeholder="Select Doctor"
                                />
                            </td>
                            <td class="text-xs-center">
                                <!-- <v-autocomplete 
                                    v-model="physician_list[props.index].specialization_id" 
                                    :items="specialization" 
                                    :disabled="for_viewing"
                                    color="accent" 
                                    item-value="id" 
                                    item-text="name"
                                /> -->
                                <global-select-box
                                    v-model="physician_list[props.index].specialization_id" 
                                    :default="physician_list[props.index].specialization_id" 
                                    api-link="api/specialization"
                                    :filters="{is_archived:0}"
                                    is-load
                                    :disabled="for_viewing"
                                    color="accent" 
                                    item-value="id" 
                                    item-text="name"
                                ></global-select-box>
                            </td>
                            <td class="text-xs-center">
                                <v-select 
                                    v-model="physician_list[props.index].doctor_rate_rvs" 
                                    color="accent" 
                                    :items="rate_rvs"
                                    :disabled="for_viewing"
                                />
                            </td>
                            <td class="text-xs-center">
                                <v-autocomplete 
                                    v-model="physician_list[props.index].doctor_procedures_id" 
                                    :items="memberListProcedure" 
                                    :disabled="for_viewing"
                                    color="accent" 
                                    item-value="id" 
                                    item-text="name"
                                />
                            </td>
                            <td class="text-xs-center">
                                <!-- <currency-format-v2  
                                    v-model="physician_list[props.index].doctor_fee" 
                                    :default_amount="physician_list[props.index].doctor_fee" 
                                    :readonly="for_viewing"
                                    class="text-amount"
                                ></currency-format-v2> -->
                                <v-text-field 
                                    type="Number" 
                                    reverse 
                                    v-model="physician_list[props.index].doctor_fee" 
                                    :readonly="for_viewing"
                                    color="accent"
                                    @change="setChargeToCarewell(props.index,'doctor_fee')"
                                />
                            </td>
                            <td class="text-xs-center">
                                <!-- <currency-format-v2  
                                    v-model="physician_list[props.index].doctor_phic_amt" 
                                    :default_amount="physician_list[props.index].doctor_phic_amt" 
                                    :readonly="for_viewing"
                                    @change="physicianListComputation(props.index)"
                                    class="text-amount"
                                ></currency-format-v2> -->
                                <v-text-field 
                                    type="Number" 
                                    reverse 
                                    v-model="physician_list[props.index].doctor_phic_amt" 
                                    color="accent" 
                                    :readonly="for_viewing"
                                    @change="physicianListComputation(props.index,'doctor_phic_amt')"/>
                            </td>
                            <td class="text-xs-center">
                                <!-- <currency-format-v2  
                                    v-model="physician_list[props.index].doctor_patient_charge" 
                                    :default_amount="physician_list[props.index].doctor_patient_charge" 
                                    :readonly="for_viewing"
                                    @change="physicianListComputation(props.index)"
                                    class="text-amount"
                                ></currency-format-v2> -->
                                <v-text-field 
                                    type="Number" 
                                    reverse 
                                    v-model="physician_list[props.index].doctor_patient_charge" 
                                    color="accent" 
                                    :readonly="for_viewing"
                                    @change="physicianListComputation(props.index,'doctor_patient_charge')"/>
                            </td>
                            <td class="text-xs-center">
                                <!-- <currency-format-v2  
                                    v-model="physician_list[props.index].doctor_patient_charge" 
                                    :default_amount="physician_list[props.index].doctor_patient_charge" 
                                    :readonly="for_viewing"
                                    @change="physicianListComputation(props.index)"
                                    class="text-amount"
                                ></currency-format-v2> -->
                                <!-- <v-text-field 
                                    type="Number" 
                                    reverse 
                                    v-model="physician_list[props.index].doctor_carewell_charge" 
                                    color="accent" 
                                    :readonly="for_viewing"
                                    @change="physicianListComputation(props.index)"/> -->
                                <v-text-field 
                                    type="Number" 
                                    reverse 
                                    v-model="physician_list[props.index].doctor_carewell_charge" 
                                    color="accent" 
                                    readonly
                                    @change="physicianListComputation(props.index,'doctor_carewell_charge')"/>
                            </td>
                            <td class="text-xs-center" v-if="$route.query.is_adjust">
                                <!-- <currency-format-v2  
                                    v-model="physician_list[props.index].doctor_charge_other" 
                                    :default_amount="physician_list[props.index].doctor_charge_other" 
                                    :readonly="for_viewing"
                                    @change="physicianListComputation(props.index)"
                                    class="text-amount"
                                ></currency-format-v2> -->
                                <v-text-field 
                                    type="Number" 
                                    reverse v-model="physician_list[props.index].doctor_charge_other" 
                                    color="accent" 
                                    :readonly="for_viewing"
                                    @change="physicianListComputation(props.index,'doctor_charge_other')"/>
                            </td>
                            <td class="text-xs-center">
                                <!-- <v-icon class="mr-2" @click="doctorsAddRow" color="green" > add </v-icon> -->
                                <v-icon 
                                    class="mr-2" 
                                    @click="doctorListIndex(props.index)" 
                                    :disabled="for_viewing"
                                    color="red"
                                > close </v-icon>
                            </td>
                        </template>
                        <template slot="footer">
                            <td :colspan="headers_physician.length" class="text-xs-center">
                                <v-btn 
                                    :disabled="for_viewing"
                                    block 
                                    depressed 
                                    small 
                                    dark 
                                    @click="doctorsAddRow"
                                ><strong>ADD PHYSICIAN</strong></v-btn>
                            </td>
                        </template>
                        <template slot="no-data">
                            <v-alert  :value="true" color="info" icon="info" outline>
                                <span style="color:black;">Click "<strong>ADD PHYSICIAN</strong>" button to add physician.</span>
                            </v-alert>
                        </template>
                    </v-data-table>
                </template>
            <div class="top-holder">
                <v-label>&nbsp;</v-label>
                <div :class="[is_adjust? 'total-table-adjusted' : 'total-table']">
                    <strong>
                        <v-label> TOTAL ACTUAL PF CHARGE : </v-label><br>
                        PHP {{ getSumPhysicianActual | mixin_change_currency_format }}
                    </strong>
                    <strong>
                        <v-label> TOTAL PHIC CHARITY/SWA : </v-label><br>
                        PHP {{ getSumPhysicianPhic | mixin_change_currency_format }} 
                    </strong>
                    <strong>
                        <v-label> TOTAL CHARGED TO PATIENT : </v-label><br>
                        PHP {{ getSumPhysicianPatient | mixin_change_currency_format }}
                    </strong>
                    <strong>
                        <v-label> TOTAL CHARGED TO CAREWELL : </v-label><br>
                        PHP {{ getSumPhysicianCarewell | mixin_change_currency_format }} 
                    </strong>
                    <strong v-if="is_adjust">
                        <v-label> TOTAL CHARGED TO OTHERS : </v-label><br> 
                        PHP {{ getSumPhysicianOther | mixin_change_currency_format }}
                    </strong>
                </div>
            </div>    
            </v-flex>
        </div>
    </span>
</template>
<script>
import { availmentData } from '../../mixins/AvailmentStore.js';
import procedureInformation from '../components/ProcedureInformation';
import AvailmentAutoSaveMixins from '../../mixins/AvailmentAutoSaveMixins';

export default {
    mixins : [ AvailmentAutoSaveMixins ],
    components:{
        procedureInformation,
    },
    data : () => ({
        form : {},
        physician_list : [],
        procedures_doctor : [],
        specialization : [],
        rate_rvs : ['2001', '2009'],
        headers_physician: [
            {text: 'Physician', value: 'doctor_id'}, 
            {text: 'Specialization',value: 'specialization_id'}, 
            {text: 'Rate/RVS',value: 'rate_rvs'}, 
            {text: 'Procedure',value: 'procedures_id'}, 
            {text: 'Actual PF Charges',value: 'actual_pf_charge'}, 
            {text: 'PHIC Charity/SWA',value: 'phic_charity_amount'}, 
            {text: 'Charged to Patient',value: 'patient_charged'}, 
            {text: 'Charged to Carewell',value: 'carewell_charged'}, 
            {text: 'Action',value: 'action'}, 
        ],
        requiredRules: [
            v => !!v || "Input is required"
        ],
        is_adjust : false,
        ret_val : 0,
        for_viewing : false,
    }),
    computed : {
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
        providerListDoctor : function() {
            let provider_doctor = availmentData.getters.getProviderDoctorList;
            let arrayData = [];
            provider_doctor.forEach((array, index) => {
                arrayData.push(array.doctor)
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
        getSumPhysicianActual: function(){
            return this.physician_list.reduce((sum,arr)=>{
                return parseFloat(+sum + +arr.doctor_fee).toFixed(2)
            }, 0)
        },
        getSumPhysicianPhic: function(){
            return this.physician_list.reduce((sum,arr)=>{
                return parseFloat(+sum + +arr.doctor_phic_amt).toFixed(2)
            }, 0)
        },
        getSumPhysicianPatient: function(){
            return this.physician_list.reduce((sum,arr)=>{
                return parseFloat(+sum + +arr.doctor_patient_charge).toFixed(2)
            }, 0)
        },
        getSumPhysicianCarewell: function(){
            return this.physician_list.reduce((sum,arr)=>{
                return parseFloat(+sum + +arr.doctor_carewell_charge).toFixed(2)
            }, 0)
        },
         getSumPhysicianOther: function(){
            return this.physician_list.reduce((sum,arr)=>{
                return parseFloat(+sum + +arr.doctor_charge_other).toFixed(2)
            }, 0)
        },
    },
    watch : {
        providerListDoctor : function(newValue, oldValue) {
            // this.physician_list = new Array
            // this.doctorsAddRow();
        },
        getSumPhysicianCarewell : function(newValue, oldValue) {
            availmentData.dispatch('getTotalPhysicianCarewell', newValue);
        }
    },
    methods : {
        doctorsAddRow(){
            let data = { doctor_id : null,
                        specialization_id : null,
                        doctor_rate_rvs : null,
                        doctor_procedures_id : null,
                        doctor_fee : 0,
                        doctor_phic_amt: 0,
                        doctor_patient_charge: 0,
                        doctor_carewell_charge: 0,
                        doctor_charge_other: 0,
                        }
            this.physician_list.push(data);
        },
        doctorListIndex(index){
            let limit = 0;
            if(this.physician_list.length > limit)
            {
                this.physician_list.splice(index, 1);
            }
            else
            {
                this.toaster(`Cannot be less than ${limit}.`, 400);
            }
            this.physicianInfo();
        },
        getSpecialization(statusValue = 0){
            let filters = {
                is_archived : statusValue
            }
            this.axiosSpecialization(filters)
        },
        axiosSpecialization(filters){
            axios.get('api/specialization'+this.generateFilterURL(filters))
            .then(response=>{
                this.specialization = response.data.data.data 
            })
            .catch(error=>{

            })
        },
        physicianListComputation(index,text_field = ""){
            let doctor_fee = parseFloat(this.physician_list[index].doctor_fee);
            let phic_amt = this.physician_list[index].doctor_phic_amt;
            let patient_charge = this.physician_list[index].doctor_patient_charge;
            let carewell_charge = this.physician_list[index].doctor_carewell_charge;

            let total = +doctor_fee - ( +phic_amt + +patient_charge );

            if(total < 0)
            {
                if(text_field === 'doctor_phic_amt')
                {
                    this.physician_list[index].doctor_phic_amt = phic_amt = 0;
                }
                else if(text_field === 'doctor_patient_charge')
                {
                    this.physician_list[index].doctor_patient_charge = patient_charge = 0;
                }
                
                total = ( +doctor_fee - ( +phic_amt + +patient_charge ) );

                this.physician_list[index].doctor_carewell_charge = total;
            }
            // else
            // {
            //     this.physician_list[index].doctor_carewell_charge = (total).toFixed(2);
            // }

            this.physician_list[index].doctor_carewell_charge = (total).toFixed(2);
            this.physician_list[index].doctor_phic_amt = parseFloat(phic_amt).toFixed(2);
            this.physician_list[index].doctor_patient_charge = parseFloat(patient_charge).toFixed(2);

            return this.physicianInfo();

        },
        // physicianListComputation(index){
        //     let doctor_fee = parseFloat(this.physician_list[index].doctor_fee)
        //     let phic_amt = this.physician_list[index].doctor_phic_amt
        //     let patient_charge = this.physician_list[index].doctor_patient_charge
        //     let carewell_charge = this.physician_list[index].doctor_carewell_charge
        //     let bool = true;
        //     let ctr = this.ret_val;
        //     let total = +phic_amt + +patient_charge + +carewell_charge
        //     if(doctor_fee < total)
        //     {
        //         this.toaster(`Cannot be greater than Actual PF Charges`, 400);
        //         bool = false;
        //         ctr += 1;
        //     }
        //     else if (doctor_fee > total)
        //     {
        //         this.toaster(`Cannot be less than Actual PF Charges`, 400);
        //         bool = false;
        //         ctr += 1;
        //     }
        //     else
        //     {
        //         ctr = 0;
        //     }
        //     this.ret_val = ctr;
        //     this.physicianInfo();
        //     return bool;
        // },
        isAdjustHeader(){
            if(this.$route.query.is_adjust)
            {
                this.headers_physician.splice(8,0,{text: 'Charge to Other',value: 'charge_other'});
                this.is_adjust = true;
            }
        },
        physicianInfo(){
            availmentData.dispatch('physicianInformation',this.physician_list);
        },
        setChargeToCarewell(index,text_field){
                let doctor_fee = parseFloat(this.physician_list[index].doctor_fee).toFixed(2);
                this.physician_list[index].doctor_fee = doctor_fee;
                this.physician_list[index].doctor_carewell_charge  = doctor_fee;
                this.physicianListComputation(index,text_field)
        }
    },
    mounted(){
        this.isAdjustHeader();
        // this.doctorsAddRow();
        // if(this.physician_list.length == 0 && !this.$route.query.id)
        // {
        //     this.doctorsAddRow();
        // }
        // this.getSpecialization();
        availmentData.dispatch('viewingAvailment');
        this.for_viewing = availmentData.state.form.viewingAvailment;
    },
    updated() {
        this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.physician_list,'physicianInformation');
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
