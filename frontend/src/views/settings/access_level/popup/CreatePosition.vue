<template>
    <span>
        <span block color="primary" dark @click="loadDialog"><slot>Open Dialog</slot></span>
        <v-dialog v-model="dialog" persistent max-width="400px">
            <v-card class="edit-description">

                <!-- Dialog Title -->
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">
                    <span class="headline white--text">Create User Position</span>
                </v-card-title>

                <!-- Dialog Content -->
                <v-card-text>
                    <v-form ref="form"  @submit.prevent="save" lazy-validation>
                        <div class="main-holder">
                            <v-text-field label="Name" v-model="user_position.name" :rules="requiredRules" outline></v-text-field>
                            <position-access-children>
                                <v-btn depressed color="accent"><i class="fas fa-plus mr-3"></i>User Position Level Access</v-btn> 
                            </position-access-children>
                        </div>
                    <input type="submit" ref="submit_button" class="hide-submit">
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed @click.native="submit" :disabled="loading">SAVE</v-btn>
                </v-card-actions>
                <!-- Dialog Buttons -->
                <v-card-actions class="grey lighten-3 pa-0 ma-0" v-if="loading">
                    <v-progress-linear slot="progress" color="grey" class="pa-0 ma-0" indeterminate></v-progress-linear>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default {
        data() {
            return {
                fontWeightClass: ['font-weight-bold', 'font-weight-medium', 'font-weight-regular', 'font-weight-light', 'font-weight-thin'],
                dialog: false,
                selected: [null],
                access_types: [
                    {text: 'Read', value: 'read'},
                    {text: 'Show', value: 'show'},
                    {text: 'Create', value: 'create'},
                    {text: 'Edit', value: 'edit'},
                    {text: 'Archive', value: 'archive'}
                ],
                user_position: {
                    name: null,
                    user_position_accesses: []
                },
                loading: false,
                requiredRules: [
                    v => !!v || 'This input cannot be blank.',
                ],
            }
        },
        mounted () {
            
        },
        props : {
            eventName: {
                type: String,
                default: "createUserPosition"
            }
        },
        methods : {
            loadDialog(){
                this.dialog = true
                this.checkUserAccesses()
                EventBus.$on('positionAccess', (data)=> {
                    this.user_position.user_position_accesses = data
                })
            },
            checkUserAccesses()
            {
                this.user_position.user_position_accesses = []
                this.pages.forEach((data, key) => {
                    this.user_position.user_position_accesses.push({
                        id: data.id,
                        code: data.code,
                        types: this.selected[key] || []
                    })
                })
            },
            save()
            {
                this.loading = true
                if(this.$refs.form.validate()){
                    axios.post(`/api/user_position`, this.user_position)
                    .then(response => {
                        EventBus.$emit(this.eventName, response)
                        EventBus.$emit('clearAccesses', response)
                        this.toaster(response.data, response.status)
                        this.clear()
                        this.close()
                    })
                    .catch(error => {
                        this.error_message  = error.response.data
                        this.toaster(this.error_message, error.response.status)
                        EventBus.$emit(this.eventName)
                    })
                    .finally(() => {
                        this.loading = false
                    })
                } else {
                    this.loading = false
                }
            },
            submit() {
                this.$refs.submit_button.click()
            },
            close() {
                this.dialog = false
            },
            clear() {
                this.user_position.user_position_accesses = []
                this.$refs.form.reset()
            },

        }
    }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

    .edit-description {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .main-holder {
            border: 1px solid #ededed;
            padding: 40px;

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            .v-input--radio-group {
                margin-top: 0px !important;

                .v-input__slot {
                    margin-bottom: 0px !important;
                }

            }
            .v-btn
            {
                margin: 0px;
            }
        }
    }
</style>