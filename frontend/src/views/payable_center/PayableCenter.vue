<template>
    <div class="procedure-type" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="procedure-type__menu">
            <v-text-field prepend-inner-icon="search" class="my-0 c-input" placeholder="Search Keyword..." solo flat @change="getData({status:status, keyword:filters.name})" v-model="filters.name"></v-text-field>
            <global-select-box
                v-model="filters.provider_id"
                :filters={is_archived:0}
                :default="'all'"
                item-text="name"
                item-value="id"
                api-link="api/provider"
                label="Select Provider"
                add-all
                is-load
                solo
                flat
                @input="getEmitData($event)"
		    >
            </global-select-box>
            <span></span>
             <v-btn color="primary" @click="go_to_url('/payable-center/create')"><v-icon>mdi-plus</v-icon> Create Payable</v-btn>
        </div>
        <v-tabs color="primary" v-model="active_tab" dark icons-and-text grow slider-color="#ffa500">
            <v-tab v-for="item in tab_items" :key="item.TabTitle" :disabled="loading" @click="getData({status:item.value})">
                {{ item.TabTitle }}
                <v-icon>{{ item.TabIcon }}</v-icon>
            </v-tab>
            <v-tab-item v-for="item in tab_items" :key="item.TabTitle"></v-tab-item>
        </v-tabs>
            <v-data-table 
            :headers="headers" 
            :items="list" 
            :loading="loading"
            :pagination.sync="pagination"
            :total-items="table.total"
            :rows-per-page-items="[5,15,25,35]"
            class="elevation-1">
                <v-progress-linear slot="progress" color="grey" indeterminate></v-progress-linear>
                <template slot="items" slot-scope="props">
                <td class="text-xs-center">{{ props.item.provider.name }}</td>
                <td class="text-xs-center">{{ props.item.soa_number }}</td>
                <td class="text-xs-center">{{ props.item.received_date | mixin_change_date_format }}</td>
                <td class="text-xs-center">{{ props.item.due_date | mixin_change_date_format }}</td>
                <td class="text-xs-center">{{ props.item.payable_availment.length }}</td>
                <td class="text-xs-center">{{ props.item.user.full_name }}</td>
                <td class="text-xs-center">{{ props.item.created_at | mixin_change_date_format }}</td>
                <td class="text-xs-center">
                    <v-select
                        :items="select_action"
                        label="Select"
                        @change="actionSelected($event, status,props.item.id)"
                    >
                    </v-select>
                </td>
            </template>
        </v-data-table>
    </div>
</template>


