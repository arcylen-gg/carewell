<template> 
    <div class="access-level" v-if="route">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="access-level__menu">
            <v-text-field prepend-inner-icon="search" class="my-0 c-input" v-model="filters.name" placeholder="Search Keyword..." solo flat @change="getData({status:status,keyword:filters.name})"></v-text-field>
            <create-position v-if="route.accesses.create">
                <v-btn depressed color="accent"><i class="fas fa-plus mr-3"></i>Create User Position</v-btn> 
            </create-position>
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
        :pagination.sync="pagination"
        :total-items="table.total"
        :rows-per-page-items="[5,15,25,35]"
        class="elevation-1">
            <v-progress-linear slot="progress" color="grey" indeterminate></v-progress-linear>
            <template slot="items" slot-scope="props">
                <!-- <td>{{ props.index +1 }}</td> -->
                <!-- <td class="text-xs-center">{{ props.item.id }}</td> -->
                <td class="text-xs-center">{{ props.item.name }}</td>
                <td class="text-xs-center">
                    <v-chip label color="green" text-color="white">{{ props.item.user_count }}</v-chip>
                </td>
                <td class="text-xs-center">{{ props.item.created_at | mixin_change_date_format }}</td>
                <td>
                    <v-layout wrap>
                        <v-flex xs12 sm12 md12 class="layout justify-center align-center">
                            <view-position v-if="route.accesses.show" :item="props.item">
                                <v-icon class="mx-1">mdi-eye</v-icon>
                            </view-position>
                            <edit-position v-if="route.accesses.edit" :item="props.item">
                                <v-icon class="mx-1">mdi-pencil</v-icon>
                            </edit-position>
                            <archive-position v-if="route.accesses.archive" :item="props.item">
                                <v-icon class="mx-1">{{ !props.item.is_archived ? 'mdi-delete' : 'mdi-restore' }}</v-icon>
                            </archive-position>
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
            headers: [
                // { text: 'NO.', value: 'number', sortable: false },
                { text: 'POSITION NAME', value: 'position_name', sortable: false },
                { text: 'NO. OF USERS', value: 'number_users', sortable: false },
                { text: 'DATE CREATED', value: 'date_created', sortable: false },
                { text: 'ACTION', value: 'action', sortable: false }
            ],
            tab_items: [
                { TabTitle: 'ACTIVE',TabIcon: 'mdi-account-check', value: 0 }, 
                { TabTitle: 'INACTIVE', TabIcon: 'mdi-account-minus', value: 1}
            ],
            breadcrumbs: [
                { text: 'Settings', disabled: false}, 
                { text: 'Access Level', disabled: true}
            ],
            searchKeyword : '',
            status:0,
            table: [],
            item: [],
            filters: {}
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
            getData({status = 0, keyword = ''}){
                 this.filters = {
					is_archived : this.status = status,
					limit : this.table.per_page,
					page : this.pagination.page,
					name : keyword,
                }
                
                this.axiosGet(this.filters);
            },
            axiosGet(filters){
                this.loading = true
                axios.get('/api/user_position' + this.generateFilterURL(filters))
                .then(response => { 
                    this.table = response.data.data
                    this.item = response.data.data.data
                })
                .catch(error => {

                })
                .finally(()=>{
                    this.loading = false
                })
            },
        },
        mounted () {
            this.route = this.get_page_access(this.pages, this.$route.name)
            EventBus.$on('editUserPosition', (data)=> {
                this.getData({})
            })
            EventBus.$on('createUserPosition', (data)=> {
                this.getData({})
            })
            EventBus.$on('archiveUserPosition', (data)=> {
                this.getData({status:this.status})
            })
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .v-breadcrumbs li a {
        font-size: 16px;
    }

    .access-level {
        .access-level__menu {
            display: grid;
            grid-template-columns: repeat(2, 1fr);

            .v-text-field {
                width: 60%;
                align-self: center;
            }

            :nth-child(2) {
                justify-self: end !important;
            }

            .v-btn {
                height: 40px;
            }

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191;
                }
            }
        }

        table {
            thead {
                tr {
                    th {
                        text-align: center !important;
                    }
                }
            }
        }
    }
</style>