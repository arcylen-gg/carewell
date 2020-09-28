<template>
    <span>
       <span @click="loadDialog" style="cursor: pointer">
            <slot><v-icon>mdi-delete</v-icon> Archive Data</slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="400px">
            <v-card class="view-description">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">{{item.is_archived == 0 ? 'ARCHIVE' : 'RESTORE'}} Member</v-card-title>
                    <v-form ref="form" @submit.prevent="save">
                        <v-card-text>
                            <div class="edit-user__holder">
                                You are about to {{ !item.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{item.first_name}} {{item.last_name}}"</span>. Are you sure you want to do this?
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
                popup_archive_plan_description: false,
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
            axios.put(`/api/member/${ this.item.id }`,this.item)
            .then(response => {
                    EventBus.$emit(this.eventName, response)
                    this.toaster(response.data.data, response.status)
                    this.close()
                    // this.get_count();
                })
                .catch(error => {
                    // EventBus.$emit(this.eventName, '')
                    this.toaster(error.response.data.message, error.response.status)
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
    @import "../../../scss/app.scss";

    .edit-member {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .top-holder {
            border: 1px solid #ededed;
            padding: 15px;
            margin-bottom: 10px;

            .top-input__holder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
            }

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            textarea {
                height: 55px;
            }

            .present-address {
                .v-input__slot {
                    margin-bottom: 0px;
                }

                .v-input {
                    margin-top: 0px;
                }

                label {
                    margin-bottom: 0px;
                }

                textarea {
                    height: 55px;
                }
            }
        }

        .mid1-holder {
            padding: 15px;
            border: 1px solid #ededed;
            margin-bottom: 10px;

            .mid1-input__holder {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr auto;
                grid-column-gap: 20px;

                .v-text-field {
                    font-size: 15px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                    }
                }

                .v-text-field__details {
                    margin-bottom: 0px !important;
                }
            }


        }

        .mid2-holder {
            border: 1px solid #ededed;
            padding: 15px;
            margin-bottom: 10px;

            .mid2-input__holder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;

                .v-text-field {
                    font-size: 15px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                    }
                }

                .v-text-field__details {
                    margin-bottom: 0px !important;
                }
            }
        }

        .bottom-holder {
            padding: 15px;
            border: 1px solid #ededed;

            .bottom-holder__title
            {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                .subheading{
                    align-self: center;
                }
                .btn-holder
                {
                    justify-self: end;
                }
            }
            .bottom-input__holder
            {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;

                .v-text-field {
                    font-size: 15px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                    }
                }

                .v-text-field__details {
                    margin-bottom: 0px !important;
                }
            }
        }
    }
</style>