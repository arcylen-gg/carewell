<template>
    <span>
        <span @click="popup_import_member = true">
            <slot></slot>
        </span>
        <v-dialog v-model="popup_import_member" scrollable max-width="500px">
            <v-card class="import-file-member">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Import File</v-card-title>
                <v-card-text>
                    <div class="import-holder">
                        <v-text-field color="accent" mask="############" label="Number of Member" outline></v-text-field>
                        <b-form-file v-model="file" :state="Boolean(file)" placeholder="Download Template"></b-form-file>
                        <b-form-file v-model="file2" :state="Boolean(file2)" placeholder="Choose Excel File" class="mt-4"></b-form-file>
                        <div class="btn-holder">
                            <v-btn depressed color="accent" class="mt-4" @click="popup_import_member = false">Import Excel File</v-btn>
                        </div>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default {
        data() {
            return {
                popup_import_member: false,
                file: null,
                file2: null,
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .import-file-member {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .import-holder {
            border: 1px solid #ededed;
            padding: 30px;
            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            .btn-holder
            {
                text-align: center;
            }
        }
    }
</style>