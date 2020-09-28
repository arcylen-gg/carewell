<template>
    <v-dialog v-model="popup_image_contract" scrollable max-width="950px" lazy>
        <v-btn class="ml-0" color="success" depressed slot="activator" style="width: 100%; height: 40px;">Image Contract</v-btn>
        <v-card class="image-contract">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Image Contract</v-card-title>
            <v-card-text>
                <div class="contract-table">
                    <v-data-table :headers="contract_headers" :items="contract_items" class="elevation-1">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left py-2"><img :src="props.item.image_contract" style="height: 100px;"></td>
                            <td class="text-xs-left">
                                <v-btn color="info" depressed dark>{{ props.item.action }}</v-btn>
                            </td>
                        </template>
                    </v-data-table>
                </div>
            </v-card-text>
             <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="tertiary" depressed dark @click="popup_image_contract = false">CLOSE</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data() {
            return {
                popup_image_contract: false,
                contract_headers: [{
                        text: 'IMAGE CONTRACT',
                        value: 'image_contract'
                    },
                    {
                        text: 'ACTION',
                        value: 'action'
                    },
                ],
                contract_items: [{
                        value: false,
                        image_contract: require('../../../assets/carewell-logo.jpg'),
                        action: 'Delete',
                    },
                ],
            }
        },
        watch: {
            availment_menu(val) {
                val && this.$nextTick(() => (this.$refs.availment_picker.activePicker = 'YEAR'))
            },
        },
        methods: {
            availment_save(date) {
                this.$refs.availment_menu.save(date)
            },
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .image-contract {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .v-card {
            border: 1px solid #ededed;
        }

        .payment-history {
            .payment-history__menu {
                display: grid;
                grid-template-columns: repeat(3, 1fr);

                .v-text-field {
                    font-size: 15px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                    }
                }

                .v-btn {
                    height: 40px;
                }
            }

            table {
                thead {
                    tr {
                        th {
                            text-align: center !important;
                        }
                    }
                }
            }
        }

        .availment-history {
            .availment-history__menu {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-column-gap: 10px;

                .v-btn {
                    height: 40px;
                }

                .v-text-field {
                    font-size: 13px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                    }
                }
            }
        }

    }
</style>