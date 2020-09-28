<template>
  <div class="member-center">
    <v-breadcrumbs :items="breadcrumbs" class="px-0">
      <v-icon slot="divider">chevron_right</v-icon>
    </v-breadcrumbs>
    <div class="member-center__menu">
      <div class="search-company">
        <v-text-field prepend-inner-icon="search" class="my-0" placeholder="Search Keyword..." solo flat v-model="filters.name"></v-text-field>
      </div>
      <div class="filter-company">
		<global-select-box
			v-model="filters.company_id"
			item-text="name"
			item-value="id"
			api-link="api/company"
			label="Filter by Company"
			:default="filters.company_id"
			:filters={is_archived:0}
			add-all
			is-load
			outline
			flat
			solo
			@input="getEmitData($event)"
		></global-select-box>
      </div>
		<v-btn 
			color="accent" 
			depressed 
			:loading="loading"
			@click="getData({status:status,keyword:filters.name})"
		>
			Run Report
		</v-btn>
      <div class="dropdown">
        <b-dropdown style="width: 200px; height: 47px ;" id="ddown-right" right text="Operations" class="m-0" >
          <b-dropdown-item-button>
            <v-icon class="mr-3">mdi-account-multiple-plus</v-icon>
            <create-member>Create Member</create-member>
          </b-dropdown-item-button>
          <b-dropdown-item-button @click="go_to_url('/import-member-center')">
            <v-icon class="mr-2">mdi-file-import</v-icon>
            Import Member
          </b-dropdown-item-button>
          <b-dropdown-item-button @click="member_export(status)">
            <v-icon class="mr-2">mdi-file-export</v-icon>
            Export to Excel
          </b-dropdown-item-button>
        </b-dropdown>
      </div>
    </div>
    <div class="member-table">
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
				class="elevation-1"
			>
                <v-progress-linear slot="progress" color="grey" indeterminate></v-progress-linear>
                <template slot="items" slot-scope="props">
                <td nowrap>{{ props.item.company.universal_id }}</td>
                <td class="text-xs-left" nowrap>{{ props.item.company.carewell_id  }}</td>
                <td class="text-xs-left" nowrap>{{ props.item.first_name }} {{ props.item.last_name }}</td>
                <td class="text-xs-left">{{ props.item.company.company ? props.item.company.company.name : ''  }}</td>
                <td class="text-xs-left">{{ props.item.policy_date }}</td>
                <td>
                  <v-layout wrap>
                    <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                      <view-member v-if="route.accesses.show" :form="props.item">
                      <v-icon class="mx-1">mdi-eye</v-icon>
                      </view-member>

                      <edit-member v-if="route.accesses.edit && status == 0" :form="props.item">
                      	<v-icon class="mx-1">mdi-pencil</v-icon>
                      </edit-member>
                      <archive-member v-if="route.accesses.archive" :item="props.item">
                      <v-icon class="mx-1">{{props.item.is_archived ? "mdi-restore" : "mdi-delete"}}</v-icon>
                      </archive-member>
                    </v-flex>
                  </v-layout>
                </td>
        </template>
      </v-data-table>
    </div>
  </div>
</template>

<script>
  export default {
    data: () => ({
		breadcrumbs: [
			{text: 'Member Center',disabled: false,}, 
			{text: 'Home',disabled: true,}
		],
		select_items: [],
		headers: [
			{text: 'UNIVERSAL ID',value: 'universal_id', sortable:false},
			{text: 'CAREWELL ID',value: 'carewell_id', sortable:false},
			{text: 'MEMBER NAME',value: 'full_name', sortable:false},
			{text: 'COMPANY',value: 'company_name', sortable:false},
			{text: 'POLICY DATE',value: 'policy_date', sortable:false},
			{text: 'ACTION',value: 'action', sortable:false}
		],

		tab_items: [
			{TabTitle: 'ACTIVE',TabIcon: 'mdi-account-check',value:0}, 
			{TabTitle: 'INACTIVE',TabIcon: 'mdi-account-minus',value:1}
		],

		item:{},
		active_tab: 'tab-0',
		status: 0,
		list: [],
		filters:{},
		pagination: {},
		table:{},
		loading: false,
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

					this.axiosGet(filters)
				}
			}
		},
    methods: {
		getEmitData(data){
			this.filters.company_id = data;
		},
		getData({status = 0, keyword= ''}) {
			this.filters = {
				is_archived : this.status = status,
				limit : this.table.per_page,
				page : this.pagination.page,
				name : keyword,
				company_id : this.filters.company_id || 'all'
			}
			this.axiosGet(this.filters)
		},
		async axiosGet(filters){
			this.loading = true;

			const response = await axios.get('api/member'+this.generateFilterURL(filters));
			console.log(response)
			this.table = await response.data;
			this.list = await response.data.data;

			this.loading = await false;
		},
		member_export(status){
			window.open(this.$axios.defaults.baseURL+"/api/export/member_exportation?status="+status,"_blank");
		},
	},
	mounted(){
		this.route = this.get_page_access(this.pages, this.$route.name)
		this.filters.company_id = 'all'


		EventBus.$on('archivePlanDescription', (data)=> {
			this.getData({status:this.status})
		});
		EventBus.$on('editPlanDescription', (data)=> {
			this.getData({})
		});
		EventBus.$on('createMember', (data)=> {
			this.getData({})
		});
			EventBus.$on('typeOfProcedures', (data)=> {
			this.getData({})
		});
		EventBus.$on('editMember', (data)=> {
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
  .member-center {
	  
    .member-center__menu {
      display: grid;
      grid-template-columns: repeat(4, 1fr);

	  .dropdown {
		justify-self: end;
	  }

      .filter-company {
        padding-right: 50px;
      }

      .search-company {
        padding-right: 50px;
      }

      .operation-button {
        padding: 5px;

        .v-btn {
          height: 40px !important;
        }
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