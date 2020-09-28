<template>
    <div>
        <div>
            <h4><slot name="title"></slot></h4>
        </div>
        <v-data-table 
            v-model="selected"
            :headers="headers" 
            :items="availment_data" 
            item-key="data"
            :pagination.sync="pagination"
            class="elevation-1"
            hide-actions
        >
            <template slot="items" slot-scope="props">
                <td nowrap><strong>{{ props.item.name }}</strong></td>
                <td nowrap class="set-td-width">
                    <v-menu
                        v-model="release_date[props.index]"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        lazy
                        transition="scale-transition"
                        offset-y
                        full-width
                        min-width="290px"
                    >
                        <date-format
                            slot="activator"
                            v-model="availment_data[props.index].release_date"
                            :default="availment_data[props.index].release_date"
                            label="Release Date"
                            append-icon="event"
                            outline
                            readonly
                            :rules="requiredRules"
                        ></date-format>
                        <v-date-picker v-model="availment_data[props.index].release_date" @input="release_date[props.index] = false"></v-date-picker>
                    </v-menu>
                </td>
                <td nowrap>
                    <v-text-field v-model="availment_data[props.index].check_number" :rules="requiredRules"></v-text-field>
                </td>
                <td nowrap class="set-td-width">
                    <v-menu
                        v-model="check_date[props.index]"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        lazy
                        transition="scale-transition"
                        offset-y
                        full-width
                        min-width="290px"
                    >
                        <date-format
                            slot="activator"
                            v-model="availment_data[props.index].check_date"
                            :default="availment_data[props.index].check_date"
                            label="Due Date"
                            append-icon="event"
                            outline
                            readonly
                            :rules="requiredRules"
                        ></date-format>
                        <v-date-picker v-model="availment_data[props.index].check_date" @input="check_date[props.index] = false"></v-date-picker>
                    </v-menu>
                </td>
                <td>
                    <v-text-field v-model="availment_data[props.index].cv_number" :rules="requiredRules"></v-text-field>
                </td>
                <td nowrap><strong>{{ currency_format('₱',props.item.total_amount) }}</strong></td>
                <td nowrap class="set-td-width">
                    <global-select-box
                        v-model="availment_data[props.index].bank_id"
                        :default="availment_data[props.index].bank_id"
                        :filters="{is_archived:0}"
                        :rules="requiredRules"
                        api-link="api/bank"
                        class="c-input "
                        label="Select Bank"
                        color="accent" 
                        item-text="name"
                        item-value="id"
                        is-load
                        solo
                        flat
                    >
                    </global-select-box>
                </td>
                <td nowrap>
                    <v-text-field v-model="availment_data[props.index].received_by" :rules="requiredRules"></v-text-field>
                </td>
                <td>
                    <strong>{{props.item.approval_id}}</strong>
                </td>
            </template>
        </v-data-table>
    </div>
</template>
<script>
export default {
    data : () => ({
        selected: [],
        release_date : [],
        check_date : [],
        headers: [
            {text: 'PAYEE',value: 'payee_id'}, 
            {text: 'RELEASE DATE',value: 'release_date', width:"250"}, 
            {text: 'CHEQUE NUMBER',value: 'cheque_number'}, 
            {text: 'CHEQUE DATE',value: 'cheque_date', width:"250"}, 
            {text: 'CV NUMBER',value: 'cv_number'}, 
            {text: 'AMOUNT',value: 'amount'}, 
            {text: 'BANK',value: 'bank_id',width:"250"}, 
            {text: 'RECEIVED BY',value: 'received_by'}, 
            {text: 'REFERENCE APPROVAL',value: 'reference_approval'}, 
        ],
        pagination:{
            rowsPerPage: -1
        },
        requiredRules: [
            v => !!v || 'Input is required',
        ],
    }),
    props : {
        availment_data : {
            type:Array,
            default: () => []
        }
    },
}
</script>

<style lang="scss">
// td {
//   width: 50px;
// }
.set-td-width
{
  width: 20em;
}
</style>


