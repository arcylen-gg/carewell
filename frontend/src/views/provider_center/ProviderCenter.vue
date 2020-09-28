<template>
  <div class="provider-center" v-if="route">
    <v-breadcrumbs :items="breadcrumbs" class="px-0">
      <v-icon slot="divider">chevron_right</v-icon>
    </v-breadcrumbs>
    <div class="provider-center__menu">
      <div class="search-company">
        <v-text-field prepend-inner-icon="search" class="my-0 c-input" placeholder="Search Keyword..." solo flat @change="getData({status:status,keyword:filters.name})" v-model="filters.name"></v-text-field>
      </div>
      <!-- <v-btn color="success" depressed><v-icon class="mr-3">mdi-file-import</v-icon> Import from Excel</v-btn> -->
        <v-btn color="success" @click="go_to_url('/settings/import-network-provider-center')"><v-icon>mdi-file-import</v-icon> IMPORT PROVIDER WITH PAYEE</v-btn>
      <create-provider v-if="route.accesses.create"></create-provider>
    </div>
    <div class="provider-table">
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
                            <td class="text-xs-left">{{ props.item.name }}</td>
                            <td class="text-xs-left">{{ props.item.contact_person }}</td>
                              <td class="text-xs-left">
                                  <strong>E-mail: </strong>{{ props.item.email }}<br>
                                  <strong>Tel no.: </strong>{{ props.item.tel_number }}<br>
                                  <strong>Contact no.: </strong>{{ props.item.contact_number }}
                              </td>
                            <td class="text-xs-left">
                                <v-layout wrap>
                                    <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                                        <view-provider :form="props.item"><v-icon>mdi-eye</v-icon></view-provider>
                                        <edit-provider v-if="route.accesses.edit && index == 0" :form="props.item"><v-icon>mdi-pencil</v-icon></edit-provider>
                                        <archive-provider v-if="route.accesses.archive" :form="props.item"><v-icon>{{ !props.item.is_archived ? 'mdi-delete' : 'mdi-restore' }}</v-icon></archive-provider>
                                    </v-flex>
                                </v-layout>
                            </td>
                        </template>
                    </v-data-table>
                </v-card>
            </v-tab-item>
        </v-tabs-items>

    </div>
  </div>
</template>

<script>
  export default {
        data: () => ({
			loading: false,
			pagination : {},
			table : {},
            active_tab: 'tab-0',
            item:[],
            filters:{},
            route : null,
            status:0,
            breadcrumbs: [
                    {text: 'Settings',disabled: false,},
                    {text: 'Network Provider Center',disabled: true,}
                ],
            headers: [
                    // {text: 'NO.',value: 'provider_id'}, 
                    {text: 'PROVIDER NAME',value: 'provider_name'}, 
                    {text: 'CONTACT PERSON',value: 'contact_person'},
                    {text: 'CONTACT REFERENCE',value: 'contact_reference'},
                    {text: 'ACTION',value: 'action'},
                ],

            tab_items: [
                    {TabTitle: 'ACTIVE',TabIcon: 'mdi-account-check' , value:0,},
                    {TabTitle: 'INACTIVE',TabIcon: 'mdi-account-minus', value:1,},
                ],
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
				axios.get('api/provider'+this.generateFilterURL(filters))
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

            EventBus.$on('archiveProvider', (data)=> {
                this.getData({status : this.status})
            });
            EventBus.$on('editProvider', (data)=> {
                this.getData({})
            });
            EventBus.$on('createProvider', (data)=> {
                this.getData({})
            });
        },
    }
</script>

<style lang="scss">
  @import "../../scss/app.scss";

  .v-breadcrumbs li a {
    font-size: 16px;
  }
  .v-datatable thead th.column.sortable
  {
      color: #000 !important;
  }
  table.v-table tbody td
  {
      color: #000;
  }

  .provider-center {
    .provider-center__menu {
      display: grid;
      text-align: center;
      grid-template-columns: repeat(3, 1fr);
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