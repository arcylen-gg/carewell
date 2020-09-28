<template>
    <span>
        
    </span>
</template>

<script>
    export default {
        data() {
            return {
                radios: '2',
                existing: ['Waived'],
                popup_account_settings: false,
                select_position: ['Admin', 'All Access'],
            }
        }
    }
</script>

<style lang="scss">
    @import "../scss/app.scss";

    .account-settings {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .top-holder {
            border: 1px solid #ededed;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 20px;
            margin-bottom: 10px;

            .textfield-container
            {
                align-self: center;
            }

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #ced4da !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }
        }

        .bottom-holder {
            padding: 20px 20px 10px;
            border: 1px solid #ededed;

            .bottom-holder__info
            {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 40px;
            }

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #ced4da !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }
        }
    }
</style>