<script>
import CreatePayable from './popup/CreatePayable';
    export default {
        components : {
            CreatePayable,
        },
        data: () => ({
            active_tab: 'tab-0',
            item:[],
            breadcrumbs: [
                {text: 'Dashboard', disabled: false,}, 
                {text: 'Payable Center',disabled: true,},
            ],
            filters:{},
            loading: false,
            headers: [
                {text: 'PROVIDER',value: 'provider_id'}, 
                {text: 'SOA NUMBER',value: 'soa_number'}, 
                {text: 'DATE RECEIVED',value: 'date_received'},
                {text: 'DUE DATE',value: 'due_date'}, 
                {text: 'APPROVAL NUMBER',value: 'approval_number'}, 
                {text: 'PREPARED BY',value: 'prepared_by'},
                {text: 'PREPARATION DATE',value: 'preparation_date'}, 
                {text: 'ACTION',value: 'action', width:"250"},
            ],
            list:[],
            status:'open',
            route : null,
            tab_items: [
                {TabTitle: 'OPEN',value: 'open',TabIcon: 'mdi-account-check'}, 
                {TabTitle: 'CLOSE',value: 'close',TabIcon: 'mdi-account-minus'},
                {TabTitle: 'DRAFT',value: 'draft',TabIcon: 'mdi-account-alert'},
            ],
            pagination : {},
            table : {},
            select_action : [
                "Version-1",
                "Version-2",
                "Version-3",
                "Version-4",
                "View Payable",
                "Edit Payable",
                "Mark as Close",
            ]
        }),
        watch: {
            pagination: {
                handler () {
                    let filters = {
						status : this.status,
						limit : this.pagination.rowsPerPage || 5,
						page : this.pagination.page || 1,
                        name : this.filters.name || '',
                        provider_id : this.filters.provider_id || 'all'
					}

                    this.axiosGet(filters);
                },
                deep: true
            },
            status : function(newValue,oldValue){
               this.select_action = new Array;

               if(newValue === "close")
               {
                   this.select_action = [
                       "View Mark as Close",
                       "Edit Mark as Close"
                   ]
               }
               else if (newValue === "draft")
               {
                   this.select_action = [
                       "Edit Draft"
                   ]
               }
               else
               {
                   this.select_action = [
                        "Print for Internal Reports",
                        "Print for External Reports",
                        "Print for Hospital/ Provider",
                        "Print for Doctors",
                        "Print SOA with Payable to Hospital and per Doctor",
                        "View Payable",
                        "Edit Payable",
                        "Mark as Close",
                   ]
               }
            }
        },
        methods : {
            getEmitData(data){
                this.filters.provider_id = data;
                this.getData({status:this.status,keyword:this.filters});
                return;
            },
            actionSelected(event, status,payable_id){

                if(status === 'open')
                {
                    if(event === "Mark as Close")
                    {
                        this.go_to_url('/payable-center/mark-close?id='+payable_id);
                    }
                    else if(event === "Edit Payable")
                    {
                        this.go_to_url('/payable-center/edit?id='+payable_id);
                    }
                    else if(event === "View Payable")
                    {
                        this.go_to_url('/payable-center/view?id='+payable_id);
                    }
                    else if(
                        event === "Version-1" ||  
                        event === "Version-2" || 
                        event === "Version-3" || 
                        event === "Version-4" )
                    {
                        window.open(this.$axios.defaults.baseURL+`/export/payable/print?id=${payable_id}&report-type=${event}`,"_blank")
                    }
                }
                else if(status === "close")
                {
                    if(event === "View Mark as Close")
                    {  
                        this.go_to_url('/payable-center/mark-close?id='+payable_id+'&status=view');
                    }
                    else if(event === "Edit Mark as Close")
                    {
                        this.go_to_url('/payable-center/mark-close?id='+payable_id+'&status=edit');
                    }
                }
                else if(status === "draft")
                {
                    if(event === "Edit Draft")
                    {
                        this.go_to_url('/payable-center/mark-close?id='+payable_id+'&status=edit');
                    }
                }
            },
            getData({status = 'open', keyword= ''}) {
                this.filters = {
					status : this.status = status,
					limit : this.table.per_page,
					page : this.pagination.page,
                    name : keyword.name || '',
                    provider_id : keyword.provider_id || 'all'
                }
                this.axiosGet(this.filters)
            },
            axiosGet(filters){
                this.loading = true
                axios.get('api/payable'+this.generateFilterURL(filters))
                .then(response=>{
                    this.table = response.data.data
                    this.list = response.data.data.data
                })
                .catch(error=>{

                })
                .finally(() => {
                    this.loading = false
                })
            },
        },
        mounted () {
            this.route = this.get_page_access(this.pages, this.$route.name)

            EventBus.$on('createPayable', (data)=> {
                this.getData({status:this.status})
            })
        }
    }
</script>

<style lang="scss">
    @import "../../scss/app.scss";

    .procedure-type
    {
        .v-breadcrumbs li a {
        font-size: 16px;
        }
        .procedure-type__menu
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
                    margin-left: auto;
                }
            }
            :nth-child(3), :nth-child(4)
            {
                justify-self: end;
            }
            .v-text-field 
            {
                font-size: 15px;
                .v-input__slot {
                    border: 1px solid #919191;
                }
            }
        }
        
        table
        {
            thead
            {
                tr
                {
                    th
                    {
                        text-align: center !important;
                    }
                }
            }
        }
    }
</style>
