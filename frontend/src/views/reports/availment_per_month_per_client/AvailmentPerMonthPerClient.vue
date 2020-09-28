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
            <v-btn color="tertiary" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('availment-per-month-per-client','pdf')">Export to PDF</v-btn>
            <v-btn color="success" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('availment-per-month-per-client','excel')">Export to Excel</v-btn>
        </div>
        <v-card flat>
            <v-data-table 
                :headers="headers" 
                :items="items" 
                :loading="loading"
                :pagination.sync ="pagination"
                :total-items="table.total"
                class="elevation-1"
            >
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left">{{ props.item.name }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[0] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[0] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[1] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[1] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[2] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[2] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[3] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[3] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[4] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[4] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[5] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[5] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[6] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[6] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[7] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[7] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[8] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[8] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[9] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[9] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[10] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[10] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_count[11] }}</td>
                    <td class="text-xs-right">{{ props.item.monthly_total[11] | mixin_change_currency_format }}</td>
                    <td class="text-xs-right">{{ props.item.year_total | mixin_change_currency_format}}</td>
                </template>

                <template slot="footer" v-if="show">
                    <td class="text-xs-left availment-monitoring--total">TOTAL</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[0] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[0] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[1] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[1] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[2] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[2] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[3] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[3] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[4] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[4] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[5] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[5] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[6] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[6] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[7] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[7] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[8] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[8] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[9] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[9] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[10] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[10] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[11] }}</td>
                    <td class="text-xs-right availment-monitoring--total">{{ total.total_amount[11] | mixin_change_currency_format }}</td>
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
                {text: 'Availment Per Month Per Client',disabled: true,}
            ],
            headers: [
                {text:'Company' , value:'company'},
                {text:'January Count' , value:'January_count'},
                {text: 'January Availment', value: 'January_total'},
                {text:'February Count' , value:'February_count'},
                {text: 'February Availment', value: 'February_total'},
                {text:'March Count' , value:'March_count'},
                {text: 'March Availment', value: 'March_total'},
                {text:'April Count' , value:'April_count'},
                {text: 'April Availment', value: 'April_total'},
                {text:'May Count' , value:'May_count'},
                {text: 'May Availment', value: 'May_total'},
                {text:'June Count' , value:'June_count'},
                {text: 'June Availment', value: 'June_total'},
                {text:'July Count' , value:'July_count'},
                {text: 'July Availment', value: 'July_total'},
                {text:'August Count' , value:'August_count'},
                {text: 'August Availment', value: 'August_total'},
                {text:'September Count' , value:'September_count'},
                {text: 'September Availment', value: 'September_total'},
                {text:'October Count' , value:'October_count'},
                {text: 'October Availment', value: 'October_total'},
                {text:'November Count' , value:'November_count'},
                {text: 'November Availment', value: 'November_total'},
                {text:'December Count' , value:'December_count'},
                {text: 'December Availment', value: 'December_total'},
                {text: 'YTD', value: 'year_to_date'},
            ],
            active_tab: 'tab-0',
            items:[],
            provider : [],
            filters:{
                year: new Date().toISOString().substr(0,10)
            },
            route : null,
            status:'new',
            pagination: {
                rowsPerPage : -1,
            },
            table : {},
            loading : false,
            year_selected: false,
            show: false,
            total:{},
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
                await this.getAvailmentMonitoring('availment-per-month-per-client', exportType);
                this.loading = await false;
                this.show = await true;
            },
            async getAvailmentMonitoring(reportType, exportType){
                let filters = {
                    limit: 'showAll',
                    year: this.convertedDateFrom
                }
                const { data : item } = await axios.get(`report/${reportType}/${exportType}`+this.generateFilterURL(filters));
                console.log({item})
                this.items = await item.item;
                this.total = await {
                    total_count: item.overall_monthly_count,
                    total_amount: item.overall_monthly_total,
                    grand_total_amount: item.overall_grand_total
                }
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

        .availment-monitoring--total {
            font-weight: bolder
        }
    }
</style>