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
            <span></span>
            <v-btn color="tertiary" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('availment-monitoring','pdf')">Export to PDF</v-btn>
            <v-btn color="success" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('availment-monitoring','excel')">Export to Excel</v-btn>
        </div>
            <v-card flat>
                <v-data-table 
                    :headers="headers" 
                    :items="availment_monitoring" 
                    :loading="loading"
                    :pagination.sync ="pagination"
                    :total-items="table.total"
                    :rows-per-page-items="[5,15,25,35]"
                    class="elevation-1"
                >
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left">{{ props.item.name }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[0] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[0] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[1] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[1] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[2] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[2] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[3] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[3] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[4] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[4] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[5] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[5] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[6] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[6] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[7] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[7] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[8] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[8] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[9] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[9] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[10] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[10] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.no_of_patient[11] }}</td>
                        <td class="text-xs-right">{{ props.item.total_month[11] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center">{{ props.item.total_patient }}</td>
                        <td class="text-xs-right">{{ props.item.total_amount | mixin_change_currency_format}}</td>
                    </template>

                    <template slot="footer" v-if="show">
                        <td class="text-xs-left availment-monitoring--total">TOTAL</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[0] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[0] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[1] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[1] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[2] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[2] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[3] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[3] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[4] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[4] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[5] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[5] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[6] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[6] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[7] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[7] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[8] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[8] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[9] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[9] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[10] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[10] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.total_patient[11] }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[11] | mixin_change_currency_format }}</td>
                        <td class="text-xs-center availment-monitoring--total">{{ total.grand_total_patient }}</td>
                        <td class="text-xs-right availment-monitoring--total">{{ total.grand_total_amount | mixin_change_currency_format}}</td>
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
                {text: 'Availment Monitoring Report',disabled: true,}
            ],
            headers: [
                {text:'AVAILMENT TYPE' , value:'availment_type'},
                {text: 'NO. OF PATIENTS', value: 'January_patient_num'},
                {text:'JANUARY' , value:'January_total'},
                {text: 'NO. OF PATIENTS', value: 'February_patient_num'},
                {text:'FEBRUARY' , value:'February_total'},
                {text: 'NO. OF PATIENTS', value: 'March_patient_num'},
                {text:'MARCH' , value:'March_total'},
                {text: 'NO. OF PATIENTS', value: 'April_patient_num'},
                {text:'APRIL' , value:'April_total'},
                {text: 'NO. OF PATIENTS', value: 'May_patient_num'},
                {text:'MAY' , value:'May_total'},
                {text: 'NO. OF PATIENTS', value: 'June_patient_num'},
                {text:'JUNE' , value:'June_total'},
                {text: 'NO. OF PATIENTS', value: 'July_patient_num'},
                {text:'JULY' , value:'July_total'},
                {text: 'NO. OF PATIENTS', value: 'August_patient_num'},
                {text:'AUGUST' , value:'August_total'},
                {text: 'NO. OF PATIENTS', value: 'September_patient_num'},
                {text:'SEPTEMBER' , value:'September_total'},
                {text: 'NO. OF PATIENTS', value: 'October_patient_num'},
                {text:'OCTOBER' , value:'October_total'},
                {text: 'NO. OF PATIENTS', value: 'November_patient_num'},
                {text:'NOVEMBER' , value:'November_total'},
                {text: 'NO. OF PATIENTS', value: 'December_patient_num'},
                {text:'DECEMBER' , value:'December_total'},
                {text: 'TOTAL NO. OF PATIENTS', value: 'patient_total'},
                {text: 'TOTAL AMOUNT', value: 'amount_total'},
            ],
            active_tab: 'tab-0',
            availment_monitoring:[],
            provider : [],
            filters:{
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
            }
        },
        computed : {
			convertedDateFrom : function () {
                // return this.filters.date_from
                return this.filters.year.substr(0,4)
            },
        },
        methods : {
            async getData(exportType){
                this.loading = await true;
                await this.getAvailmentMonitoring('availment-monitoring', exportType);
                await this.getTotal();
                this.loading = await false;
            },
            async getAvailmentMonitoring(reportType, exportType){
                let filters = {
                    limit: 'showAll',
                    year: this.convertedDateFrom
                }
                const { data : item } = await axios.get(`report/${reportType}/${exportType}`+this.generateFilterURL(filters));
                // console.log({item});
                this.availment_monitoring = await item;
            },
            async getTotal(){
                let total_patient = [];
                let total_amount = [];
                // 12 = 12 months
                for (let index = 0; index < 12; index++) {
                    let patient = await this.availment_monitoring.reduce((acc, current) => {
                        return acc + current.no_of_patient[index]
                    }, 0);
                    total_patient[index] = await patient;
                    let amount = await this.availment_monitoring.reduce((acc, current) => {
                        return acc + current.total_month[index]
                    }, 0);
                    total_amount[index] = await amount;
                }
                let grand_total_amount = await this.availment_monitoring.reduce((acc, current) => {
                        return acc + current.total_amount
                    }, 0);
                
                 let grand_total_patient = await this.availment_monitoring.reduce((acc, current) => {
                        return acc + current.total_patient
                    }, 0);
                this.total = await {
                    total_patient,
                    total_amount,
                    grand_total_amount,
                    grand_total_patient
                }
                // to avoid error: TypeError: Cannot read property '0' of undefined
                this.show = await true;
            },
            save (date) {
                this.$refs.year_selected.save(date);
                this.$refs.picker.activePicker = 'YEAR'
                this.year_selected = false;
            }
        },
        mounted(){
            this.getData('index');
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
            grid-template-columns: repeat(5, 1fr);
            justify-content: center;
            align-self: center;
            grid-gap: 10px;
        }
    }
</style>