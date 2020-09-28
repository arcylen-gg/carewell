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

            <company-table :company_list="company_record"></company-table>
            
            <v-btn color="accent" depressed type="submit" @click="getData('index')">Run Report</v-btn>

            <span></span>

            <v-btn color="tertiary" depressed dark type="submit" @click="exportFile('availment-summary', 'pdf')">Export to PDF</v-btn>

            <v-btn color="success" depressed dark type="submit" @click="exportFile('availment-summary', 'excel')">Export to Excel</v-btn>
            <!-- <v-btn color="accent" depressed type="submit" @click="REPORT_MIXINS_GET_DATA('company-availment','index')">Company Availment</v-btn>
            <v-btn color="accent" depressed type="submit" @click="REPORT_MIXINS_GET_DATA('availment-monitoring','index')">Testing</v-btn> -->
        </div>
            <v-card flat>
                <v-data-table 
                    :headers="headers" 
                    :items="availment_summary" 
                    :loading="loading"
                    :pagination.sync ="pagination"
                    :total-items="table.total"
                    :rows-per-page-items="[5,15,25,35]"
                    class="elevation-1"
                >
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left">{{props.item.name}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[0]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[1]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[2]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[3]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[4]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[5]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[6]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[7]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[8]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[9]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[10]}}</td>
                        <td class="text-xs-left">{{props.item.no_of_availments[11]}}</td>
                        <td class="text-xs-left">{{props.item.total_availments}}</td>
                    </template>

                    <template slot="footer" v-if="show">
                        <td class="text-xs-left availment-summary--total"><strong>TOTAL</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[0]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[1]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[2]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[3]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[4]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[5]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[6]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[7]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[8]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[9]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[10]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.total_of_availments[11]}}</strong></td>
                        <td class="text-xs-left availment-summary--total"><strong>{{total.grand_total_availments}}</strong></td>
                    </template>
                </v-data-table>
            </v-card>
    </div>
</template>

<script>
import ReportMixins from '../js/ReportMixins';

    export default {
        mixins : [ ReportMixins ],
        data: () => ({
            breadcrumbs: [
                {text: 'Dashboard',disabled: false,}, 
                {text: 'Reports', disabled: false,},
                {text: 'Availment Summary Report',disabled: true,}
            ],
            headers: [
                {text: 'COMPANY', value: 'company_name'}, 
                {text: 'JANUARY',value: 'jan_total'}, 
                {text: 'FEBRUARY',value: 'feb_total'}, 
                {text: 'MARCH',value: 'mar_total'}, 
                {text: 'APRIL',value: 'apr_total'}, 
                {text: 'MAY',value: 'may_total'}, 
                {text: 'JUNE',value: 'jun_total'}, 
                {text: 'JULY',value: 'jul_total'},
                {text: 'AUGUST',value: 'aug_total'},
                {text: 'SEPTEMBER',value: 'sep_total'},
                {text: 'OCTOBER',value: 'oct_total'},
                {text: 'NOVEMBER',value: 'nov_total'},
                {text: 'DECEMBER',value: 'dec_total'},
                {text: 'TOTAL',value: 'total'}
            ],
            active_tab: 'tab-0',
            availment_summary : [],
            company_record : [],
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
                await this.getAvailmentSummary('availment-summary', exportType);
                await this.getTotal(this.availment_summary)
                this.loading = await false;
            },
            async getAvailmentSummary(reportType, exportType, filter_record = []){
                let filters = {
                    limit: 'showAll',
                    year: this.convertedDateFrom,
                    company_id : JSON.stringify(this.filters.company_id) || ''
                }
                const { data : item } = await axios.get(`report/${reportType}/${exportType}`+this.generateFilterURL(filters));
                this.availment_summary = await item;
            },
            async getTotal(data){
                let total_of_availments = [];
                // 12 = 12 months
                for (let index = 0; index < 12; index++) {
                    let availments = await data.reduce((acc, current) => {
                        return acc + current.no_of_availments[index]
                    }, 0);
                    total_of_availments[index] = await availments;
                }

                let grand_total_availments = await data.reduce((acc, current) => {
                        return acc + current.total_availments
                    }, 0);
                this.total = await {
                    total_of_availments,
                    grand_total_availments
                };
                // to avoid error: TypeError: Cannot read property '0' of undefined
                this.show = await true;
            },
            exportFile(reportType, exportType)
            {
                let filters = {
                    limit: 'showAll',
                    year: this.convertedDateFrom,
                    company_id : JSON.stringify(this.filters.company_id) || ''
                }
                window.open(this.$axios.defaults.baseURL+`/report/${reportType}/${exportType}`+this.generateFilterURL(filters),"_blank");
            },
            async filterRecord()
            {
                await this.getAvailmentSummary('availment-summary', 'index');
                this.company_record = this.availment_summary;
            }
        },
        mounted(){
            this.getData('index');
            this.filterRecord();
            EventBus.$on('sortCompany', (data)=> {
                this.filters.company_id = data;
            })
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
            grid-template-columns: repeat(6, 1fr);
            justify-content: center;
            align-self: center;
            grid-gap: 10px;
        }
    }
</style>