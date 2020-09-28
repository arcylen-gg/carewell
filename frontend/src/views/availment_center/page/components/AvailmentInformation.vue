<template>
    <span>
        <!-- <v-form ref="form" lazy-validation> -->
            <v-label><h3>Availment Information</h3></v-label>
            <div class="top-holder-four">
                <global-select-box
                    v-model="form.provider"
                    :rules="requiredRules" 
                    :default="form.provider"
                    :disabled="for_viewing"
                    api-link="api/provider"
                    :filters="{is_archived:0}"
                    color="accent" 
                    item-value="id" 
                    item-text="name" 
                    label="Select Provider" 
                    is-load
                    outline 
                    return-object
                    @input="getEmitData($event)"
                    @change="AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(form,'availmentInformation')"
                ></global-select-box>

                <v-autocomplete
                    v-model="form.benefitType" 
                    :rules="requiredRules" 
                    :items="memberListBenefitType"
                    :disabled="for_viewing"
                    color="accent" 
                    item-value="benefit_type.id" 
                    item-text="benefit_type.name" 
                    label="Select Type of Availment"
                    outline
                    return-object
                    @change="memberBenefitType"
                />
                <v-menu 
                    ref="date_avail_menu" 
                    :close-on-content-click="false" 
                    v-model="date_avail_val"
                    :nudge-right="120" 
                    lazy transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px"
                    :disabled="for_viewing"
                >
                    <date-format 
                        slot="activator" 
                        v-model="form.date_avail" 
                        :rules="requiredRules" 
                        color="accent" 
                        label="Availment Date" 
                        append-icon="event" 
                        readonly 
                        :disabled="for_viewing"
                        outline
                    ></date-format>
                    <v-date-picker 
                        ref="date_avail_picker" 
                        v-model="form.date_avail" 
                        min="1950-01-01"
                        @change="date_avail_save"
                    />
                </v-menu> 
                <v-menu 
                    ref="discharged_date_menu" 
                    :close-on-content-click="false" 
                    v-model="discharged_date_val"
                    :nudge-right="120" 
                    lazy transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px"
                    :disabled="for_viewing"
                >
                    <date-format 
                        slot="activator" 
                        v-model="form.discharged_date" 
                        color="accent" 
                        label="Discharged Date" 
                        append-icon="event" 
                        readonly 
                        :disabled="for_viewing"
                        outline
                    ></date-format>
                    <v-date-picker 
                        ref="discharged_date_picker" 
                        v-model="form.discharged_date" 
                        min="1950-01-01"
                        @change="discharged_date_save"
                    />
                </v-menu> 
            </div>
            <div class="top-holder-three">
                <v-textarea 
                    v-model="form.chief_complaint" 
                    :rules="!for_diagnosis ? requiredRules : []"  
                    color="accent" 
                    outline 
                    label="Chief Complaint"
                    :disabled="for_viewing"
                    @change="AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(form,'availmentInformation')"
                />
                <v-textarea 
                    v-model="form.initial_diagnosis" 
                    :rules="!for_diagnosis ? requiredRules : []" 
                    :disabled="for_viewing"
                    color="accent" 
                    outline 
                    label="Initial Diagnosis"
                    @change="AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(form,'availmentInformation')"
                />
                <v-textarea 
                    v-model="form.final_diagnosis" 
                    :rules="!for_diagnosis ? requiredRules : []" 
                    :disabled="for_viewing"
                    color="accent" 
                    outline 
                    label="Final Diagnosis"
                    @change="AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(form,'availmentInformation')"
                />
            </div>
            <global-select-box
                v-model="form.diagnosis_id"
                :default="form.diagnosis_id"
                api-link="api/diagnosis_list"
                :filters="{is_archived:0}"
                :disabled="for_viewing"
                :rules="!for_diagnosis ? requiredRules : []"
                color="accent" 
                item-value="id" 
                item-text="name" 
                label="Select Diagnosis to Charged" 
                is-load
                outline
                @change="AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(form,'availmentInformation')"
            ></global-select-box>
    </span>
</template>
<script>
import { availmentData } from '../../mixins/AvailmentStore.js';
import AvailmentAutoSaveMixins from '../../mixins/AvailmentAutoSaveMixins';

export default {
    mixins : [ AvailmentAutoSaveMixins ],
    data : () => ({
        form : {
            diagnosis_id : null,
            chief_complaint : "",
            initial_diagnosis : "",
            final_diagnosis : "",
        },
        providers : [],
        diagnosis : [],
        requiredRules: [
            v => !!v || "Input is required"
        ],
        date_avail_val : false,
        discharged_date_val : false,
        for_viewing : false,
        for_diagnosis : false,
    }),
    computed  : {
        memberListBenefitType : function() {
            this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.form,'availmentInformation');
            return availmentData.getters.getMemberBenefitTypeList;
        },
    },
    methods : {
        getEmitData(data){
            this.form.provider = data;
            this.providerSelect();
        },
        date_avail_save(date) {
            this.$refs.date_avail_menu.save(date)
            this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.form,'availmentInformation');
        },
        discharged_date_save(date) {
            this.$refs.discharged_date_menu.save(date)
            this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.form,'availmentInformation');
        },
        async memberBenefitType(){
            const { id } = this.form.benefitType;

            const { data : coverage_plan_procedures } = await axios.get(`/api/select/availment/coverage-plan/benefit-type/${id}/procedure`);

            this.form.benefitType.procedure = coverage_plan_procedures;

            this.for_diagnosis = this.form.benefitType.benefit_type.name == 'Outpatient Consultation';
            
            let availments = availmentData.getters.getSelectedMemberAvailment;
            
            let benefitID = this.form.benefitType.benefit_type_id;

            let arrayData = [];
            
            let totalAvailmentAmount = 0;
            
            let amount = 0;

            availments.forEach((arr,ind) => {
                if(arr.benefit_type_id == benefitID)
                {
                    arrayData.push(arr)
                    amount += arr.grand_total;
                }

                totalAvailmentAmount += arr.grand_total;
            });

            availmentData.dispatch('getMemberAvailmentByBenefit', arrayData);

            let deduction = totalAvailmentAmount - amount;

            let remaining_amount = availmentData.getters.maximumBenefitLimit - deduction;
            
            availmentData.dispatch('remaining_balance', remaining_amount);
            
            let current_balance = this.form.benefitType ? this.form.benefitType.covered_amount : 0;

            availmentData.dispatch('getSelectedMemberBenefitType',this.form.benefitType);

            availmentData.dispatch('amount_covered',current_balance);
        },
        async providerSelect(){
            const { id } = this.form.provider;
           
            const  { data : { doctor_providers },  data : { provider_payee }} = await axios.get(`/api/select/availment/provider/${id}/payee-and-doctor`);

            this.form.provider.provider_payee = provider_payee;
            this.form.provider.doctor_providers = doctor_providers;

            availmentData.dispatch('getSelectedProvider',this.form.provider);
        },
    },
    mounted(){
        availmentData.dispatch('viewingAvailment');
        this.for_viewing = availmentData.state.form.viewingAvailment;
    },
    // updated() { //keypress event
    //     this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.form,'availmentInformation');
    // }
}
</script>
