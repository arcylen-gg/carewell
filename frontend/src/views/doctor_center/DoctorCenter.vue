<template>
    <div class="doctor-center" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="doctor-center__menu">
            <v-text-field prepend-inner-icon="search" class="my-0" placeholder="Search Keyword..." solo flat @change="getData({status:status,keyword:filters.name})" v-model="filters.name"></v-text-field>
            <div class="doctor-center__menu">
                <v-btn color="success" @click="go_to_url('/settings/maintenance/import-doctor')"><v-icon>mdi-file-import</v-icon> IMPORT DOCTOR</v-btn>
                <add-doctor class="ml-auto" v-if="route.accesses.create">
                    <v-btn color="accent" depressed><i class="fas fa-plus mr-3"></i>Add Doctor</v-btn>
                </add-doctor>
            </div>
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
                            <td>{{ props.index+1 }}</td>
                            <td class="text-xs-left">{{ props.item.first_name }} {{props.item.middle_name}} {{props.item.last_name}}</td>
                            <td class="text-xs-left">{{ props.item.doctor_provider.length}}</td>
                            <td class="text-xs-left">{{ props.item.created_at | mixin_change_date_format }}</td>
                            <td class="text-xs-left">
                            <v-layout wrap>
                            <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                                <view-doctor :form="props.item"><v-icon>mdi-eye</v-icon></view-doctor>
                                <edit-doctor :form="props.item"  v-if="route.accesses.edit && index == 0" ><v-icon>mdi-pencil</v-icon></edit-doctor>
                                <archive-doctor :form="props.item" v-if="route.accesses.archive"><v-icon>{{ !props.item.is_archived ? 'mdi-delete' : 'mdi-restore' }}</v-icon></archive-doctor>
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
            active_tab : 'tab-0',
            filters : {},
            item : [],
            route : null,
            status : 0,
            searchKeyword : '',
            headers: [
                { text: 'NO.', value: 'doctor_id' }, 
                { text: 'DOCTOR NAME', value: 'doctor_name' }, 
                { text: 'NO. OF PROVIDERS', value: 'doctor_provider' },
                { text: 'DATE CREATED', value: 'date_created' }, 
                { text: 'ACTION', value: 'action' }
            ],

            tab_items: [
                { TabTitle: 'ACTIVE', TabIcon: 'mdi-account-check', value:0,}, 
                { TabTitle: 'INACTIVE', TabIcon: 'mdi-account-minus', value:1,} 
            ],

            breadcrumbs: [
                {text: 'Settings',disabled: false,},
                {text: 'Doctor Center',disabled: true,}
            ],
            loading:false,
            table: {},
            pagination : {}
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
				}
        	},
		},
        methods : {
            getData({status = 0,keyword = ''}){
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
                    axios.get('api/doctor'+this.generateFilterURL(filters))
                    .then(response=>{
                        this.table = response.data.data
                        this.item = response.data.data.data
                    })
                    .catch(error=>{

                    })
                    .finally(()=>{
                        this.loading = false
                    })
            }
        },
        mounted(){
            this.route = this.get_page_access(this.pages, this.$route.name)

            EventBus.$on('addDoctor', (data)=> {
                this.getData({});
            });
            EventBus.$on('editDoctor', (data)=> {
                this.getData({});
            });
            EventBus.$on('archiveDoctor', (data)=> {
                this.getData({status:this.status});
            });
        }
    }
</script>

<style lang="scss">
    @import "../../scss/app.scss";

    .v-datatable thead th.column.sortable
    {
        color: #000 !important;
    }
    table.v-table tbody td
    {
        color: #000;
    }
    .v-breadcrumbs li a {
        font-size: 16px;
    }
    .v-text-field {
        font-size: 15px;

        .v-input__slot {
            border: 1px solid #919191 !important;
        }
    }

    .doctor-center__menu {
        display: grid;
        grid-template-columns: repeat(2, 1fr);

        .v-text-field {
            width: 60%;
            align-self: center;
        }

        .v-btn {
            height: 40px;
        }

        .v-dialog__container
        {
            justify-self: end;
        }
        .v-text-field {
        font-size: 15px;

        .v-input__slot {
          border: 1px solid #ced4da;
        }
      }
    }

</style>