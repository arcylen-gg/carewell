<template>
    <v-dialog v-model="popup_import_member" scrollable max-width="500px">
        <!-- <v-btn slot="activator" color="accent" depressed><v-icon class="mr-3">mdi-file-import</v-icon>Import from Excel</v-btn> -->
        <v-btn style="width: 96%;" slot="activator" color="success" depressed><v-icon class="mr-3">mdi-file-import</v-icon> Import from Excel</v-btn>
        <v-card class="import-file-provider">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Import File Provider</v-card-title>
            <v-card-text>
                <div class="import-holder">
                    <b-form-file v-model="file" :state="Boolean(file)" placeholder="Download Template"></b-form-file>
                    <b-form-file v-model="file2" :state="Boolean(file2)" placeholder="Choose Excel File" class="mt-4"></b-form-file>
                    <div class="btn-holder">
                        <v-btn depressed color="accent" class="mt-4" @click="popup_import_member = false">Import Excel File</v-btn>
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
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

    .import-file-provider {
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