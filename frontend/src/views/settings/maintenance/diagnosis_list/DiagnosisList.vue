<template>
    <div class="diagnosis-list" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="coverage-description__menu">
            <v-text-field prepend-inner-icon="search" class="my-0" placeholder="Search Keyword..." solo flat @change="getData({status:status,keyword:filters.name})" v-model="filters.name"></v-text-field>
            <v-label>&nbsp;</v-label>
            <!-- <import-plan-description/> -->
            <v-btn color="success" @click="go_to_url('/settings/maintenance/import-diagnosis-list')"><v-icon>mdi-file-import</v-icon> IMPORT DIAGNOSIS</v-btn>
            <create-diagnosis v-if="route.accesses.create"/>
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
                :items="item" 
                :loading="loading"
                :pagination.sync="pagination"
                :total-items="table.total"
                :rows-per-page-items="[5,15,25,35]"
                class="elevation-1"
            >
                <v-progress-linear slot="progress" color="grey" indeterminate></v-progress-linear>
                <template slot="items" slot-scope="props">
                <td class="text-xs-center">{{ props.item.id }}</td>
                <td class="text-xs-center">{{ props.item.name }}</td>
                <td class="text-xs-center">{{ props.item.description }}</td>
                <td>
                    <v-layout wrap>
                            <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                                <view-diagnosis v-if="route.accesses.show" :item="props.item">
                                <v-icon class="mx-1">mdi-eye</v-icon>
                                </view-diagnosis>
                                <edit-diagnosis v-if="route.accesses.edit && status == 0" :item="props.item">
                                <v-icon class="mx-1">mdi-pencil</v-icon>
                                </edit-diagnosis>
                                <archive-diagnosis v-if="route.accesses.archive" :item="props.item">
                                <v-icon class="mx-1">{{props.item.is_archived ? "mdi-restore" : "mdi-delete"}}</v-icon>
                                </archive-diagnosis >
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
            item : [],
            active_tab: 'tab-0',
            filters:{},
            pagination : {},
            loading: false,
            breadcrumbs: [
                { text: 'Settings', disabled: false,},
                { text: 'Maintenance', disabled: false,},
                { text: 'Diagnosis List', disabled: true,}
            ],
            headers: [
                {text: 'NO.',value: 'number'}, 
                {text: 'NAME',value: 'name'}, 
                {text: 'DESCRIPTION',value: 'description'},
                {text: 'ACTION',value: 'action'},
            ],
            tab_items: [
                {TabTitle: 'ACTIVE',TabIcon: 'mdi-account-check',value:0}, 
                {TabTitle: 'INACTIVE',TabIcon: 'mdi-account-minus',value:1},
            ],
            route : null,
            status:0,
            table : {}
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
            getData({status = 0,keyword=''}) {
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
                axios.get('api/diagnosis_list'+this.generateFilterURL(filters))
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
        },
        mounted(){
            this.route = this.get_page_access(this.pages, this.$route.name)

            EventBus.$on('archiveDiagnosis', (data)=> {
                this.getData({status:this.status})
            });
            EventBus.$on('editDiagnosis', (data)=> {
                this.getData({})
            });
            EventBus.$on('createDiagnosis', (data)=> {
                this.getData({})
            });
        },
    }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

    .diagnosis-list
    {
        .v-breadcrumbs li a {
        font-size: 16px;
        }
        .coverage-description__menu
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
