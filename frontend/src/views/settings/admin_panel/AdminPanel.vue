<template> 
    <div class="admin-panel" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0"> 
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="search-and-create__button">
            <v-text-field prepend-inner-icon="search" class="my-0 c-input" placeholder="Search Keyword..." solo flat @change="getData({status:status,keyword:filters.name})" v-model="filters.name"></v-text-field>
            <create-user-admin v-if="route.accesses.create"/>
            <!-- <create-user-admin v-if="route.accesses.create" :selectOptions="selectOptions"/> -->
        </div>
        <v-tabs color="primary" dark icons-and-text grow v-model="active_tab" slider-color="#ffa500">
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
                        class="elevation-1"
                    >
                        <template slot="items" slot-scope="props">
                            <!-- <td>{{ props.index +1 }}</td> -->
                            <!-- <td>{{ props.item.id }}</td> -->
                            <td class="text-xs-left">{{ props.item.first_name }} {{ props.item.middle_name }} {{ props.item.last_name }}</td>
                            <td class="text-xs-left">{{ props.item.email }}</td>
                            <td class="text-xs-left">{{ props.item.user_position.name }}</td>
                            <td class="text-xs-left">
                                <v-layout wrap>
                                    <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                                        <view-user-admin v-if="route.accesses.show" :form="props.item"><v-icon>mdi-eye</v-icon></view-user-admin>
                                        <div v-if="!props.item.is_default">
                                            <edit-user-admin v-if="route.accesses.edit && index == 0" :form="props.item"><v-icon>mdi-pencil</v-icon></edit-user-admin>
                                            <archive-user-admin v-if="route.accesses.archive" :form="props.item"><v-icon>{{ !props.item.is_archived ? 'mdi-delete' : 'mdi-restore' }}</v-icon></archive-user-admin>
                                        </div>
                                    </v-flex>
                                </v-layout>
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
            table : {},
            loading : false,
            pagination : {},
            active_tab: 'tab-0',
            item:[],
            filters:{},
            headers: [
                // {text: 'NO.',value: 'user_number'}, 
                {text: 'FULL NAME',value: 'name_user'}, 
                {text: 'EMAIL',value: 'email'},
                {text: 'POSITION',value: 'user_position'},
                {text: 'ACTION',value: 'action'},
            ],
            tab_items: [
                {TabTitle: 'ACTIVE',TabIcon: 'mdi-account-check' , value:0,},
                {TabTitle: 'INACTIVE',TabIcon: 'mdi-account-minus', value:1,},
            ],
            breadcrumbs: [
                {text: 'Settings',disabled: false,}, 
                {text: 'Admin Panel',disabled: true,}
            ],
            route : null,
            status:0,
        }),
        watch : {
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
                deep : true
        	},
		},
        methods : {
            getData({status = 0, keyword = ''}){
                this.filters = {
					is_archived : this.status = status,
					limit : this.table.per_page,
					page : this.pagination.page,
					name : keyword,
				}
                this.axiosGet(this.filters)
            },
            axiosGet(filters){
                this.loading = true
                axios.get('api/user'+this.generateFilterURL(filters))
                .then(response=>{
                    this.table = response.data.data
                    this.item = response.data.data.data
                })
                .catch(error=>{

                })
                .finally(()=>{
                    this.loading = false
                })
            },
        },
        mounted(){

            this.route = this.get_page_access(this.pages, this.$route.name)

            EventBus.$on('archiveAdmin', (data)=> {
                this.getData({status:this.status})
            });
            EventBus.$on('editAdmin', (data)=> {
                this.getData({})
            });
            EventBus.$on('createAdmin', (data)=> {
                this.getData({})
            });
        },
    
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";
</style>