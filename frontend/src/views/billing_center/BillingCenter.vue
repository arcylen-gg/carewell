<template>
    <div class="billing-&-collection-center billing-center" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="billing-center__menu">
            <v-text-field prepend-inner-icon="search" class="my-0 c-input" placeholder="Search Keyword..." solo flat @change="getData({status:status,keyword:filters.name})" v-model="filters.name"></v-text-field>
            <global-select-box
                v-model="filters.company_id"
                api-link="api/company"
                label="Select Company"
                item-text="name"
                item-value="id"
                class="c-input"
                color="accent" 
                default='all'
                add-all
                is-load
                solo 
                flat
                :filters="{is_archived:0}"
                @input="getEmitData($event)"
            >
            </global-select-box>
            <span></span>
            <v-btn color="primary" @click="go_to_url('/billing-&-collection-center/create')"><v-icon>mdi-plus</v-icon> Create CAL</v-btn>
        </div>
        <v-tabs color="primary" v-model="active_tab" dark icons-and-text grow slider-color="#ffa500">
            <v-tab v-for="item in tab_items" :key="item.TabTitle" :disabled="loading" @click="getData({status:item.value})">
                {{ item.TabTitle }}
                <v-icon>{{ item.TabIcon }}</v-icon>
            </v-tab>
            <v-tab-item v-for="item in tab_items" :key="item.TabTitle"></v-tab-item>
        </v-tabs>
        <v-card flat>
            <v-data-table 
                :headers="headers" 
                :items="item" 
                :loading="loading"
                :pagination.sync="pagination"
                :total-items="table.total"
                :rows-per-page-items="[5,15,25,35]"
                class="elevation-1"
            >
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left">{{ props.item.cal_number }}</td>
                    <td class="text-xs-left">{{ props.item.company.name }}</td>
                    <td class="text-xs-left">{{ props.item.payment_cal_date | mixin_change_date_format }}</td>
                    <td class="text-xs-left">{{ props.item.payment_due_date | mixin_change_date_format }}</td>
                    <td class="text-xs-left">{{ props.item.payment_mode.name }}</td>
                    <td class="text-xs-left">{{ props.item.payment_term.name }}</td>
                    <td class="text-xs-left">{{ props.item.cal_member_count }}</td>
                    <td class="text-xs-left">{{ props.item.payment_start_date | mixin_change_date_format }}</td>
                    <td class="text-xs-left">{{ props.item.payment_end_date | mixin_change_date_format }}</td>
                    <td v-show="status == 'pending'" class="text-xs-left">{{ props.item.aging }}</td>
                    <td class="text-xs-left">   
                        <v-menu offset-overflow>
                            <v-btn 
                                slot="activator" 
                                color="tertiary" 
                                small 
                                dark 
                                depressed
                            >
                                Action
                            </v-btn>
                            <v-list>
                                <v-list-tile>
                                    <v-list-tile-title @click="printCAL(props.item.id)" style="cursor: pointer;">
                                            <v-icon>mdi-printer</v-icon> Print CAL
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-title @click="view(status,props.item.id)" style="cursor: pointer;">
                                            <v-icon>mdi-eye</v-icon> View CAL
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile>
                                    <v-list-tile-title  @click="go_to_url(`/billing-&-collection-center/edit?id=${props.item.id}`)" style="cursor: pointer;">
                                        <v-icon>mdi-pencil</v-icon> Edit CAL
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile v-if="status != 'close'">
                                    <v-list-tile-title style="cursor: pointer;" @click="member_export(props.item.company_id, props.item.payment_mode.id, props.item.id)">
                                        <v-icon>mdi-file-export</v-icon> Export Member
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile  v-if="status != 'close'">
                                    <v-list-tile-title  @click="go_to_url(`/billing-&-collection-center/import?id=${props.item.id}`)" style="cursor: pointer;">
                                            <v-icon>mdi-file-import</v-icon> Import Member
                                    </v-list-tile-title>
                                </v-list-tile>
                                <v-list-tile  v-if="status != 'close'">
                                    <v-list-tile-title @click="go_to_url(`/billing-&-collection-center/mark-close?id=${props.item.id}`)"  style="cursor: pointer;">
                                        <v-icon>mdi-close</v-icon> Mark as Close
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                        <make-as-pending :item="props.item"  v-if="status != 'close' && status != 'pending'">
                            <v-icon>mdi-arrow-bottom-left</v-icon> Mark as Pending
                        </make-as-pending>
                    </td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>

