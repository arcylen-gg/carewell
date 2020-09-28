<template>
    <div>
        <span><strong>Acoounting Period:</strong></span>
        <v-layout row wrap>
            <v-flex xs12 sm6 md4>
                <v-menu
                    v-model="menu_from"
                    :close-on-content-click="false"
                    :nudge-right="0"
                    lazy
                    transition="scale-transition"
                    offset-y
                    fuaria-rowindex=""
                    min-width="200px">
                    <v-text-field
                        slot="activator"
                        v-model="convertedDateFrom"
                        label="Date From"
                        hint="MM-DD format"
                        persistent-hint
                        append-icon="event"
                        outline
                        readonly>
                    </v-text-field>
                    <v-date-picker v-model="picker_from" @input="datepicker_accounting_from = false" type="date"></v-date-picker>
                </v-menu>
            </v-flex>
            <v-flex xs12 sm6 md4>
                <v-menu
                    v-model="menu_to"
                    :close-on-content-click="false"
                    :nudge-right="0"
                    lazy
                    transition="scale-transition"
                    offset-y
                    fuaria-rowindex=""
                    min-width="200px">
                    <v-text-field
                        slot="activator"
                        v-model="convertedDateTo"
                        label="Date To"
                        hint="MM-DD format"
                        persistent-hint
                        append-icon="event"
                        outline
                        readonly>
                    </v-text-field>
                    <v-date-picker v-model="picker_to" @input="datepicker_accounting_to = false" type="date"></v-date-picker>
                </v-menu>
            </v-flex>
            <v-flex xs12 sm6 md4>
                <global-select-box
                    v-model="form.mode_of_payment"
                    api-link="api/payment_mode"
                    item-text="name"
                    item-value="id"
                    label="Select Mode of Payment" 
                    :filters="{is_archived:0}"
                    is-load
                    outline 
                >
                </global-select-box>
            </v-flex>
        </v-layout>
    </div>
</template>
<script>
import { settingData } from '../SettingStore.js';

export default {
    data : () => ({
        menu_from : false,
        datepicker_accounting_from: false,
        picker_from: new Date().toISOString().substr(0,10),
        menu_to : false,
        datepicker_accounting_to: false,
        picker_to: new Date().toISOString().substr(0,10),
        select_payment: [],
        form : {},
        row : null,
    }),
    computed : {
        convertedDateFrom : function () {
            return this.picker_from.substr(5)
        },
        convertedDateTo : function () {
            return this.picker_to.substr(5)
        }
    },
    methods : {
     
    },
    mounted(){
 
    }
}
</script>

