<template>
    <div class="availment-center" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
         <div class="availment-center__menu">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="availment-center__search">
            <v-text-field prepend-inner-icon="search" class="my-0 c-input" placeholder="Search Keyword..." solo flat v-model="filters.name"></v-text-field>

            <global-select-box
                class="c-input" 
                color="accent" 
                label="Filter by Company" 
                v-model="filters.company_id"
                default="all"
                item-text="name"
                item-value="id"
                api-link="api/company"
                add-all
                :filters="{is_archived:0}"
                is-load
                solo 
                flat
            ></global-select-box> 

            <global-select-box
                class="c-input" 
                color="accent" 
                label="Filter by Provider" 
                v-model="filters.provider_id"
                default="all"
                item-text="name"
                item-value="id"
                api-link="api/provider"
                add-all
                :filters="{is_archived:0}"
                is-load
                solo 
                flat
            ></global-select-box> 

            <!-- <v-menu
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
            </v-menu> -->
            
            <!-- <v-menu
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
            </v-menu> -->
            <v-btn color="accent" depressed type="submit" @click="getData({status:status,keyword:filters})">Run Report</v-btn>
            <!-- <v-btn color="primary" @click="go_to_url('/availment-center/create')"><v-icon>mdi-plus</v-icon>Create New Availment</v-btn> -->
            <div class="dropdown">
                <b-dropdown style="width: 200px; height: 47px ;" id="ddown-right" right text="Operations" class="m-0" >
                <b-dropdown-item-button @click="go_to_url('/availment-center/create')">
                    <v-icon class="mr-2">mdi-account-multiple-plus</v-icon>
                    Create New Availment
                </b-dropdown-item-button>
                <b-dropdown-item-button @click="go_to_url('/availment-center/import')">
                    <v-icon class="mr-2">mdi-file-import</v-icon>
                    Import Availment
                </b-dropdown-item-button>
                </b-dropdown>
            </div>
        </div>
        <v-tabs color="primary" dark icons-and-text grow show-arrows v-model="active_tab" slider-color="#ffa500">
            <v-tab v-for="(tabItem,index) in tab_items" :key="index" :href="`#tab-${index}`" @change="getData({status:tabItem.value})">
                {{ tabItem.TabTitle }}
                <v-icon>{{ tabItem.TabIcon }}</v-icon>
            </v-tab>
        </v-tabs>
        <v-tabs-items v-model="active_tab">
            <v-tab-item
                v-for="(tabItem,index) in tab_items"
                :value="`tab-${index}`"
                :key="index"
            >
                <v-card flat>
                    <v-data-table 
                        :headers="headers" 
                        :items="item" 
                        :loading="loading"
						:pagination.sync ="pagination"
						:total-items="table.total"
						:rows-per-page-items="[5,15,25,35]"
                        class="elevation-1">
                        <template slot="items" slot-scope="props">
                            <!-- <td>{{ props.index }}</td> -->
                            <td class="text-xs-left">{{ props.item.approval_id }}</td>
                            <td class="text-xs-left">{{ props.item.member.company.universal_id }}</td>
                            <td class="text-xs-left">{{ props.item.carewell_id }}</td>
                            <td class="text-xs-left">{{ props.item.member.fullname }}</td>
                            <td class="text-xs-left">{{ props.item.member.company.company.name }}</td>
                            <td class="text-xs-left">{{ props.item.provider.name }}</td>
                            <td class="text-xs-left">{{ props.item.date_avail | mixin_change_date_format }} </td>
                            <td class="text-xs-right">{{ props.item.grand_total | mixin_change_currency_format }} </td>
                            <td class="text-xs-left" style="padding: 0px">
                                <v-menu offset-y>
                                    <v-btn slot="activator" color="tertiary" small dark depressed>
                                        Action
                                    </v-btn>
                                    <v-list>
                                        <v-list-tile>
                                            <v-list-tile-title @click="printAvailment(props.item.id)" style="cursor: pointer;">
                                                <v-icon>mdi-printer</v-icon> Print Availment
                                            </v-list-tile-title>
                                        </v-list-tile>
                                        <v-list-tile v-if="route.accesses.create">
                                            <v-list-tile-title @click="go_to_url(`/availment-center/view?id=${props.item.id}`)" style="cursor: pointer;">
                                                <v-icon>mdi-eye</v-icon> View Availment
                                            </v-list-tile-title>
                                        </v-list-tile>
                                        <v-list-tile v-if="route.accesses.edit && index == 0">
                                            <v-list-tile-title @click="go_to_url(`/availment-center/edit?id=${props.item.id}`)" style="cursor: pointer;">
                                                <v-icon>mdi-pencil</v-icon> Edit Availment 
                                            </v-list-tile-title>
                                        </v-list-tile>
                                    </v-list>
                                </v-menu>
                                <disapproved-availment v-if="active_tab != 'tab-2'" :form="props.item"><v-icon color="tertiary">mdi-close</v-icon>Disapproved</disapproved-availment>
                            </td>
                        </template>
                    </v-data-table>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
    </div>
