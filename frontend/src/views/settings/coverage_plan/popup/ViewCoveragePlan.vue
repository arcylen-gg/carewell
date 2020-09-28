<template>
    <span>
        <span @click="popup_view_coverage = true">
            <slot></slot>
        </span>
        <v-dialog v-model="popup_view_coverage" scrollable max-width="1000px">
            <v-card class="view-coverage-plan">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">View Coverage Plan</v-card-title>
                <v-card-text>
                    <div class="top-holder">
                        <v-text-field color="accent" label="Plan Name" outline value="Ward Plan II" readonly></v-text-field>
                        <v-text-field color="accent" label="Monthly Premium" outline value="550" readonly></v-text-field>
                        <v-text-field color="accent" label="Age Bracket" outline value="16 - 20" readonly></v-text-field>
                        <v-text-field color="accent" label="Handling Fee" outline value="13.5%" readonly></v-text-field>
                        <v-text-field color="accent" label="Processing Fee" outline value="0" readonly></v-text-field>
                        <v-text-field color="accent" label="Card Fee" outline value="0" readonly></v-text-field>
                        <v-text-field color="accent" label="Hospital Income Benefits (HIB)" outline
                            value="0" readonly></v-text-field>
                        <v-text-field color="accent" label="Pre-Existing" outline value="Waived" readonly></v-text-field>
                        <v-text-field color="accent" label="Annual Benefit Limit (ABL)" outline value="120000"
                            readonly></v-text-field>
                        <v-text-field color="accent" label="Maximum Benefit Limit (MBl)" outline value="60000"
                            readonly></v-text-field>
                        <div></div>
                        <v-radio-group row readonly v-model="radios" :mandatory="false">
                            <v-radio label="Year" value="1"></v-radio>
                            <v-radio label="Illness / Disease" value="2"></v-radio>
                        </v-radio-group>
                    </div>
                    <div class="bottom-holder">
                        <div class="title text-center text-dark font-weight-bold">TYPE OF BENEFITS</div>
                        <div class="bottom-holder__info mb-2">
                            <div class="body-2 font-weight-bold mt-3">Annual Physical Examination (APE)</div>
                            <div class="bottom-holder__info-subtitle">
                                <div class="body-1 text-dark font-weight-medium">Charge: Charged to MBL</div>
                                <div class="body-1 text-dark font-weight-medium">Amount Covered: 1200</div>
                                <div class="body-1 text-dark font-weight-medium">Limit Yearly: 1</div>
                                <div class="body-1 text-dark font-weight-medium"></div>
                                <div class="body-1 text-dark font-weight-medium">Per Confinement Amount: 0</div>
                                <div class="body-1 text-dark font-weight-medium">Limit Monthly: 0</div>
                            </div>
                        </div>
                        <v-expansion-panel dark>
                            <v-expansion-panel-content>
                                <div slot="header" class="text-center font-weight-bold subheading">Procedure</div>
                                <v-card light>
                                    <v-data-table :headers="headers" :items="table_view_procedure" class="text-xs-center">
                                        <template slot="items" slot-scope="props">
                                            <td class="text-xs-center">{{ props.item.view_procedure_consultation }}</td>
                                            <td class="text-xs-center">{{ props.item.view_procedure_amount }}</td>
                                        </template>
                                    </v-data-table>
                                </v-card>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                        <div class="bottom-holder__info mb-2">
                            <div class="body-2 font-weight-bold mt-3">Outpatient Consultation</div>
                            <div class="bottom-holder__info-subtitle">
                                <div class="body-1 text-dark font-weight-medium">Charge: Charged to MBL</div>
                                <div class="body-1 text-dark font-weight-medium">Amount Covered: 350</div>
                                <div class="body-1 text-dark font-weight-medium">Limit Yearly: 1</div>
                                <div class="body-1 text-dark font-weight-medium"></div>
                                <div class="body-1 text-dark font-weight-medium">Per Confinement Amount: 0</div>
                                <div class="body-1 text-dark font-weight-medium">Limit Monthly: 0</div>
                            </div>
                        </div>
                        <v-expansion-panel dark>
                            <v-expansion-panel-content>
                                <div slot="header" class="text-center font-weight-bold subheading">Procedure</div>
                                <v-card light>
                                    <v-data-table :headers="headers" :items="table_view_procedure" class="text-xs-center">
                                        <template slot="items" slot-scope="props">
                                            <td class="text-xs-center">{{ props.item.view_procedure_consultation }}</td>
                                            <td class="text-xs-center">{{ props.item.view_procedure_amount }}</td>
                                        </template>
                                    </v-data-table>
                                </v-card>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
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
                radios: '2',
                existing: ['Waived'],
                popup_view_coverage: false,
                headers: [{
                    text: 'CONSULTATION',
                    value: 'view_procedure_consultation'
                }, 
                {
                    text: 'AMOUNT',
                    value: 'view_procedure_amount'
                }, 
            ],
            table_view_procedure: [{
                value: false,
                view_procedure_consultation: 'Chest X-Ray',
                view_procedure_amount: '300',
            },{
                value: false,
                view_procedure_consultation: 'Complete Blood Count',
                view_procedure_amount: '400',
            },{
                value: false,
                view_procedure_consultation: 'Fecalysis',
                view_procedure_amount: '500',
            },{
                value: false,
                view_procedure_consultation: 'Physical Exam',
                view_procedure_amount: '600',
            }],
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

    .view-coverage-plan {
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

            .bottom-holder__info {
                .bottom-holder__info-subtitle {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    grid-column-gap: 20px;
                }
            }

            .v-expansion-panel__header {
                background-color: #1E3581 !important;
            }
            table
            {
                thead
                {
                    tr
                    {
                        th
                        {
                            text-align: center !important;
                        }
                    }
                }
            }
        }
    }
</style>