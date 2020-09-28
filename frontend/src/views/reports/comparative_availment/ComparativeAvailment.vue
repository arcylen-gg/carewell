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
                ref="datepicker_from"
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
                    v-model="convertedDateFrom"
                    label="Year From..."
                    append-icon="event"
                    outline
                    readonly
                    ></v-text-field>
                <v-date-picker reactive no-title v-model="filters.date_from" ref="picker" @input="save" :max="new Date().toISOString().substr(0, 4)" min="1950-01-01"></v-date-picker>
            </v-menu>
            
            <v-menu
                ref="datepicker_to"
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
                    v-model="convertedDateTo"
                    label="Date To..."
                    append-icon="event"
                    outline
                    readonly
                ></v-text-field>
                <v-date-picker reactive no-title v-model="filters.date_to" ref="picker" :max="new Date().toISOString().substr(0, 4)" @input="save" min="1950-01-01"></v-date-picker>
            </v-menu>
            
            <v-btn color="accent" depressed type="submit" @click="getData('index')">Run Report</v-btn>
            <!-- <span></span> -->
            <v-btn color="tertiary" depressed dark type="submit"  @click="exportData('pdf')">Export to PDF</v-btn>
            <v-btn color="success" depressed dark type="submit" @click="exportData('excel')">Export to Excel</v-btn>
        </div>
        <v-card flat :key="component_key">
            <v-data-table 
                :headers="headers" 
                :items="item" 
                :loading="loading"
                :pagination.sync ="pagination"
                class="elevation-1"
            >
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left">{{ props.item.name }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[0] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[0] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[1] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[1] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[2] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[2] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[3] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[3] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[4] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[4] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[5] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[5] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[6] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[6] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[7] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[7] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[8] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[8] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[9] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[9] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[10] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[10] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_from[11] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_monthly_to[11] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_year_from | mixin_change_currency_format }}</td>
                    <td class="text-xs-center">{{ props.item.count_year_to | mixin_change_currency_format }}</td>
                </template>
                <template slot="footer" v-if="show">
                    <td class="text-xs-left availment-monitoring--total">TOTAL</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[0] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[0] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[1] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[1] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[2] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[2] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[3] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[3] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[4] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[4] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[5] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[5] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[6] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[6] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[7] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[7] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[8] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[8] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[9] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[9] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[10] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[10] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.from[11] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.to[11] | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_from | mixin_change_currency_format }}</td>
                    <td class="text-xs-center availment-monitoring--total">{{ total.total_to | mixin_change_currency_format }}</td>
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
                {text: 'Comparative Availment Per Month Per Client',disabled: true,}
            ],
            headers: [],
            active_tab: 'tab-0',
            item:[],
            provider : [],
            filters:{
                date_from: new Date().toISOString().substr(0,10),
                date_to: new Date().toISOString().substr(0,10),
            },
            route : null,
            status:'new',
            pagination: {
                rowsPerPage : -1,
            },
            table : {},
            loading : false,
            datepicker_from: false,
            datepicker_to: false,
            total: {},
            show: false,
            months:['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            component_key: 0
        }),
        watch: {
            datepicker_from (val) {
                val && this.$nextTick(() => (this.$refs.picker.activePicker = 'YEAR'))
            },
            datepicker_to (val) {
                val && this.$nextTick(() => (this.$refs.picker.activePicker = 'YEAR'))
            }
        },
        computed : {
            convertedDateFrom : function () {
                // return this.filters.date_from
                return this.filters.date_from.substr(0,4)

            },
            convertedDateTo : function () {
                // return this.filters.date_to;
                return this.filters.date_to.substr(0,4)

            },
        },
        methods: {
            save (date) {
                this.$refs.datepicker_from.save(date);
                this.$refs.datepicker_to.save(date);
                this.$refs.picker.activePicker = 'YEAR'
                this.$refs.picker.activePicker = 'YEAR'
                this.datepicker_from = false;
                this.datepicker_to = false;
            },
            exportData(exportType){
                let filters = {
                    limit: 'showAll',
                    year_from: this.convertedDateFrom,
                    year_to: this.convertedDateTo
                }
                
                window.open(this.$axios.defaults.baseURL+`/report/comparative-availment/${exportType}`+this.generateFilterURL(filters),"_blank");
            },
            async getData(exportType){
                await this.setHeader();
                this.component_key++
                // console.log(this.component_key)
                this.loading = await true;
                await this.getRecord('comparative-availment', exportType);
                this.show = await true;
                this.loading = await false;
            },
            async getRecord(reportType, exportType){
                let filters = {
                    limit: 'showAll',
                    year_from: this.convertedDateFrom,
                    year_to: this.convertedDateTo
                }
                const data = await axios.get(`report/${reportType}/${exportType}`+this.generateFilterURL(filters));

                this.item = await data.data.item;

                this.total = await {
                    from: data.data.from,
                    to: data.data.to,
                    total_from: data.data.total_from,
                    total_to: data.data.total_to
                }
            },
            async setHeader(){
                this.headers = await [];
                
                await this.headers.push({text: 'Company', value: 'company'});

                await this.months.map((item, key) => {
                    this.headers.push({text : `${item} ${this.convertedDateFrom}`, value: `${item}_${this.convertedDateFrom}`});
                    if(this.convertedDateFrom === this.convertedDateTo){
                        this.headers.push({text : `${item} ${this.convertedDateTo}`, value: `${item}_${this.convertedDateTo}_${key}`});
                    } else {
                        this.headers.push({text : `${item} ${this.convertedDateTo}`, value: `${item} ${this.convertedDateTo}`});
                    }
                });

                await this.headers.push(
                    {text : `Grand Total ${this.convertedDateFrom}`, value: 'grand_total_from'}, 
                    {text : `Grand Total ${this.convertedDateTo}`, value: 'grand_total_to'}
                );
                
                console.log(this.headers)
            }
        },
        async mounted(){
            await this.setHeader();
            await this.getData('index');
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