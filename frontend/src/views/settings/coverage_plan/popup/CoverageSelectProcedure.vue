<template>
    <v-dialog v-model="dialog" scrollable persistent max-width="1366px" lazy>
        <v-btn class="ml-0" color="accent" depressed slot="activator" @click="loadDialog" :loading="loading">Select Procedure</v-btn>
        <v-card class="coverage-select-procedure">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Select Procedure - {{item.name}} </v-card-title>
            <v-card-text>
                <procedure-description :for_MBL="for_MBL" :form="form"/>
                <procedure-table :form="form" :eventName="eventName" :benefit_index="benefit_index" :item="item" :selected="selected" :amount="amount" :select_basic_procedure="true"/>
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
import ProcedureDescription from './CoverageSelectProcedureDescription';
import ProcedureTable from './CoverageSelectProcedureTable';
    export default {
        components : {
            ProcedureDescription,
            ProcedureTable
        },
        data() {
            return {
                per_confinement: false,
                per_month: false,
                dialog: false,
                selected: [],
                amount : [],
                loading : false,
            }
        },
        props : {
            item : {
                type: Object,
            },
            eventName : {
                type: String,
                default: 'selectProcedures'
            },
            benefit_index : {
                type: Number,
                default: 0
            },
            form: {
                type: Object,
                default: () => {
                    return {
                    }
                }
            },
            for_MBL:{
                type: Number,
                default: 0
            }
        },
        watch : {
            for_MBL : function(newValue,oldValue){
                // console.log({newValue,oldValue})
                this.form.charges = newValue == 1 ? "Covered" : "Charged to MBL";
            }
        },
        methods : {
            matchProcedures() {
                this.procedure_types.forEach((d1, k1) => {
                    this.selected[k1] = []
                    d1.procedures.forEach((d2, k2) => {
                        this.procedure_types[k1].procedures[k2].amount = 0
                        if(this.form.coverage_plan_procedures && this.form.coverage_plan_procedures.length)
                        {
                            this.form.coverage_plan_procedures.forEach((d3, k3) => {
                                if(d2.id == d3.procedure_id)
                                {
                                    this.procedure_types[k1].procedures[k2].amount = d3.amount
                                    this.selected[k1][k2] = d2
                                }
                            })
                        }
                    })
                })
            },
            loadDialog() {
                console.log(this.item,'edrich')
                this.loading = true;
                console.log(this.for_MBL)
                this.form.charges = this.for_MBL == 1 ? "Covered" : "Charged to MBL";
                console.log(this.form,'asdasd')
                this.checkEdit();
            },
            save()
            {
                EventBus.$emit('passParamTrigger', true);
                if(this.form.covered_amount)
                {
                    this.form.item_count = 0
                    let chkAmount = [];
                    this.selected.forEach((data,key)=>{
                        chkAmount = data.filter(chk => parseFloat(chk.amount) > parseFloat(this.form.covered_amount))
                    })
                    if(chkAmount.length >= 1)
                    {
                        this.toaster('Procedure amount must not be greater than the Amount Covered.',400);
                    }
                    else
                    {
                        this.form.coverage_plan_procedures = this.selected
                        this.form.coverage_plan_procedures.forEach((data, key) => {
                            this.form.item_count += this.form.coverage_plan_procedures[key].length
                        })
                        this.form.benefit_type_id = this.item.id
                        this.form.benefit_index = this.benefit_index
                        EventBus.$emit(this.eventName, this.form)
                        this.dialog = false
                    }
                }
                else
                {
                    this.toaster('Filled Up Required Fields.',400);
                }
                
            },
            cancel() {
                this.dialog = false
            },
            checkEdit(){
                if(window.location.pathname === "/settings/coverage-plan/edit")
                {
                    this.matchProcedures()
                }
                else
                {
                    this.selectBasicProcedure();
                }
                this.loading = false;
            },
            selectBasicProcedure()
            {
                if(this.item.id == 1 && !this.selected.length)
                {
                    this.selected[0] = [];
                    this.selected[0][0] = {};
                    this.selected[0][0].id = "1";
                    this.selected[0][0].name = "CHEST XRAY";
                    this.selected[0][0].is_archived = "0";
                    this.selected[0][0].is_default = "1";
                    this.selected[0][0].procedure_type_id = "1";
                    this.selected[0][0].amount = null;

                    this.selected[0][1] = {};
                    this.selected[0][1].id = "2";
                    this.selected[0][1].name = "COMPLETE BLOOD COUNT";
                    this.selected[0][1].is_archived = "0";
                    this.selected[0][1].is_default = "1";
                    this.selected[0][1].procedure_type_id = "1";
                    this.selected[0][1].amount = null;

                    this.selected[0][2] = {};
                    this.selected[0][2].id = "3";
                    this.selected[0][2].name = "URINALYSIS";
                    this.selected[0][2].is_archived = "0";
                    this.selected[0][2].is_default = "1";
                    this.selected[0][2].procedure_type_id = "1";
                    this.selected[0][2].amount = null;

                    this.selected[0][3] = {};
                    this.selected[0][3].id = "4";
                    this.selected[0][3].name = "FECALYSIS";
                    this.selected[0][3].is_archived = "0";
                    this.selected[0][3].is_default = "1";
                    this.selected[0][3].procedure_type_id = "1";
                    this.selected[0][3].amount = null;

                    this.selected[0][4] = {};
                    this.selected[0][4].id = "5";
                    this.selected[0][4].name = "PHYSICAL EXAM";
                    this.selected[0][4].is_archived = "0";
                    this.selected[0][4].is_default = "1";
                    this.selected[0][4].procedure_type_id = "1";
                    this.selected[0][4].amount = null;
                }
                // console.log('item');
                // console.log(this.item);
                if(this.item.id == 2 && !this.selected.length)
                {
                    this.selected[3] = [];
                    this.selected[3][0] = {};
                    this.selected[3][0].id = "18";
                    this.selected[3][0].name = "CONSULTATION";
                    this.selected[3][0].is_archived = "0";
                    this.selected[3][0].is_default = "1";
                    this.selected[3][0].procedure_type_id = "4";
                    this.selected[3][0].amount = null;
                }

                if(this.item.id == 4 && !this.selected.length)
                {
                    this.selected[3] = [];
                    this.selected[3][0] = {};
                    this.selected[3][0].id = "25";
                    this.selected[3][0].name = "MINOR OPERATION";
                    this.selected[3][0].is_archived = "0";
                    this.selected[3][0].is_default = "1";
                    this.selected[3][0].procedure_type_id = "4";
                    this.selected[3][0].amount = null;
                }
            }
        },
        mounted () {
            this.checkEdit();
            EventBus.$off('procedureChangeParam');
            EventBus.$on('procedureChangeParam', (res) => 
            {
                this.selected = res;
                // this.selected[res.main_index][res.index].amount = res.amount;
            });
            // EventBus.$on('addCoveragePlanProcedures', () => {
            //     this.get_procedure_types()
            // });
        }
    }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

    .coverage-select-procedure {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .top-holder {
            border: 1px solid #ededed;
            padding: 15px 15px 0px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 50px;
            margin-bottom: 10px;

            .checkbox-holder {

                .v-input--checkbox {
                    margin-top: 0px;

                    .v-input__slot {
                        margin-bottom: 0px;
                    }

                    label
                    {
                        margin-bottom: 0px;
                    }
                }
            }

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

        .bottom-holder {
            padding: 15px;
            border: 1px solid #ededed;

            table {
                thead {
                    tr {
                        th {
                            text-align: center !important;
                        }
                    }
                }
            }

            .button-holder {
                display: flex;

                .v-text-field {
                    font-size: 15px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                        width: 50%;
                        margin-left: auto;
                        margin-bottom: 0px;
                    }
                }
            }
        }


    }
</style>