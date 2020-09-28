<template>
    <v-container fluid>
        <v-layout column>
            <v-dialog
                v-model="loading"
                persistent
                width="300"
            >
                <v-card
                    color="primary"
                    dark
                >
                    <v-card-text>
                    {{loadingText}}
                    <v-progress-linear
                        indeterminate
                        color="white"
                        class="mb-0"
                    ></v-progress-linear>
                    </v-card-text>
                </v-card>
            </v-dialog>
            <coverage-plan-breadcrumbs></coverage-plan-breadcrumbs>
            <v-layout justify-end>
                <v-flex xs12 sm12 md2 lg2>
                    <b-dropdown id="ddown1" size="lg" right text="Actions" class="mb-2 pull-right ml-auto float-right">
                        <b-dropdown-item @click="save(0)">{{saveValueItems[0].text}}</b-dropdown-item>
                        <b-dropdown-item @click="save(1)">{{saveValueItems[1].text}}</b-dropdown-item>
                    </b-dropdown>
                    <!-- <v-select
                        v-model="save_value"
                        :items="saveValueItems"
                        :label="saveValueLabel"
                        box
                        item-text="text"
                        item-value="value"
                        @change="save($event)"
                    ></v-select> -->
                </v-flex>
              
            </v-layout>
            <coverage-plan-description ref="coveragePlanDescription"></coverage-plan-description>
            <coverage-plan-benefits></coverage-plan-benefits>
        </v-layout>
    </v-container>
</template>

<script>
import { coveragePlanData } from './js/CoveragePlanStore';

