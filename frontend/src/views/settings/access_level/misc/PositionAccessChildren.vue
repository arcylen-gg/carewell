<template>
    <span>
        <!-- <v-btn depressed color="accent"><i class="fas fa-plus mr-3" @click="loadDialog"></i><slot>User Position Access</slot></v-btn> -->
        <span block color="primary" dark @click="loadDialog"><slot>Open Dialog</slot></span>
        <v-dialog v-model="dialog" persistent scrollable max-width="700px">
            <v-card class="create-position">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">User Position Access</v-card-title>
                <v-card-text>
                    <div class="create-position__holder">
                        <div class="title mb-2">Page Access and Visibility </div>
                        <div flat v-for="(page, index) of pages" :key="page.name"  
                        class="subheading " 
                        color="blue-grey lighten-5" 
                        :style="{'margin-left' : `${page.type * 20}px`}">
                        <b-form-checkbox plain class="blue-grey--text" :class="fontWeightClass[page.type]" v-model="select_all[index]" @change="selectAll(index)">{{ page.name }}</b-form-checkbox>
                        <b-form-group class="display-block">
                            <b-form-checkbox-group v-model="selected[index]" name="access_types" :options="access_types" @change="selectSingle(index)">
                            </b-form-checkbox-group>
                        </b-form-group>
                        </div>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="dialog = false">CANCEL</v-btn>
                    <v-btn color="accent" depressed @click="setAccesses">SET</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default {
        props : {
            children : {
                type: Array
            },
            depth : {
                type: Number,
                default: 1
            },
            eventName : {
                type: String,
                default: 'positionAccess'
            },
            existing_accesses : {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                dialog: false,
                selected: [],
                select_all: [],
                access_types: [
                    {text: 'Read', value: 'read'},
                    {text: 'Show', value: 'show'},
                    {text: 'Create', value: 'create'},
                    {text: 'Edit', value: 'edit'},
                    {text: 'Archive', value: 'archive'},
                ],
                fontWeightClass: ['font-weight-bold', 'font-weight-medium', 'font-weight-regular', 'font-weight-light', 'font-weight-thin'],
                codes: []
            }
        },
        mounted () {
        },
        methods : {
            loadDialog(){
                this.dialog = true
                this.modifyAccesses()
                EventBus.$on('clearAccesses', (data)=> {
                    this.selected = []
                    this.select_all = []
                })
            },
            setAccesses()
            {   
                this.user_position_accesses = []
                this.pages.forEach((data, key) => {
                    this.user_position_accesses.push({
                        id: data.id,
                        code: data.code,
                        types: this.selected[key] || []
                    })
                })
                EventBus.$emit(this.eventName, this.user_position_accesses)
                this.dialog = false
            },
            selectAll(index)
            {
                this.selected[index] = !this.select_all[index] ? this.access_types.map(data => { return data.value }) : []
            },
            selectSingle(index)
            {
                this.select_all[index] = this.selected[index] && this.selected[index].length == this.access_types.length-1 ? true : false
            },
            modifyAccesses(){
                if(this.existing_accesses && this.existing_accesses.length)
                {
                    this.existing_accesses.forEach((data, index) => {
                        this.selected[index] = data.accesses
                        if(this.access_types.length == this.selected[index].length)
                        {
                            this.select_all[index] = true
                        }
                        else
                        {
                            this.select_all[index] = false
                        }
                    })
                }
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";
    .create-position {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .create-position__holder {
            border: 1px solid #ededed;
            padding: 50px 50px 30px;
            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #ced4da !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }
        }
    }

    .dasher {
        letter-spacing: -3px;
    }
</style>