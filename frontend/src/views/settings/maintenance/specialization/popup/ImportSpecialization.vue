<template>
    <v-dialog v-model="popup_import_description" scrollable max-width="500px">
        <v-btn slot="activator" depressed color="success" dark><v-icon class="mr-3">mdi-file-import</v-icon>Import Plan Description</v-btn>
        <v-card class="import-specialization">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Import Plan Description</v-card-title>
            <v-card-text>
                <div class="main-holder">
                    <b-form-file v-model="file" :state="Boolean(file)" placeholder="Download template"></b-form-file>
                    <b-form-file v-model="file2" :state="Boolean(file2)" placeholder="Choose file" class="mt-3"></b-form-file>
                    <div class="btn-holder">
                        <v-btn color="accent" class="mt-4" depressed>Import File</v-btn>
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
                file: null,
                file2: null,
                type: ['APE', 'Laboratory'],
                popup_import_description: false,
            }
        }
    }
    
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .import-description {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .main-holder {
            border: 1px solid #ededed;
            padding: 40px;

            .btn-holder {
                text-align: center;
            }
        }
    }
</style>