</template>

<script>
    export default {
        data: () => ({
            breadcrumbs: [
                {text: 'Dashboard',disabled: false,}, 
                {text: 'Availment Center',disabled: true,}
            ],
            headers: [
                {text: 'APPROVAL NO.', value: 'approval_number', sortable: false}, 
                {text: 'UNIVERSAL ID',value: 'universal_id', sortable: false}, 
                {text: 'CAREWELL ID',value: 'carewell_id', sortable: false}, 
                {text: 'MEMBER NAME',value: 'member_name', sortable: false}, 
                {text: 'COMPANY NAME',value: 'company', sortable: false}, 
                {text: 'PROVIDER NAME',value: 'provider', sortable: false}, 
                {text: 'DATE ISSUED',value: 'date_issued', sortable: false}, 
                {text: 'GRAND TOTAL',value: 'grand_total', sortable: false}, 
                {text: 'ACTION',value: 'action', width: "1%", sortable: false},
            ], 
            tab_items: [
                {TabTitle: 'D&U',TabIcon: 'mdi-account-plus' , value:'new',},
                {TabTitle: 'ICOS',TabIcon: 'mdi-account-check' , value:'open',},
                {TabTitle: 'DISAPPROVED',TabIcon: 'mdi-account-remove' , value:'disapproved',},
                {TabTitle: 'RESISTED CLAIMS',TabIcon: 'mdi-account-alert', value:'resisted_claims',},
                {TabTitle: 'CANCELED',TabIcon: 'mdi-account-off' , value:'cancelled',},
                {TabTitle: 'PAID',TabIcon: 'mdi-coin', value: 'close'},
                {TabTitle: 'IBNR',TabIcon: 'mdi-close', value:'ibnr',},
            ],
            active_tab: 'tab-0',
            item:[],
            filters:{},
            route : null,
            status:'new',
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
						status : this.status,
						limit : this.pagination.rowsPerPage || 5,
						page : this.pagination.page || 1,
                        name : this.filters.name || '',
                        provider_id : this.filters.provider_id || 'all',
                        company_id : this.filters.company_id || 'all',
                        // date_from : this.filters.date_from || '',
                        // date_to : this.filters.date_to || '',
					}

					this.axiosGet(filters);
				}
        	},
		},
        methods : {
            getData({status = 'new', keyword = ''}){
				this.filters = {
					status : this.status = status,
					limit : this.table.per_page || 5,
					page : this.pagination.page,
                    name : keyword.name || '',
                    provider_id : keyword.provider_id || 'all',
                    company_id : keyword.company_id || 'all',
                    // date_from : keyword.date_from,
                    // date_to : keyword.date_to,
                }
                this.axiosGet(this.filters)
			},
            axiosGet(filters){
                this.loading = true
                axios.get('api/availment'+this.generateFilterURL(filters))
                .then(response=>{
                    console.log({response})
                    this.table = response.data.data
                    this.item = response.data.data.data
                })
                .catch(error=>{

                })
                .finally(()=>{
                    this.loading = false
                })
            },
            printAvailment(id){
                window.open(this.$axios.defaults.baseURL+`/export/availment/print?id=${id}`,"_blank");
            }
        },
        mounted(){
            this.route = this.get_page_access(this.pages, this.$route.name)

            EventBus.$on('createAvailment', (data)=> {
                this.getData({});
            });
            EventBus.$on('editAvailment', (data)=> {
                this.getData({});
            });
            EventBus.$on('disapprovedAvailment', (data)=> {
                this.getData({});
            })
        }
    }
</script>

<style lang="scss">
    @import "../../scss/app.scss";
    .availment-center {
        .v-datatable thead th.column.sortable
        {
            color: #000 !important;
        }
        table.v-table tbody td
        {
            color: #000;
        }
        .availment-center__menu
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
            // :nth-child(3), :nth-child(4)
            // {
            //      justify-self: end;
            // }
        }
        .availment-center__search
        {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            justify-content: center;
            align-self: center;
            grid-gap: 10px;

            .dropdown {
                justify-self: end;
            }
        }
    }
</style>
