<template>
    <div>
        <div class="title text-dark font-weight-bold mb-2">Procedures</div>
        <v-divider></v-divider>
        <span class="top-holder">
            <span></span>
            <div>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </div>
        </span>
        <!-- <v-btn @click="getData()">CLICK</v-btn> -->
        <v-tabs dark slot="extension" color="primary" show-arrows fixed-tabs>
            <v-tabs-slider color="#DF7E00"></v-tabs-slider>
            <v-tab v-for="type in procedure_types" :key="type.name">
                {{ type.name }}
            </v-tab>
            <v-tab-item v-for="(type, index) in procedure_types" :key="type.name">
                <v-card flat>
                    <table 
                        class="table-procedures"
                    >
                        <thead>
                            <tr>
                                <th class="table-procedures-procedure-data">
                                    <input 
                                        type="checkbox" 
                                        @change="setSelectAllProcedure(index,$event)" 
                                        :checked="isSelectAll({base_length:type.procedures.length,index})"
                                    >
                                </th>
                                <th v-for="(header, key) in headers" :key="key" class="table-procedures-procedure-data">
                                    {{header.text}}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(procedure, key) in type.procedures" :key="key" v-show="procedure.show && procedure.is_archived == 0" class="table-procedures-procedures">
                                <td class="table-procedures-procedure-data">
                                    <input 
                                        type="checkbox" 
                                        v-model="procedure.selected" 
                                        @change="setSelectedProcedure({index,procedure_data:procedure})" 
                                        :checked="procedure.selected"
                                    >
                                </td>
                                <td class="table-procedures-procedure-data">{{ procedure.name }}</td>
                                <td>
                                    <currency-format-v2  
                                        v-model="procedure.amount" 
                                        :default_amount="procedure.amount"
                                        @change="changeAmount({selected:procedure.selected, type_index:index, procedure_id:procedure.id, amount:procedure.amount})"
                                        class="text-amount"
                                    ></currency-format-v2>
                                    <!-- <input 
                                        type="number" 
                                        v-model="procedure.amount"
                                        class="text-amount"
                                        @change="changeAmount({selected:procedure.selected, type_index:index, procedure_id:procedure.id, amount:procedure.amount})"
                                    > -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="button-holder mt-3">
                        <create-plan-description :form="{procedure_type_id: type.id}"><v-btn color="info" class="m-0">ADD PROCEDURE</v-btn></create-plan-description>                      
                        <v-spacer></v-spacer>
                    </div>
                </v-card>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
import { coveragePlanData } from '../../js/CoveragePlanStore';

export default {
    data: () => ({
        search:'',
        headers: [
            {text: 'DESCRIPTION'}, 
            {text: 'SPECIAL AMOUNT'}
        ],
        selected:[],
        select_procedure:[],
        select_select:[],
        selected_length: '',
    }),
    props:{
        benefitIndex : {
            type : Number,
            required : true
        }
    },
    computed:{
        procedure_types(){
            const index = this.benefitIndex;
            let procedures_type = coveragePlanData.getters.BENEFIT_WITH_PROCEDURES[index].procedure_type;

            procedures_type.forEach((data, key) => 
            {
                data.procedures.forEach((procedure, procedure_key) =>
                {
                    if(this.search)
                    {
                        if (procedure.name.toLowerCase().indexOf(this.search.toLowerCase()) > -1)
                        {
                            procedure.show = true;
                        }
                        else
                        {
                            procedure.show = false;
                        }
                    }
                    else
                    {
                        procedure.show = true;
                    }
                });
            });

            return procedures_type;
        }
    },
    methods:{
        isSelectAll({base_length,index}){
            let selected = this.selected[index];
            if(selected)
            {
                if(base_length == this.selected[index].length)
                {
                    return true;
                }
            }
            return false;
        },
        setSelectAllProcedure(procedure_type_index, event){
            const index = this.benefitIndex;
            let event_target = event.target.checked;
            if(event_target)
            {
                this.selected[procedure_type_index] = [];
                let procedure_types = this.procedure_types;
                procedure_types[procedure_type_index].procedures.map(item => item.selected = true);
                this.selected[procedure_type_index] = procedure_types[procedure_type_index].procedures;
            }
            else
            {
                this.selected[procedure_type_index] = [];
                let procedure_types = this.procedure_types;
                procedure_types[procedure_type_index].procedures.map(item => item.selected = false);
            }
        },
        setSelectedProcedure({index,procedure_data}){
            let selected = this.selected;

            let find_procedure = selected[index].findIndex(item => item.id == procedure_data.id )

            if(find_procedure == -1)
            {
                selected[index].push(procedure_data);
            }
            else
            {
                selected[index] = selected[index].filter(item => item.id != procedure_data.id )
            }
        },
        changeAmount({selected, type_index, procedure_id, amount}){
            if(selected)
            {   
                let data = this.selected[type_index].find(item => item.id == procedure_id);
                return data.amount = amount;
            }
        },
        async retainSelectedData(){   
            //use when adding procedures because checkbox deselects when adding procedures
            let selected = this.selected;

            await selected.forEach((data,key)=>{
                data.forEach((procedure,key_procedure)=>{
                    this.procedure_types[key].procedures.find(item =>{
                        if(item.id == procedure.id)
                        {
                            item.selected = true;
                        }
                    });
                });
            });
        },
        async setItemCount(){
            const index = this.benefitIndex;
            let item_count = 0;
            const checkIfExist = coveragePlanData.getters.SELECTED_PROCEDURES[index] ? true : false;
            if(checkIfExist)
            {
                coveragePlanData.getters.SELECTED_PROCEDURES[index].procedures.forEach((data,key)=>{
                    data ? item_count += data.length : item_count += 0;
                });
            }
            coveragePlanData.getters.BENEFIT_WITH_PROCEDURES[index].item_count = item_count
        },
        getData(){
            console.log(this.selected,'asfga')
        },
        setSelectedArray(){
            //set this.selected data to [] for each procedure types
            this.procedure_types.forEach((data,key) => {
                if(typeof this.selected[key] === "undefined")
                {
                    this.selected[key] = [];
                }
            });
        }
    },
    updated(){
        this.$nextTick(()=>{
            this.setSelectedArray();
            this.retainSelectedData();
            this.setItemCount();
        })
    }
}
</script>

<style lang="scss" scoped>
    .top-holder {
        border: 1px solid #ededed;
        padding: 15px;
        display: grid;
        background-color: #ffffff;
        grid-template-columns: repeat(2, 1fr);
        grid-column-gap: 50px;
        margin-bottom: 10px;

        .v-text-field {
            font-size: 15px;

            .v-input__slot {
                border: 1px solid #919191 !important;
            }
        }

        .v-text-field__details {
            margin-bottom: 0px !important;
        }

        .v-input--radio-group {
            margin-top: 0px !important;

            .v-input__slot {
                margin-bottom: 0px !important;
            }

        }
    }
    .table-procedures {
        width : 100%;

        & td, & th {
            height: 3em;
        };

        &-procedures:hover {
            background-color:#e0e0e0;
        }

        &-procedure-data {
            text-align: center;
            vertical-align: middle;
        };
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

