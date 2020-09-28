<template>
    <div class="bottom-holder">
        <div class="title text-dark font-weight-bold mb-2">TYPE OF BENEFITS</div>
        <v-divider></v-divider>
        <div class="benefits-holder mt-3">
            <div v-for="(benefit, index) in benefitTypes" :key="benefit.id">
                <div class="body-2 font-weight-medium">{{benefit.name}}</div>
                <coverage-plan-select-procedure :benefitName="benefit.name" :benefitIndex="index" :benefitTypeId="benefit.id"></coverage-plan-select-procedure>
                <span class="caption ml-2">{{ benefit.item_count }} Items Selected</span>
            </div>
        </div>
    </div>
</template>
<script>
import { coveragePlanData } from '../js/CoveragePlanStore';

import CoveragePlanSelectProcedure from '../popup/CoveragePlanSelectProcedure';

export default {
    components:{
        CoveragePlanSelectProcedure
    },
    data : () => ({
        filters : {
            is_archived : 0,
            showAll: 1
        },
    }),
    computed:{
        benefitTypes(){
            return coveragePlanData.getters.BENEFIT_WITH_PROCEDURES;
        },
    },
    methods:{
        async getBenefitWithProcedures(){
            const { data : { data : benefitData } } = await axios.get('/api/benefit_type' + this.generateFilterURL(this.filters));

            const { data : { data : procedureTypes } } = await axios.get('/api/procedures_type' + this.generateFilterURL(this.filters));

            //add show and selected property
            procedureTypes.map(data=>{
                data.procedures.map(procedureData=> {
                    procedureData.show = true;
                    procedureData.selected = false;
                });
            });

            await benefitData.map(data => {data.procedure_type = procedureTypes});

            coveragePlanData.dispatch('getBenefitWithProcedures', benefitData);
        },
        async setSelectedProcedures(){
            if(this.$route.query.id)
            {   
                let benefit_procedures = coveragePlanData.getters.SET_BENEFIT_PROCEDURES;

                // console.log({benefit_procedures})

                let benefit_types = this.benefitTypes;

                let selected = [];
                let benefit_index = [];
                let procedureData = [];

                await benefit_procedures.forEach((benPro,benPro_key) =>{
                    let search_benefit = (item) => item.id == benPro.benefit_type_id;
                    let benefitType = benefit_types.find(search_benefit);
                    let benefitType_index = benefit_types.findIndex(search_benefit);
         

                    if(benefitType)
                    {
                        //to prevent error .push is not a function
                        selected[benefitType_index] = {
                            benefits : benPro,
                            procedures : [],
                        }

                        //to prevent error .push is not a function
                        benefit_index[benefitType_index] = {
                            procedure_index : []
                        }

                        benPro.coverage_plan_procedures.forEach((benProCovPro,benProCovPro_key) =>{
                            let search_procedure_type = (item) => item.id == benProCovPro.procedure_type_id;
                            let procedure_type = benefitType.procedure_type.find(search_procedure_type);
                            let procedure_type_index = benefitType.procedure_type.findIndex(search_procedure_type);
 
                            if(procedure_type)
                            {
                                let procedure = procedure_type.procedures.find(item => item.id == benProCovPro.procedure_id);

                                if(procedure)
                                {
                                    //this data is use for groupings of procedures
                                    procedure.procedure_type_index = procedure_type_index;
                                    procedure.benefit_index = benefitType_index;
                                    procedure.benefit_type_id = benefitType.id;
                                    procedure.coverage_plan_procedure_id = benProCovPro.id;

                                    procedure.amount = benProCovPro.amount;

                                    procedure.selected = true;
                                    procedure.show = true;

                                    benefit_index[benefitType_index].procedure_index.push(procedure_type_index);

                                    //get all data of procedure with extra set values
                                    procedureData.push(procedure);
                                }
                            }
                        });
                    }
                });

                //remove duplicate procedure_index
                await benefit_index.map(item => item.procedure_index = [...new Set(item.procedure_index)]);

                //groupings of procedure per benefit type and procedure type
                await benefit_index.forEach((data,key)=>{
                    data.procedure_index.forEach((procedure,data_key)=>{
                        let getSelected = procedureData.filter(item=>item.benefit_index == key && item.procedure_type_index == procedure)
                        selected[key].procedures[procedure] = getSelected;
                    });
                });

                //set data to store in vuex
                await selected.forEach((data,key)=>{
                    let selectedData = {
                        index : key,
                        form : data.benefits,
                        procedures : data.procedures
                    }

                    coveragePlanData.dispatch('getSelectedAndCount',selectedData);
                });

                // console.log(coveragePlanData.getters.SELECTED_PROCEDURES)
            }
        },
        async getData(){
            await this.getBenefitWithProcedures();
            await this.setSelectedProcedures();
        },
        setItemCount(){
            const coverageData = coveragePlanData.getters;
            if(coverageData.SELECTED_PROCEDURES.length)
            {
                coverageData.BENEFIT_WITH_PROCEDURES.map((item,key) => {

                    let item_count = 0;
                    let checkIfExist = coverageData.SELECTED_PROCEDURES[key] ? true : false;

                    if(checkIfExist)
                    {
                        coverageData.SELECTED_PROCEDURES[key].procedures.forEach((data,key_data)=>{
                            data ? item_count += data.length : item_count += 0;
                        })
                    }
                    // console.log({item_count})
                    item.item_count = item_count;
                })
                // console.log({coveragePlanData})
            }
        }
    },
    created(){
        this.getData();
    },
    async updated(){
        await EventBus.$off('addCoveragePlanProceduresVuex');
        await EventBus.$on('addCoveragePlanProceduresVuex', () => {
            this.getBenefitWithProcedures();
        });
        
        this.$nextTick(()=>{
                this.setItemCount();
        });
    }
}
</script>

<style lang="scss" scoped>
    .bottom-holder {
        padding: 15px;
        border: 1px solid #ededed;
        background-color: #ffffff;
            
        .benefits-holder {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 50px;
            grid-row-gap: 15px;
        }
    }
</style>

