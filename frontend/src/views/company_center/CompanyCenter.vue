<template>
    <div class="company-center" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="company-center__menu">
            <v-text-field prepend-inner-icon="search" class="my-0" placeholder="Search Keyword..." solo flat @change="getData({status:status,keyword:filters.name})" v-model="filters.name"></v-text-field>
            <!-- <v-btn @click="company_export(status)" color="tertiary" depressed dark><v-icon class="mr-3">mdi-file-export</v-icon> Export to Excel</v-btn>
            <create-company v-if="route.accesses.create" :selectOptions="selectOptions"/> -->
            <div></div>
            <div class="dropdown">
                <b-dropdown style="width: 200px; height: 47px ;" id="down-right" right text="Operations" class="m-0" >
                <b-dropdown-item-button>
                    <v-icon class="mr-3">mdi-account-multiple-plus</v-icon>
                    <create-company v-if="route.accesses.create" :selectOptions="selectOptions">Create Company</create-company>
                </b-dropdown-item-button>
                <b-dropdown-item-button @click="go_to_url('/import-company-center')">
                    <v-icon class="mr-2">mdi-file-import</v-icon>
                    Import Company
                </b-dropdown-item-button>
                <b-dropdown-item-button @click="company_export(status)">
                    <v-icon class="mr-2">mdi-file-export</v-icon>
                    Export to Excel
                </b-dropdown-item-button>
                </b-dropdown>
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
                            <td>{{ props.index +1 }}</td>
                            <!-- <td>{{ props.item.id }}</td> -->
                            <td class="text-xs-left">{{ props.item.name }}</td>
                            <td class="text-xs-left">{{ props.item.code }}</td>
                            <td class="text-xs-left">{{ props.item.address }}</td>
                            <td class="text-xs-left">
                                <strong>E-mail: </strong>{{ props.item.email }}<br>
                                <strong>Contact Number: </strong>{{ props.item.contact_number }}
                                </td>
                            <td class="text-xs-left">
                                 <v-layout wrap>
                                    <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                                        <view-company :form="props.item" :selectOptions="selectOptions"><v-icon>mdi-eye</v-icon></view-company>
                                        <edit-company v-if="route.accesses.edit && index == 0" :form="props.item"><v-icon>mdi-pencil</v-icon></edit-company>
                                        <archive-company v-if="route.accesses.archive" :form="props.item"><v-icon>{{ !props.item.is_archived ? 'mdi-delete' : 'mdi-restore' }}</v-icon></archive-company>
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
            breadcrumbs: [
                {text: 'Company Center',disabled: false,}, 
                {text: 'Home',disabled: true,},
            ],
            headers: [
                {text: 'NO.', value: 'id'}, 
                {text: 'COMPANY NAME',value: 'name'}, 
                {text: 'CODE',value: 'code'}, 
                {text: 'ADDRESS',value: 'address'}, 
                {text: 'REFERENCE',value: 'references'}, 
                {text: 'ACTION',value: 'action'},
            ],
            tab_items: [
                {TabTitle: 'ACTIVE',TabIcon: 'mdi-account-check' , value:0,},
                {TabTitle: 'INACTIVE',TabIcon: 'mdi-account-minus', value:1,},
            ],
            active_tab: 'tab-0',
            item:[],
            filters:{},
            route : null,
            searchKeyword : '',
            status:0,
            selectOptions:{
                coverage_plan:[],
                base_coverage_plan:[]
            },
            table : {},
            pagination :{},
            loading : false,
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
                 axios.get('api/company'+this.generateFilterURL(filters))
                .then(response=>{
                    this.table = response.data.data
                    this.item = response.data.data.data
                    console.log(this.item)
                })
                .catch(error=>{

                })
                .finally(()=>{
                    this.loading = false
                })
            },
            async getDataOptions() {
                const coveragePlanAPI =  axios.get('/api/coverage_plan');

                const [coverage_plan] = await Promise.all([coveragePlanAPI])

                var filteredCoveragePlan = coverage_plan.data.data.data.filter(coverage => coverage.is_used_in_company == false);
                //console.log(filteredCoveragePlan,'filtered')
                coverage_plan.data.data.data = filteredCoveragePlan

                this.selectOptions.coverage_plan = coverage_plan.data.data
            },
            company_export(status){
                window.open(this.$axios.defaults.baseURL+"/api/export/company_exportation?status="+status,"_blank");
            },
        },
        mounted(){
            this.route = this.get_page_access(this.pages, this.$route.name)
            // this.getDataOptions();

            EventBus.$on('archiveCompany', (data)=> {
                this.getData({status:this.status})
                // this.getDataOptions();
            });
            EventBus.$on('editCompany', (data)=> {
                this.getData({})
                // this.getDataOptions();
            });
            EventBus.$on('createCompany', (data)=> {
                this.getData({})
                // this.getDataOptions();
            });
        }
    }
</script>

<style lang="scss">
    @import "../../scss/app.scss";
    .company-center {
        .v-datatable thead th.column.sortable
        {
            color: #000 !important;
        }
        table.v-table tbody td
        {
            color: #000;
        }
        .company-center__menu {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        .dropdown {
		justify-self: end;
	    }
        .v-btn
        {
            height: 40px;
        }
        .v-text-field {
            font-size: 15px;

            .v-input__slot {
            border: 1px solid #919191;
            }
        }
        }
    }
</style>
