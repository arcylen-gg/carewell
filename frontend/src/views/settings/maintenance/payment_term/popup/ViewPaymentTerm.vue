<template>
<span>
    <span @click="loadDialog">
                <slot></slot>
    </span>
    <v-dialog v-model="dialog" scrollable persistent max-width="500px">
        <v-card class="view-existing">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">View Payment Term</v-card-title>
            <v-card-text>
                <div class="main-holder">
                    <v-text-field color="accent" v-model="form.name" label="Name" :rules="requiredRules" outline readonly></v-text-field>
                    <v-text-field color="accent" v-model="form.number"  type="Number" label="Number of Days" :rules="requiredRules" outline readonly></v-text-field>
                </div>
            </v-card-text>
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
        data :() => ({
            dialog: false,
            requiredRules: [
                    v => !!v || 'Input is required',
                ],
        }),
        props : {
            form : {
                type:Object
            },
        },
        methods:{
            loadDialog(){
                this.dialog = true
            },
            close(){
                this.dialog = false
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .view-existing {
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