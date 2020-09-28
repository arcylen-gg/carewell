<template>
    <span>
         <v-label class="tab-title"><h3>Payee Information</h3></v-label>
            <div class="top-holder-three">
                <span>&nbsp;</span>
                <h4 color="primary">PAYEE</h4>
                <h4 color="primary">AMOUNT</h4>
            </div>
            <div class="top-holder-three">
                <!-- <span><input v-model="payee_checkbox" type="checkbox"></span> -->
            </div>
            <div class="top-holder-three">
                <v-label pull-right float-right>Hospital Fee</v-label>
                <v-autocomplete 
                    v-model="form.provider_payee_id" 
                    :rules="requiredRules" 
                    :items="providerListPayee" 
                    :disabled="for_viewing"
                    color="accent" 
                    item-value="id" 
                    item-text="name"  
                    label="Select Provider Payee" 
                    outline
                />
                <h3>{{ getSumProcedureCarewell | mixin_change_currency_format }}</h3>
            </div>
            <div class="top-holder-three">
                <v-label>Professional Fee</v-label>
                <v-autocomplete 
                    v-model="form.doctor_id" 
                    :rules="physicians.length != 0 ? requiredRules : []"
                    :items="providerListDoctor" 
                    :disabled="for_viewing"
                    color="accent" 
                    item-value="doctor.id" 
                    item-text="doctor.name" 
                    label="Select Doctor" 
                    outline
                />
                <h3>{{ getSumPhysicianCarewell | mixin_change_currency_format}}</h3>
            </div>            
            <div class="top-holder-three">
                <v-label pull-right float-right><h3 color="success"> Grand Total</h3></v-label>
                <v-label>&nbsp;</v-label>
                <h3>{{ getGrandTotal | mixin_change_currency_format }}</h3>
            </div>
    </span>
</template>
<script>
import { availmentData } from '../../mixins/AvailmentStore.js';
import AvailmentAutoSaveMixins from '../../mixins/AvailmentAutoSaveMixins'; 

export default {
    mixins : [ AvailmentAutoSaveMixins ],
    data : () => ({
        form : {
            doctor_id : null
        },
        requiredRules: [
            v => !!v || "Input is required"
        ],
        physicians : [],
        for_viewing : false,
        payee_checkbox: false
    }),
    watch: {
        payee_checkbox(val) {
            console.log(val);
        }
    },
    computed : {
        providerListDoctor : function() {
            let doctorList = availmentData.getters.getProviderDoctorList;
            let physicianList = availmentData.getters.physicianInformation;
            let listing = [];
            this.physicians = [];
            physicianList.forEach((arr,ind)=>{
                let physicianData = arr.doctor_id;
                doctorList.forEach((arrs,index)=>{
                    let doctorData = arrs.doctor_id;
                    if(physicianData === doctorData)
                    {
                        listing.push(arrs);
                    }
                })
            })
            this.physicians = physicianList;
            return listing;
        },
        providerListPayee : function() {
            let provider_payee = availmentData.getters.getProviderPayeeList;
            let arrayData = [];
            provider_payee.forEach((array, index) => {
                let payee_data = array;
                arrayData.push(payee_data)
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
        getSumProcedureCarewell : function () {
            return availmentData.state.form.getTotalProcedureCarewell;
        },
        getSumPhysicianCarewell : function () {
            return availmentData.state.form.getTotalPhysicianCarewell;
        },
        getGrandTotal : function () {
            return (+this.getSumProcedureCarewell + +this.getSumPhysicianCarewell).toFixed(2);
        },
    },
    mounted(){
        availmentData.dispatch('viewingAvailment');
        this.for_viewing = availmentData.state.form.viewingAvailment;
    },
    updated() {
        this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.form,'payeeInformation');
    }
}
</script>
