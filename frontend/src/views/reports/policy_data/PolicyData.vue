<template>
    <div class="policy-data">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
         <div class="policy-data__menu">
            <span></span>
            <span></span>
        </div>
        <div class="policy-data__search">
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
            
            <v-btn color="accent" depressed type="submit" @click="REPORT_MIXINS_GET_DATA('policy-data','index')">Run Report</v-btn>
            <span></span>
            <v-btn color="tertiary" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('policy-data','pdf')">Export to PDF</v-btn>
            <v-btn color="success" depressed dark type="submit" @click="REPORT_MIXINS_GET_DATA('policy-data','excel')">Export to Excel</v-btn>
            <!-- <v-btn color="accent" depressed type="submit" @click="REPORT_MIXINS_GET_DATA('company-availment','index')">Company Availment</v-btn>
            <v-btn color="accent" depressed type="submit" @click="REPORT_MIXINS_GET_DATA('availment-monitoring','index')">Testing</v-btn> -->
        </div>
            <v-card flat>
                <v-data-table 
                    :headers="headers" 
                    :items="policy_data" 
                    :loading="loading"
                    :pagination.sync ="pagination"
                    :total-items="table.total"
                    :rows-per-page-items="[5,15,25,35]"
                    class="elevation-1"
                >
                    <v-progress-linear slot="progress" color="grey" indeterminate></v-progress-linear>
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left">{{props.item.account_type}}</td>
                        <td class="text-xs-left">{{props.item.plan_name}}</td>
                        <td class="text-xs-left">{{props.item.policy_number}}</td>
                        <td class="text-xs-left">{{props.item.policy_effective_date | mixin_change_date_format}}</td>
                        <td class="text-xs-left">{{props.item.policy_expiry_date | mixin_change_date_format}}</td>
                        <td class="text-xs-left">{{props.item.member_number}}</td>
                        <td class="text-xs-left">{{props.item.member_type}}</td>
                        <td class="text-xs-left">{{props.item.gender}}</td>
                        <td class="text-xs-left">{{props.item.birth_date | mixin_change_date_format}}</td>
                        <td class="text-xs-left">{{props.item.payment_mode}}</td>
                        <td class="text-xs-left">{{props.item.member_effective_date | mixin_change_date_format}}</td>
                        <td class="text-xs-left">{{props.item.member_expiry_date | mixin_change_date_format}}</td>
                        <td class="text-xs-left">{{props.item.bill_from | mixin_change_date_format}}</td>
                        <td class="text-xs-left">{{props.item.bill_to | mixin_change_date_format}}</td>
                        <td class="text-xs-left">{{props.item.gross_membership_fee | mixin_change_currency_format}}</td>
                        <td class="text-xs-left">{{props.item.net_membership_fee | mixin_change_currency_format}}</td>
                        <td class="text-xs-left"></td>
                        <td class="text-xs-left">PHP</td>
                        <td class="text-xs-left">PHP</td>
                        <td class="text-xs-left"></td>
                        <td class="text-xs-left"></td>
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
                {text: 'Policy Data Report',disabled: true,}
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
            headers: [
                {text: 'TYPE OF ACCOUNT', value: 'account_type'}, 
                {text: 'PLAN NAME',value: 'coverage_plan_name'}, 
                {text: 'POLICY NUMBER',value: 'company_code'}, 
                {text: 'POLICY EFFECTIVE DATE',value: ''}, 
                {text: 'POLICY EXPIRY DATE',value: ''}, 
                {text: 'MEMBER NUMBER',value: 'carewell_id'}, 
                {text: 'MEMBER TYPE',value: 'member_type'},
                {text: 'SEX',value: 'gender'}, 
                {text: 'DATE OF BIRTH',value: 'birth_date'},
                {text: 'PAYMENT MODE',value: 'payment_mode_id'},
                {text: 'MEMBER EFFECTIVE DATE',value: 'member_effective_date'},
                {text: 'MEMBER EXPIRY DATE',value: 'member_expiry_date'},
                {text: 'BILL FROM DATE',value: 'cal_date'},
                {text: 'BILL TO DATE',value: 'cal_due_date'},
                {text: 'GROSS MEMBERSHIP FEE',value: 'monthly_premium'},
                {text: 'NET MEMBERSHIP FEE',value: 'net_premium'},
                {text: 'COLLECTION FEE',value: ''},
                {text: 'CURRENCY OF MEMBERSHIP FEE PAID',value: ''},
                {text: 'CURRENCY OF PLAN',value: ''},
                {text: 'UNEARNED MEMBERSHIP FEE RESERVES',value: ''},
                {text: 'TOTAL RESERVES',value: ''},
            ],
            active_tab: 'tab-0',
            policy_data:[],
            provider : [],
            filters:{},
            route : null,
            status: 0,
            pagination: {},
            table : {},
            loading : false,
            datepicker_from: false,
            datepicker_to: false,
        }),
        watch : {
			pagination: {
				handler () {
                    let filters = {
                        is_archived : this.status,
                        limit : this.pagination.rowsPerPage || 5,
                        page : this.pagination.page || 1,
                        name : this.filters.name || '',
						company_id : this.filters.company_id || 'all',
                    }

					this.axiosGet(filters, 'policy-data','index')
				}
			}
		},
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";
    .policy-data {
        .policy-data__menu
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
        .policy-data__search
        {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            justify-content: center;
            align-self: center;
            grid-gap: 10px;
        }
    }
</style>