<template>
    <span>
        <!-- <span @click="popup_archive_procedure_type = true">
            <slot></slot>
        </span> -->
        <v-dialog v-model="popup_archive_procedure_type" persistent scrollable max-width="400px">
            <span slot="activator">
                <slot></slot>
            </span>
        <v-card class="archive-procedures">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Archive  Procedures Type</v-card-title>
            <v-form ref="form" @submit.prevent="save">
                <v-card-text>
                    <div class="main-holder">
                        You are about to {{ !item.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{ item.name }}"</span> procedures type. Are you sure you want to do this?
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
                popup_archive_procedure_type: false,
                loading: false,
            }
        },
         props : {
            item:{
                type: Object,
            },
            eventName: {
                type: String,
                default: 'archiveProceduresType'
            }
        },
        methods: {
        save() {
            this.loading = true
            this.item.for_archive = true
            axios.put(`/api/procedures_type/${ this.item.id }`,this.item)
            .then(response => {
                 console.log(response.data.data)
                    EventBus.$emit(this.eventName, response)
                    this.toaster(response.data.data, response.status)
                    this.close()
                })
                .catch(error => {
                    //console.log(error.response.data.data)
                    this.toaster(error.response.data.data, error.response.data.status)
                })
                .finally(() => {
                    this.loading = false
                })
            },
        close() {
            EventBus.$emit(this.eventName)
            this.popup_edit_procedures_type = false
            this.popup_archive_procedure_type = false
            //this.loading = false
            }
        },
        
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .archive-procedures {
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