<template>
    <div class="coverage-plan" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <v-layout class="coverage-plan__menu">

        </v-layout>
        <div class="coverage-plan__menu">
            <v-text-field v-model="filters.name" prepend-inner-icon="search" class="my-0 c-input" placeholder="Search Keyword..." solo flat  @change="getData({status:status,keyword:filters.name})"></v-text-field>
            <div></div>
            <div></div>
            <v-btn depressed color="accent" solo to="/settings/coverage-plan/create"><i class="fas fa-plus mr-2"></i>Create Coverage Plan</v-btn>
        </div>
        <v-tabs color="primary" dark icons-and-text grow slider-color="#ffa500">
            <v-tab v-for="item in tab_items" :key="item.TabTitle" :disabled="loading" @click="getData({status:item.value})">
                {{ item.TabTitle }}
                <v-icon>{{ item.TabIcon }}</v-icon>
            </v-tab>
            <v-tab-item v-for="item in tab_items" :key="item.TabTitle"></v-tab-item>
        </v-tabs>
        <v-data-table 
            :headers="headers" 
            :items="item" 
            :loading="loading"
            :pagination.sync ="pagination"
            :total-items="table.total"
            :rows-per-page-items="[5,15,25,35]"
            class="elevation-1"
        >
            <v-progress-linear slot="progress" color="grey" indeterminate></v-progress-linear>
            <template slot="items" slot-scope="props">
                <td class="text-xs-center">{{ props.item.id }}</td>
                <td class="text-xs-center">{{ props.item.name }}</td>
                <td class="text-xs-center">{{ props.item.annual_benefit_limit }}</td>
                <td class="text-xs-center">{{ props.item.maximum_benefit_limit }}</td>
                <td class="text-xs-center">{{ props.item.monthly_premium }}</td>
                <td>
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                            <v-btn flat color="grey darken-1" icon small @click="viewCoverage(props.item.id)" style="cursor: pointer;">
                                <v-icon class="mx-1">mdi-printer</v-icon>
                            </v-btn>
                            <v-btn flat color="grey darken-1" icon small :to="`/settings/coverage-plan/edit?id=${props.item.id}`" style="cursor: pointer;">
                                <v-icon class="mx-1">mdi-pencil</v-icon>
                            </v-btn>
                            <archive-coverage-plan v-if="route.accesses.archive" :item="props.item">
                                <v-icon class="mx-1">{{ !props.item.is_archived ? 'mdi-delete' : 'mdi-restore' }}</v-icon>
                            </archive-coverage-plan>
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
            loading: false,
            route : null,
            pagination : {},
            status:0,
            table: {},
            item : [],
            filters: {
                is_archived : 0,
                name : null,
            },
            headers: [
                { text: 'PLAN ID', value: 'plan_id', sortable: false, align: 'center' },
                { text: 'PLAN NAME', value: 'plan_name', sortable: false, align: 'center' }, 
                { text: 'PLAN ABL', value: 'plan_abl', sortable: false, align: 'center' },
                { text: 'PLAN MBL', value: 'plan_mbl', sortable: false, align: 'center' }, 
                { text: 'MONTHLY PREMIUM', value: 'monthly_premium', sortable: false, align: 'center' },
                { text: 'ACTION', value: 'action', sortable: false, align: 'center' }
            ],
            tab_items: [
                { TabTitle: 'ACTIVE',TabIcon: 'mdi-account-check', value: 0 }, 
                { TabTitle: 'INACTIVE', TabIcon: 'mdi-account-minus', value: 1}
            ],
            breadcrumbs: [
                { text: 'Settings', disabled: false}, 
                { text: 'Coverage Plan', disabled: true}
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
                axios.get('api/coverage_plan'+this.generateFilterURL(filters))
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
            viewCoverage(id){
                window.open(this.$axios.defaults.baseURL+`/export/coverage_plan?id=${id}&user=${this.user.full_name}`,"_blank")
            }
        },
        mounted () {
            this.route = this.get_page_access(this.pages, this.$route.name)
            EventBus.$on('archiveCoveragePlan', (data)=> {
                this.getData({})
            })
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .v-breadcrumbs li a {
        font-size: 16px;
    }

    .coverage-plan__menu {
        display: grid;
        grid-template-columns: repeat(4, 1fr);

        .v-text-field {
            width: 100%;
            padding-right: 10px;
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
          border: 1px solid #919191;
        }
      }
    }

</style>