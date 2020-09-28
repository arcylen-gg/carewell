<template>
    <span>
      <span @click="loadDialog" style="cursor: pointer">
            <slot><v-icon>mdi-eye</v-icon> View Data</slot>
        </span>
        <v-dialog v-model="dialog" persistent max-width="400px">
        <v-card class="view-procedures">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Edit Plan Description</v-card-title>
            <v-card-text>
                <div class="main-holder">
                        <v-text-field color="accent" label="Name" v-model="item.name" outline readonly ></v-text-field>
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
                dialog: false,
            }
        },
        props : {
            item:{
                type: Object,
            },
            eventName: {
                type: String,
                default: 'editProceduresType'
            }
        },
        methods : {
            close() {
            EventBus.$emit(this.eventName)
            this.dialog = false
            },
            loadDialog() {
            this.dialog = true
        }
        }

    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .view-procedures {
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