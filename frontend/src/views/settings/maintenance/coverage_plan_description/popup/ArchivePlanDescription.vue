<template>
    <span>
       <span @click="loadDialog" style="cursor: pointer">
            <slot><v-icon>mdi-delete</v-icon> Archive Data</slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="400px">
            <v-card class="view-description">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Archive Plan Description</v-card-title>
                <v-form ref="form" @submit.prevent="save">
                    <v-card-text>
                        <div class="main-holder">
                            You are about to {{ !item.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{ item.name }}"</span> plan description. Are you sure you want to do this?
                        </div>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                        <v-btn color="accent" depressed type="submit" :loading="loading">{{item.is_archived == 0 ? 'ARCHIVE' : 'RESTORE'}}</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
    </v-dialog>
    </span>
</template>

<script>
    export default {
        data() {
            return {
                type: ['APE', 'Laboratory'],
                loading: false,
                dialog: false,
            }
        },
         props : {
            item:{
                type: Object,
            },
            eventName: {
                type: String,
                default: 'archivePlanDescription'
            }
        },
        methods: {
        loadDialog(){
                this.dialog = true
                this.item.is_archived == 0 ? this.formTitle = 'Archive Details' : this.formTitle = 'Restore Details'
            },   
        save() {
            this.loading = true
            this.item.for_archive = true
            axios.put(`/api/procedures/${ this.item.id }`,this.item)
            .then(response => {
                    EventBus.$emit(this.eventName, response)
                    this.get_procedure_types();
                    this.toaster(response.data.data, response.status)
                    this.close()
                })
                .catch(error => {
                    this.toaster(error.response.data.data)
                })
                .finally(() => {
                    this.loading = false
                })
            },
        close() {
            EventBus.$emit(this.eventName)
            this.dialog = false
            //this.loading = false
            }
        },
        
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .view-description {
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