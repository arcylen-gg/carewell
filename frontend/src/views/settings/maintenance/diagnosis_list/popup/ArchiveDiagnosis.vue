<template>
    <span>
        <span @click="archive_diagnosis = true">
            <slot></slot>
        </span>
        <v-dialog v-model="archive_diagnosis" scrollable persistent max-width="600px">
            <v-card class="edit-user">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">{{formTitle}}</v-card-title>
                <v-form ref="form" @submit.prevent="save">
                    <v-card-text>
                        <div class="main-holder">
                            You are about to {{ !item.is_archived ? 'archive' : 'restore'}} <strong>"{{ item.name }}"</strong> diagnosis. Are you sure you want to do this?
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
        data : () => ({
            formTitle: 'Archive Details',
            archive_diagnosis: false,
            loading : false,
        }),
        props : {
            item : {
                type:Object
            },
            eventName: {
                type: String,
                default: 'archiveDiagnosis'
            }
        },
        methods : {
            loadDialog(){
                this.archive_diagnosis = true;
                this.form.is_archived == 0 ? this.formTitle = 'Archive Details' : this.formTitle = 'Restore Details'
            },
            save(){
                this.loading = true
                this.item.for_archive = true
                axios.put(`/api/diagnosis_list/${ this.item.id }`,this.item)
                .then(response => {
                    EventBus.$emit(this.eventName, response)
                    this.toaster(response.data.data, response.status)
                    this.close()
                })
                .catch(error => {
                    this.error_message  = error.response.data.data
                    EventBus.$emit(this.eventName)
                    this.toaster(this.error_message, error.response.status)
                })
                .finally(() => {
                    this.loading = false
                })
            },
            close(){
                this.archive_diagnosis = false
            }
        },
        mounted(){

        }

    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .edit-user {
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