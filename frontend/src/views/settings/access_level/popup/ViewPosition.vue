<template>
    <span>
        <!-- <span block color="primary" dark @click="loadDialog"><slot>Open Dialog</slot></span> -->
        <span @click="loadDialog" style="cursor: pointer">
            <slot><v-icon>mdi-eye</v-icon> View Data</slot>
        </span>
        <v-dialog v-model="dialog" persistent scrollable max-width="400px">
            <v-card class="edit-description">
                <!-- Dialog Title -->
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">
                    <span class="headline white--text">View User Position</span>
                </v-card-title>
                <!-- Dialog Content -->
                <v-card-text>
                    <div class="main-holder">
                        <v-text-field color="accent" outline label="Name" v-model="item.name" :disabled="true"></v-text-field>
                    </div>
                </v-card-text>
                <!-- Dialog Buttons -->
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>
<script>
  export default {
    data: () => ({
        dialog: false,
    }),
    computed : {
    },
    props : {
        eventName: {
          type: String,
          default: 'viewUserPosition'
        },
        item: {
            type: Object,
        }
    },
    methods: {
        close() {
            this.dialog = false
        },
        loadDialog() {
            this.dialog = true
        }
    },
    mounted () {
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