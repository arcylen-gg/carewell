<template>
    <div class="procedure-type" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="procedure-type__menu">
            <v-text-field prepend-inner-icon="search" class="my-0 c-input" placeholder="Search Keyword..." solo flat @change="getData({status:status,keyword:filters.name})" v-model="filters.name"></v-text-field>
            <span></span>
            <span></span>
            <create-bank v-if="route.accesses.create">
                <v-btn depressed color="accent"><i class="fas fa-plus mr-3"></i>Create Bank</v-btn> 
            </create-bank>
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
            class="elevation-1 qweqwe">
                <v-progress-linear slot="progress" color="grey" indeterminate></v-progress-linear>
                <template slot="items" slot-scope="props">
                <td class="text-xs-center">{{ props.item.id }}</td>
                <td class="text-xs-center">{{ props.item.name }}</td>
                <td>
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                            <view-bank v-if="route.accesses.show" :item="props.item">
                            <v-icon class="mx-1">mdi-eye</v-icon>
                            </view-bank>
                            <edit-bank v-if="route.accesses.edit && status == 0" :item="props.item">
                            <v-icon class="mx-1">mdi-pencil</v-icon>
                            </edit-bank>
                            <archive-bank v-if="route.accesses.archive" :item="props.item">
                            <v-icon class="mx-1">{{props.item.is_archived ? "mdi-restore" : "mdi-delete"}}</v-icon>
                            </archive-bank>
                        </v-flex>
                    </v-layout>
                </td>
            </template>
        </v-data-table>
    </div>
</template>


<script>
    export default {
        data: () => ({
            type: ['APE', 'Laboratory'],
            active_tab: 'tab-0',
            item:[],
            breadcrumbs: [
                {text: 'Settings', disabled: false,}, 
                {text: 'Maintenance',disabled: false,},
                {text: 'Bank',disabled: true,}
            ],
            filters:{
                is_archived: 0,
            },
            loading: false,
            
            headers: [
                {text: 'NO.',value: 'id'}, 
                {text: 'NAME',value: 'name'}, 
                {text: 'ACTION',value: 'action'}
            ],
            list:[],
            status:0,
            route : null,
            searchKeyword : '',
            tab_items: [
            {TabTitle: 'ACTIVE',value: 0,TabIcon: 'mdi-account-check'}, 
            {TabTitle: 'INACTIVE',value: 1,TabIcon: 'mdi-account-minus'},
            ],
            pagination : {},
            table:{}
        }),
        watch: {
            pagination: {
                handler () {
                    let filters = {
						is_archived : this.status,
						limit : this.pagination.rowsPerPage || 5,
						page : this.pagination.page || 1,
						name : this.filters.name || '',
					}

                    this.axiosGet(filters);
                },
                deep: true
            },
        },
        methods : {
            getData({status = 0, keyword= ''}) {
                  this.filters = {
					is_archived : this.status = status,
					limit : this.table.per_page,
					page : this.pagination.page,
					name : keyword,
                }
                this.axiosGet(this.filters)
            },
            axiosGet(filters){
                axios.get('api/bank'+this.generateFilterURL(filters))
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

        EventBus.$on('archiveBank', (data)=> {
            this.getData({status:this.status})
        })
        
        EventBus.$on('createBank', (data)=> {
            this.getData({})
        })

        EventBus.$on('editBank', (data)=> {
            this.getData({})
        })

        // EventBus.$on('filterPaymentTerm', (data)=> {
        //     this.filters = data
        //     this.reloadTable()
        // })
    }
    }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

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