<script>
    export default {
        data: () => ({
            breadcrumbs: [
                {text: 'Dashboard',disabled: false,}, 
                {text: 'Billing Center',disabled: true,}
            ],
            headers: [
                {text: 'CAL NO.', value: 'cal_number'}, 
                {text: 'COMPANY',value: 'name'}, 
                {text: 'CAL DATE',value: 'cal_date'}, 
                {text: 'DUE DATE',value: 'due_date'}, 
                {text: 'MODE OF PAYMENT',value: 'payment_mode'},
                {text: 'TERM OF PAYMENT',value: 'payment_ter'},  
                {text: 'NO. OF MEMBERS',value: 'member_number'}, 
                {text: 'DATE CREATED',value: 'date_created'},
                {text: 'DATE END',value: 'date_end'}, 
                {text: '',value: 'action'},
            ],
            item :[],
            tab_items: [
                {TabTitle: 'OPEN',TabIcon: 'mdi-account-check' , value:'open',},
                {TabTitle: 'PENDING',TabIcon: 'mdi-menu' , value:'pending',},
                {TabTitle: 'CLOSED',TabIcon: 'mdi-close', value:'close',},
            ],
            active_tab: 'tab-0',
            filters:{},
            route : null,
            status:'open',
            loading:false,
            table : {},
            pagination : {},
        }),
        watch : {
			pagination: {
				handler () {
                    let filters = {
                        status : this.status,
                        limit : this.pagination.rowsPerPage || 5,
                        page : this.pagination.page || 1,
                        name : this.filters.name || '',
                        company_id : this.filters.company_id || 'all' ,
                    }

                    this.axiosGet(filters);
				}
            },
            status: function(newValue,oldValue){
                this.setHeader(newValue);
            }
		},
         methods : {
            getEmitData(data){
                this.filters.company_id = data;
                this.getData({status:this.status})
            },
            getData({status = 'open', keyword= ''}) {
                this.filters = {
					status : this.status = status,
					limit : this.table.per_page,
					page : this.pagination.page,
                    name : keyword,
                    company_id : this.filters.company_id || 'all'
                }
                this.axiosGet(this.filters)
            },
            axiosGet(filters){
                this.loading = true
                 axios.get('api/cal'+this.generateFilterURL(filters))
                .then(response=>{
                    this.table = response.data.data
                    this.item = response.data.data.data
                })
                .catch(error=>{
                })
                .finally(() => {
                    this.loading = false
                })
            },
            setHeader(status){
                if(status == 'pending')
                {
                    this.headers.splice(9,0,{text: 'AGING', value:'aging',})
                }
                else
                {
                    let data = this.headers;
                    data.map((item,key)=> item.text === "AGING" ? this.headers.splice(key,1) : item)
                }
            },
            view(status,id){
                if(status == 'close'){
                    this.go_to_url(`/billing-&-collection-center/mark-close-view?id=${id}`)
                }else{
                    this.go_to_url(`/billing-&-collection-center/view?id=${id}`)
                }
            },
            member_export(company_id, payment_mode_id, cal_id){
                window.open(this.$axios.defaults.baseURL+"/api/export/cal_member?company_id="+company_id+"&payment_id="+payment_mode_id+"&cal_id="+cal_id,"_blank");
            },
            printCAL(id){
                window.open(this.$axios.defaults.baseURL+`/export/cal/print?id=${id}`,"_blank");
            }
         },
        mounted(){
        
            EventBus.$on('markaspending', (data)=> {
                this.getData({status:this.status})
            });
            this.route = this.get_page_access(this.pages, this.$route.name)
        }
    }
</script>

<style lang="scss">
    @import "../../scss/app.scss";
    .billing-center {
        .v-datatable thead th.column.sortable
        {
            color: #000 !important;
        }
        table.v-table tbody td
        {
            color: #000;
        }
        .billing-center__menu
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
            //     // justify-self: end;
            // }
        }
    }
</style>
