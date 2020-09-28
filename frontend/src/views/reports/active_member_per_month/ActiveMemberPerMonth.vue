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
            <v-btn color="tertiary" depressed dark type="submit">Export to PDF</v-btn>
            <v-btn color="success" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('availment-monitoring','excel')">Export to Excel</v-btn>
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
                    <td class="text-xs-left">{{ props.item.premium }}</td>
                    <td class="text-xs-left">{{ props.item.policy_effective_date }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[1] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[2] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[3] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[4] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[5] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[6] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[7] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[8] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[9] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[10] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[11] }}</td>
                    <td class="text-xs-center">{{ props.item.per_month_count[12] }}</td>
                    <td class="text-xs-center">{{ props.item.total_per_client }}</td>
                </template>

                <template slot="footer" v-if="show">
                    <td class="text-xs-left availment-monitoring--total" colspan="3">TOTAL</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[1] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[2] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[3] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[4] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[5] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[6] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[7] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[8] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[9] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[10] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[11] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_count[12] }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.grand_total_count }}</td>
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
                {text: 'Active Member Per Month',disabled: true,}
            ],
            headers: [
                {text:'Company' , value:'company'},
                {text:'Prem' , value:'coverage_plan_premium'},
                {text:'Date Acquired' , value:'coverage_plan_premium'},
                {text:'January' , value:'January_count'},
                {text:'February' , value:'February_count'},
                {text:'March' , value:'March_count'},
                {text:'April' , value:'April_count'},
                {text:'May' , value:'May_count'},
                {text:'June' , value:'June_count'},
                {text:'July' , value:'July_count'},
                {text:'August' , value:'August_count'},
                {text:'September' , value:'September_count'},
                {text:'October' , value:'October_count'},
                {text:'November' , value:'November_count'},
                {text:'December' , value:'December_count'},
                {text: 'Total', value: 'year_to_date'},
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
                await this.getActiveMemberPerMonth('active-member-per-month', exportType);
                this.loading = await false;
                this.show = await true;
            },
            async getActiveMemberPerMonth(reportType, exportType){
                let filters = {
                    limit: 'showAll',
                    year: this.convertedDateFrom
                }
                const { data : item } = await axios.get(`report/${reportType}/${exportType}`+this.generateFilterURL(filters));
                console.log({item})
                this.items = await item.active_member;
                this.total = await {
                    total_count: item.total_per_month,
                    grand_total_count: item.grand_total
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