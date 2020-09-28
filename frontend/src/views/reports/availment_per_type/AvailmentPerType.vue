<template>
    <span>
        <div class="availment-per-type">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
         <div class="availment-per-type__menu">
            <span></span>
            <span></span>
        </div>
        <div class="availment-per-type__search">

            <v-select
                outline
                v-model="filters.month" 
                :items="month_name" 
                :item-text="month_name.name" 
                :item-value="month_name.id"
                label="Filter By Month"
            ></v-select>
            
            <v-menu
                ref="year_selected"
                v-model="year_selected"
                :close-on-content-click="false"
                :nudge-right="40"
                lazy
                transition="scale-transition"
                offset-y
                full-width
            >
                <v-text-field
                    slot="activator"
                    v-model="convertedDateFrom"
                    label="Filter By Year"
                    append-icon="event"
                    outline
                    readonly
                    ></v-text-field>
                <v-date-picker reactive no-title ref="picker" v-model="filters.year" @input="save" :max="new Date().toISOString().substr(0, 10)" min="1950-01-01"></v-date-picker>
            </v-menu>
            
            <v-btn color="accent" depressed type="submit" @click="getData('index')">Run Report</v-btn>

            <v-btn color="tertiary" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('availment-per-type','pdf')">Export to PDF</v-btn>

            <v-btn color="success" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('availment-per-type','excel')">Export to Excel</v-btn>
            <!-- <v-btn color="accent" depressed type="submit" @click="REPORT_MIXINS_GET_DATA('company-availment','index')">Company Availment</v-btn>
            <v-btn color="accent" depressed type="submit" @click="REPORT_MIXINS_GET_DATA('availment-monitoring','index')">Testing</v-btn> -->
        </div>
            <v-card flat>
                <v-data-table 
                    :headers="headers" 
                    :items="availment_per_type" 
                    :loading="loading"
                    :pagination.sync ="pagination"
                    :total-items="table.total"
                    :rows-per-page-items="[5,15,25,35]"
                    class="elevation-1"
                >
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left">{{props.item.name}}</td>
                        <td class="text-xs-left">{{props.item.patient_count[0]}}</td>
                        <td class="text-xs-left">{{props.item.total_per_benefit[0] | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.patient_count[1]}}</td>
                        <td class="text-xs-left">{{props.item.total_per_benefit[1] | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.patient_count[2]}}</td>
                        <td class="text-xs-left">{{props.item.total_per_benefit[2] | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.patient_count[3]}}</td>
                        <td class="text-xs-left">{{props.item.total_per_benefit[3] | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.patient_count[4]}}</td>
                        <td class="text-xs-left">{{props.item.total_per_benefit[4] | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.patient_count[5]}}</td>
                        <td class="text-xs-left">{{props.item.total_per_benefit[5] | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.patient_count[6]}}</td>
                        <td class="text-xs-left">{{props.item.total_per_benefit[6] | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.patient_count[7]}}</td>
                        <td class="text-xs-left">{{props.item.total_per_benefit[7] | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.total_patients}}</td>
                        <td class="text-xs-left">{{props.item.total_availments_amount | mixin_change_currency_format}}</td>
                    </template>

                    <template slot="footer" v-if="show">
                        <td class="text-xs-left availment-per-type--total"><strong>TOTAL</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_of_patient[0]}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_amount_availments[0] | mixin_change_currency_format}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_of_patient[1]}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_amount_availments[1] | mixin_change_currency_format}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_of_patient[2]}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_amount_availments[2] | mixin_change_currency_format}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_of_patient[3]}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_amount_availments[3] | mixin_change_currency_format}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_of_patient[4]}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_amount_availments[4] | mixin_change_currency_format}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_of_patient[5]}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_amount_availments[5] | mixin_change_currency_format}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_of_patient[6]}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_amount_availments[6] | mixin_change_currency_format}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_of_patient[7]}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.total_amount_availments[7] | mixin_change_currency_format}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.grand_total_patient}}</strong></td>
                        <td class="text-xs-left availment-per-type--total"><strong>{{total.grand_total_amount | mixin_change_currency_format}}</strong></td>
                    </template>
                </v-data-table>
            </v-card>
        </div>
    </span>
</template>

