<template>
    <div class="availment-summary">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
         <div class="availment-summary__menu">
            <span></span>
            <span></span>
        </div>
        <div class="availment-summary__search">
            <v-select outline label="Filter by" :items="filter_type" v-model="filters.filter_by" @change="clearDatePicker"></v-select>
            <v-menu
                v-model="datepicker_from"
                :close-on-content-click="false"
                :nudge-right="40"
                lazy
                transition="scale-transition"
                offset-y
                full-width
            >
                <v-text-field
                    slot="activator"
                    v-model="filters.date_from"
                    label="Date From..."
                    append-icon="event"
                    outline
                    readonly
                    ></v-text-field>
                <v-date-picker v-model="filters.date_from" @input="datepicker_from = false" :max="new Date().toISOString().substr(0, 10)" min="1950-01-01"></v-date-picker>
            </v-menu>
            
            <v-menu
                v-model="datepicker_to"
                :close-on-content-click="false"
                :nudge-right="40"
                lazy
                transition="scale-transition"
                offset-y
                full-width
            >
                <v-text-field
                    slot="activator"
                    v-model="filters.date_to"
                    label="Date To..."
                    append-icon="event"
                    outline
                    readonly
                ></v-text-field>
                <v-date-picker v-model="filters.date_to" @input="datepicker_to = false" :max="new Date().toISOString().substr(0, 10)" min="1950-01-01"></v-date-picker>
            </v-menu>
            
            <v-btn color="accent" depressed type="submit" @click="REPORT_MIXINS_GET_DATA('company-availment','index')">Run Report</v-btn>
            <span></span>
            <v-btn color="tertiary" depressed dark type="submit">Export to PDF</v-btn>
            <v-btn color="success" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('company-availment','excel')">Export to Excel</v-btn>
        </div>
            <v-card flat>
                <v-data-table 
                    :headers="headers" 
                    :items="companies" 
                    :loading="loading"
                    :pagination.sync ="pagination"
                    :total-items="table.total"
                    :rows-per-page-items="[5,15,25,35]"
                    class="elevation-1"
                >
                    <!-- <template v-slot:items="props"> -->
                    <template slot="items" slot-scope="props">
                        <tr @click="props.expanded = !props.expanded">
                        <td class="text-xs-left">
                            {{props.item.name}}<v-icon small v-if="props.expanded">mdi-chevron-down</v-icon><v-icon small v-else>mdi-chevron-right</v-icon>
                        </td>
                        <td class="text-xs-left">&nbsp;</td>
                        <td class="text-xs-left">&nbsp;</td>
                        <td class="text-xs-left">&nbsp;</td>
                        <td class="text-xs-left">&nbsp;</td>
                        <td class="text-xs-left">&nbsp;</td>
                        <td class="text-xs-left">{{currency_format('',props.item.grand_total)}}</td>
                        <td class="text-xs-left">{{currency_format('',props.item.procedure_total)}}</td>
                        </tr>
                    </template>

                    <!-- <template v-slot:expand="props"> -->
                    <template slot="expand" slot-scope="props">
                        <v-card flat>
                            <table>
                                <tbody>
                                    <!-- <template> -->
                                        <tr  v-for="(availment, key) in props.item.availments" :key="key">
                                            <td></td>
                                            <td>{{availment.approval_id}}</td>
                                            <td>{{availment.member_name}}</td>
                                            <td>{{availment.coverage_plan_name}}</td>
                                            <td>{{availment.final_diagnosis}}</td>
                                            <td>{{availment.diagnosis_name}}</td>
                                            <td>{{currency_format('',availment.grand_total)}}</td>
                                            <td>{{currency_format('',availment.procedures_carewell_charged_total)}}</td>
                                        </tr>
                                    <!-- </template> -->
                                </tbody>
                            </table>
                        </v-card>
                    </template>
                </v-data-table>
            </v-card>
    </div>
</template>

<script>
import ReportMixins from '../js/ReportMixins'

    export default {
        mixins : [ ReportMixins ],
        data: () => ({
            breadcrumbs: [
                {text: 'Dashboard',disabled: false,}, 
                {text: 'Reports', disabled: false,},
                {text: 'Company Availment Report',disabled: true,}
            ],
            headers: [
                {text: 'COMPANY', value: 'name'},
                {text: 'APPROVAL #', value: 'approval_number'}, 
                {text: 'MEMBER NAME',value: 'member_name'}, 
                {text: 'COVERAGE PLAN',value: 'coverage_plan'}, 
                {text: 'FINAL DIAGNOSIS',value: 'final_diagnosis'}, 
                {text: 'CHARGED TO',value: 'diagnosis'}, 
                {text: 'GRAND TOTAL',value: 'grand_total'}, 
                {text: 'TOTAL APPROVED',value: 'charged_to_carewell'}, 
            ],
            filter_type: [
                'All Dates',
                'Custom',
                'Today',
                'This Week',
                'This Month',
                'This Quarter',
                'This Year'
            ],
            active_tab: 'tab-0',
            item:[],
            companies:[],
            provider : [],
            filters:{},
            route : null,
            status:'new',
            pagination: {},
            table : {},
            loading : false,
            datepicker_from: false,
            datepicker_to: false,
        }),
        mounted(){
            this.loadDialog();
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";
    .availment-summary {
        .availment-summary__menu
        {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            justify-content: center;
            align-self: center;
            .v-btn
            {
                height: 40px;
            }
            :nth-child(2)
            {
                .v-input__slot
                {
                    width: 80%;
                    margin-left: 20px;
                }
            }
        }
        .availment-summary__search
        {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            justify-content: center;
            align-self: center;
            grid-gap: 10px;
        }
        table {
            table-layout: fixed;
            width : 100%;
            border-collapse: collapse;
            font-family: 'Montserrat', sans-serif !important;
        }
    }
</style>