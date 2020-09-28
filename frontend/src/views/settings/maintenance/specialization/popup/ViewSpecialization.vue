<template>
    <span>
       <span @click="loadDialog">
                <slot></slot>
            </span>
        <v-dialog persistent  v-model="dialog" scrollable max-width="500px">
            
        <v-card class="view-specialization">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">View Specialization</v-card-title>
            <v-card-text>
                <div class="main-holder">
                    <v-text-field readonly color="accent" label="Name" v-model="item.name" outline ></v-text-field>
                </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>   
                <v-spacer></v-spacer>
                <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
            </v-card-actions>
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
                type: ['APE', 'Laboratory'],
                popup_edit_procedures_type: false,
                loading: false,
                dialog: false
            }
        },
        props : {
            item:{
                type: Object,
            },
            eventName: {
                type: String,
                default: 'editSpecialization'
            }
        },
        methods : {
            save(){
                this.loading = true
                if(this.$refs.form_submit.validate())
                {
                    axios.put(`/api/specialization/${this.item.id}`, this.item)
                    .then(response => {
                        EventBus.$emit(this.eventName, response)
                        this.close()
                        console.log(response)
                        //this.toaster(response.data, response.status)
                        this.close()
                    })
                    .catch(error => {
                        console.log(error.response, error)
                        //this.toaster(error.response.data, error.status)
                    })
                    .finally(() => {
                        this.loading = false
                    })
                    }
                else
                {
                    this.loading     = false;
                }
            },
            close() {
            EventBus.$emit(this.eventName)
            this.dialog = false
            },
            loadDialog()
            {
                this.dialog = true
            }
        }

    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

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