<script>
import ReportMixins from '../js/ReportMixins';

    export default {
        mixins : [ ReportMixins ],
        data: () => ({
            breadcrumbs: [
                {text: 'Dashboard',disabled: false,}, 
                {text: 'Reports', disabled: false,},
                {text: 'Availment Per Type Report',disabled: true,}
            ],
            headers: [
                {text: 'COMPANY', value: 'company_name'}, 
                {text: 'NO. OF PATIENTS',value: 'ape_total'}, 
                {text: 'ANNUAL PHYSICAL EXAMINATION (APE)',value: 'ape'}, 
                {text: 'NO. OF PATIENTS',value: 'consultation_total'}, 
                {text: 'OUTPATIENT CONSULTATION',value: 'consultation'}, 
                {text: 'NO. OF PATIENTS',value: 'laboratory_total'}, 
                {text: 'OUTPATIENT LABORATORY',value: 'laboratory'}, 
                {text: 'NO. OF PATIENTS',value: 'minor_OR_total'},
                {text: 'MINOR OPERATION',value: 'minor_OR'},
                {text: 'NO. OF PATIENTS',value: 'emergency_cases_total'},
                {text: 'EMERGENCY CASES',value: 'emergency_cases'},
                {text: 'NO. OF PATIENTS',value: 'ibr_total'},
                {text: 'INSURANCE BENEFIT RIDER',value: 'ibr'},
                {text: 'NO. OF PATIENTS',value: 'confinement_total'},
                {text: 'CONFINEMENT',value: 'confinement'},
                {text: 'NO. OF PATIENTS',value: 'dental_total'},
                {text: 'DENTAL',value: 'dental'},
                {text: 'TOTAL NO. OF PATIENTS',value: 'total_no_availment'},
                {text: 'GRAND TOTAL AMOUNT',value: 'grand_total_amount'},
            ],
            month_name: [
                {text: 'ALL MONTH', value: 0},
                {text: 'JANUARY', value: 1},
                {text: 'FEBRUARY', value: 2},
                {text: 'MARCH', value: 3},
                {text: 'APRIL', value: 4},
                {text: 'MAY', value: 5},
                {text: 'JUNE', value: 6},
                {text: 'JULY', value: 7},
                {text: 'AUGUST', value: 8},
                {text: 'SEPTEMBER', value: 9},
                {text: 'OCTOBER', value: 10},
                {text: 'NOVEMBER', value: 11},
                {text: 'DECEMBER', value: 12},
            ],
            active_tab: 'tab-0',
            availment_per_type:[],
            provider : [],
            filters:{
                month : 0,
                year: new Date().toISOString().substr(0,10)
            },
            route : null,
            status:'new',
            pagination: {},
            table : {},
            loading : false,
            year_selected: false,
            show: false
        }),
        watch: {
            year_selected (val) {
                val && this.$nextTick(() => (this.$refs.picker.activePicker = 'YEAR'))
            },
        },
        computed : {
			convertedDateFrom : function () {
                return this.filters.year.substr(0,4)
            }
        },
        methods : {
            save (date) {
                this.$refs.year_selected.save(date);
                this.$refs.picker.activePicker = 'YEAR'
                this.year_selected = false;
            },
            async getData(exportType){
                this.loading = await true;
                await this.getAvailmentPerType('availment-per-type', exportType);
                await this.getTotal();
                this.loading = await false;
            },
            async getAvailmentPerType(reportType, exportType){
                let filters = {
                    limit: 'showAll',
                    month: this.filters.month,
                    year: this.convertedDateFrom
                }
                const { data : item } = await axios.get(`report/${reportType}/${exportType}`+this.generateFilterURL(filters));
                this.availment_per_type = await item;
            },
            async getTotal(){
                let total_of_patient = [];
                let total_amount_availments = [];
                // 12 = 12 months
                for (let index = 0; index < 8; index++) {
                    let availments = await this.availment_per_type.reduce((acc, current) => {
                        return acc + current.patient_count[index]
                    }, 0);
                    total_of_patient[index] = await availments;
                    let amount_total = await this.availment_per_type.reduce((acc, amount) => {
                        return acc + amount.total_per_benefit[index]
                    }, 0);
                    total_amount_availments[index] = await amount_total;
                }

                let grand_total_patient = await this.availment_per_type.reduce((acc, current) => {
                    return acc + current.total_patients
                }, 0);
                let grand_total_amount = await this.availment_per_type.reduce((acc, amount) => {
                    return acc + amount.total_availments_amount
                }, 0);
                this.total = await {
                    total_of_patient,
                    total_amount_availments,
                    grand_total_patient,
                    grand_total_amount
                };
                // to avoid error: TypeError: Cannot read property '0' of undefined
                this.show = await true;
            }
        },
        mounted(){
            this.getData('index');
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";
    .availment-per-type {
        .availment-per-type__menu
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
        .availment-per-type__search
        {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            justify-content: center;
            align-self: center;
            grid-gap: 10px;
        }
    }
</style>