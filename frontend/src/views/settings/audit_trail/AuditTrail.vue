<template> 
    <div class="admin-panel" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0"> 
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="search-and-filter">
            <v-text-field prepend-inner-icon="search" class="my-0 c-input" placeholder="Search Keyword..." solo flat v-model="filters.name"></v-text-field>
            <v-menu
                v-model="datepicker_from"
                :close-on-content-click="false"
                :nudge-right="40"
                lazy
                transition="scale-transition"
                offset-y
                full-width
                min-width="290px"
            >
                <date-format
                    slot="activator"
                    v-model="filters.date_from"
                    :default="new Date().toISOString().substr(0, 10)"
                    label="Date From..."
                    append-icon="event"
                    outline
                    readonly
                ></date-format>
                <v-date-picker 
                    v-model="filters.date_from" 
                    @input="datepicker_from = false" 
                    :max="new Date().toISOString().substr(0, 10)" 
                    min="1950-01-01"
                ></v-date-picker>
            </v-menu>
            <v-menu
                v-model="datepicker_to"
                :close-on-content-click="false"
                :nudge-right="40"
                lazy
                transition="scale-transition"
                offset-y
                full-width
                min-width="290px"
            >
                <date-format
                    slot="activator"
                    v-model="filters.date_to"
                    :default="new Date().toISOString().substr(0, 10)"
                    label="Date To..."
                    append-icon="event"
                    outline
                    readonly
                ></date-format>
                <v-date-picker 
                    v-model="filters.date_to" 
                    @input="datepicker_to = false" 
                    :max="new Date().toISOString().substr(0, 10)" 
                    min="1950-01-01"
                ></v-date-picker>
            </v-menu>
            <v-btn color="accent" depressed type="submit" :loading="loading" @click="getData({status:status, keyword:filters})">Run Report</v-btn>
               <!-- <v-btn color="accent" depressed type="submit" :loading="loading" @click="getAction">Run Report</v-btn> -->
            <span></span>
            <v-select color="accent" v-model="form.export" label="Exports" :items="[{value: 'excel',text : 'Export To Excel'}, {value: 'pdf',text : 'Export To PDF'}]" item-text="text" item-value="value" outline @change="exportList"></v-select>
        </div>
        <v-data-table 
            :headers="headers" 
            :items="item" 
            class="elevation-1"
            :loading="loading"
            :pagination.sync ="pagination"
            :total-items="table.total"
            :rows-per-page-items="[5,15,25,35]"
        >
            <template slot="items" slot-scope="props">
                <td>{{ props.item.created_at }}</td>
                <td class="text-xs-left">{{ props.item.user.first_name }} {{ props.item.user.middle_name }} {{ props.item.user.last_name }}</td>
                <td class="text-xs-left">{{ props.item.remarks }}</td>
                <td class="text-xs-left">{{ props.item.description }}</td>
                <td class="text-xs-left">
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                            <view-audit-trail v-if="route.accesses.show" :form="props.item">
                            <v-icon class="mx-1">mdi-eye</v-icon>
                            </view-audit-trail>
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
            active_tab: 'tab-0',
            item:[],
            filters:{},
            headers: [
                {text: 'DATE',value: 'date'}, 
                {text: 'USER',value: 'user'}, 
                {text: 'TRANSACTION',value: 'remarks'},
                {text: 'DESCRIPTION',value: 'description'},
                {text: 'ACTION',value: 'action'},
            ],
            breadcrumbs: [
                {text: 'Settings',disabled: false,}, 
                {text: 'Audit Trail',disabled: true,}
            ],
            route : null,
            searchKeyword : '',
            status:0,
            selectOptions: [],
            form : {},
            datepicker_from: false,
            datepicker_to: false,
            loading : false,
            pagination : {},
            table : {},
        }),
        watch : {
			pagination: {
				handler () {
					let filters = {
						is_archived : this.status,
						limit : this.pagination.rowsPerPage || 5,
						page : this.pagination.page || 1,
                        name : this.filters.name || '',
                        date_from : this.filters.date_from || new Date().toISOString().substr(0, 10),
                        date_to : this.filters.date_to || new Date().toISOString().substr(0, 10),
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
                    name : keyword.name || '',
                    date_from : keyword.date_from,
                    date_to : keyword.date_to
				}

				this.axiosGet(this.filters)
			},
            axiosGet(filters){
                this.loading = true;
                axios.get('api/audit_trail'+this.generateFilterURL(filters))
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
            exportList()
            {
                window.open(this.$axios.defaults.baseURL+'/api/export/audit_trail'+this.generateFilterURL(this.filters)+`&file_type=${this.form.export}`,"_blank");
            }
        },
        mounted(){
            this.route = this.get_page_access(this.pages, this.$route.name)
        },
    
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";
    .search-and-filter {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        grid-gap : 5px
    }

</style>
