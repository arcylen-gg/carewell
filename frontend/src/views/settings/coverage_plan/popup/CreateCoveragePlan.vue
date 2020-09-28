<template>
    <v-dialog scrollable max-width="1000px">
        <v-btn slot="activator" depressed color="accent"><i class="fas fa-plus mr-3"></i>Create Coverage Plan</v-btn>
    </v-dialog>
</template>

<script>
    export default {
        data() {
            return {
                existing: ['Waived'],
                popup_coverage_plan: false,
            }
        }
    }
</script>


<style lang="scss">
    @import "../../../../scss/app.scss";

    .create-coverage__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .top-holder {
            border: 1px solid #ededed;
            padding: 15px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 50px;
            margin-bottom: 10px;

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
        }

        .bottom-holder {
            padding: 15px;
            border: 1px solid #ededed;

            .benefits-holder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
                grid-row-gap: 15px;
            }
        }


    }
</style>