import CoveragePlanBreadcrumbs from './sub_component/CoveragePlanBreadcrumbs'
import CoveragePlanDescription from './sub_component/CoveragePlanDescription'
import CoveragePlanBenefits from './sub_component/CoveragePlanBenefits'
export default {
    components:{
        CoveragePlanBreadcrumbs,
        CoveragePlanDescription,
        CoveragePlanBenefits
    },
    data : () => ({
        initialState : () => ({
            age_bracket_from: 0,
            age_bracket_to: 1,
            annual_benefit_limit: "0",
            card_fee: "0",
            handling_fee: "0",
            hospital_income_benefit: "0",
            max_limit_per: "1",
            maximum_benefit_limit: "0",
            monthly_premium: "0",
            name: "",
            processing_fee: "0",
        }),
        form : {
            age_bracket : []
        },
        save_value:'',
        vuexData:null,
        saveValueLabel: 'Save',
        saveValueItems: [
            {text: 'Save & New', value : 0},
            {text: 'Save & Close', value : 1}
        ],
        loading: false,
        loadingText: 'Loading Data...'
    }),
    methods: {
        qwe(){
            this.loading = true
        },
        validateAgeBracket(from,to){
            from = parseFloat(from);
            to = parseFloat(to);
  
            if(from >= to ){
                return false;
            }
            return true;
        },
        setInitialState(){
            this.$refs.coveragePlanDescription.form =  this.initialState();
        },
        checkIfGreateThanZero(value){
            let number = parseFloat(value);
            return number > 0;
        },
        validateForm(){
            let coveragePlanDescription = this.$refs.coveragePlanDescription.form;// to limit typing of this.$refs.coveragePlanDescription.form

            let validate = [];
            validate[0] = {
                value : coveragePlanDescription.name ? true : false,
                message : 'Plan name is required.'
            };
            validate[1] = {
                value : this.validateAgeBracket(coveragePlanDescription.age_bracket_from,coveragePlanDescription.age_bracket_to),
                message : 'Invalid age bracket.'
            };
            validate[2] = {
                value : this.checkIfGreateThanZero(coveragePlanDescription.monthly_premium),
                message : 'Monthly premium is required.'
            };
            validate[3] = {
                value : this.checkIfGreateThanZero(coveragePlanDescription.maximum_benefit_limit),
                message : 'Maximum Benefit Limit is required.'
            };
            validate[4] = {
                value : coveragePlanDescription.pre_existing_id ? true : false,
                message : 'Select Pre-existing.'
            };
            validate[5] = {
                value : coveragePlanData.getters.SELECTED_PROCEDURES.length > 0 ? true : false,
                message : 'Select at least one procedure.'
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
        async savingOptions(method,url){
            this.loading = true;

            await axios[method](url,this.form)
            .then( response => {
                this.toaster(response.data,response.status);
                if(this.save_value == 1)
                {
                    this.$router.push('/settings/coverage-plan')
                }
                else
                {
                    if(this.saveValueLabel !== 'Update')
                    {
                        this.$refs.coveragePlanDescription.form.name = null
                    }
                }
            })
            .catch( error => {
                this.toaster(error.response.data, error.response.status)
            })
            .finally(()=>{
                this.save_value = ''
            })

            this.loading = await false;
        },
        saveOrUpdate(id){
            if(id){
                this.loadingText = 'Updating Data...';
                this.savingOptions('put',`/api/coverage_plan/${id}`);
            }
            else
            {
                this.loadingText = 'Saving Data...';
                this.savingOptions('post','/api/coverage_plan');
            }
        },
        async save(saveOption){
            let coveragePlanDescription = this.$refs.coveragePlanDescription.form;
            const { age_bracket_from : from ,  age_bracket_to : to }   = coveragePlanDescription;

            let validation = this.validateForm();

            if(validation.length == 0)
            {
                this.save_value = saveOption;
                this.form = coveragePlanDescription;

                this.form.age_bracket = [from,to];

                this.form.coverage_plan_benefits = coveragePlanData.getters.SELECTED_PROCEDURES;

                //add item count this.form.coverage_plan_benefits
                //use for checking for update
                this.form.coverage_plan_benefits.map( (item,key) =>  item.benefits.item_count = coveragePlanData.getters.BENEFIT_WITH_PROCEDURES[key].item_count);

                await this.saveOrUpdate(this.$route.query.id);
            }
            else
            {
                this.save_value = ''
                this.toaster(validation.message,400);
            }
        },
        setCoveragePlanDescription(coverage_plan){
            let coveragePlanDescription = this.$refs.coveragePlanDescription;
            coveragePlanDescription.form = {
                name : coverage_plan.name,
                age_bracket_from : coverage_plan.age_bracket_from,
                age_bracket_to : coverage_plan.age_bracket_to,
                annual_benefit_limit : coverage_plan.annual_benefit_limit,
                card_fee : coverage_plan.card_fee,
                handling_fee : coverage_plan.handling_fee,
                hospital_income_benefit : coverage_plan.hospital_income_benefit,
                max_limit_per : coverage_plan.max_limit_per,
                member_type : coverage_plan.member_type,
                maximum_benefit_limit : coverage_plan.maximum_benefit_limit,
                monthly_premium : coverage_plan.monthly_premium,
                processing_fee : coverage_plan.processing_fee,
                pre_existing_id : coverage_plan.pre_existing_id
            }
        },
        setSaveValue(){
            const values = [   
                    {text: 'Update', value : 0},
                    {text: 'Update & Close', value : 1}
                ];
            this.saveValueItems = values;
            this.saveValueLabel = 'Update';
        },
        async setData(coverage_plan_id){
            this.loading = true;
            if(coverage_plan_id){
                const {data : coverage_plan} = await axios.get(`/api/coverage_plan/${coverage_plan_id}`);

                this.setSaveValue();
                
                await this.setCoveragePlanDescription(coverage_plan);

                coveragePlanData.dispatch('setProcedures',coverage_plan);

                // console.log({coveragePlanData})
            }
            else{
                this.setInitialState();
                // return;
            }
            this.loading = await false;

        },
        clearCoveragePlanData(){
            coveragePlanData.dispatch('clearCoveragePlanDataVuex');
        }
    },
    mounted(){
        // this.setInitialState();
        
        this.setData(this.$route.query.id)
    },
    beforeRouteLeave(to, from, next){
        this.clearCoveragePlanData();
        next();
    },
    destroyed(){
        this.clearCoveragePlanData();
        console.log('destroyed')    
    }
}
</script>
