<template>
    <span>
        <div class="payment-history__menu">
            <v-select color="accent" 
                :items="select_company" 
                item-text="name" 
                item-value="id" 
                label="Filter by Company"
                outline
                @change="filterData"
                v-model="filters.company_id"
            />
            <v-btn depressed color="info" class="mt-2">Actual</v-btn>
            <v-btn dark depressed color="success" class="mt-2">Distributed</v-btn>
        </div>
        <div class="payment-table">
            <v-data-table :headers="payment_headers" :items="itemData" class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-center">{{ props.item.carewell_id }}</td>
                    <td class="text-xs-center">{{ props.item.cal.cal_number }}</td>
                    <td class="text-xs-center">{{ props.item.cal.payment_start_date | mixin_change_date_format }}</td>
                    <td class="text-xs-center">{{ props.item.cal.payment_end_date | mixin_change_date_format }}</td>
                    <td class="text-xs-center">{{ props.item.paid_amount | mixin_change_currency_format }}</td>
                </template>
            </v-data-table>
        </div>
    </span>
</template>
<script>
export default {
    data: () => ({
        payment_headers: [
            {text: 'CAREWELL ID',value: 'carewell_id'},
            {text: 'CAL NO.',value: 'cal_no'},
            {text: 'PAYMENT START',value: 'payment_start'},
            {text: 'PAYMENT END',value: 'payment_end'},
            {text: 'PAYMENT AMOUNT',value: 'payment_amount'},
        ],
        filters : {},
        itemData : []
    }),
    props : {
        item: {
            type: Array,
            default : () => []
        },
        select_company:{
            type: Array,
            default: () => []
        },
    },
    methods : {
        async filterData(){
            if(this.filters.company_id === 'all')
            {
                this.itemData = this.item
            }
            else
            {
                let data = await this.item.filter((item) => item.cal.company_id == this.filters.company_id);
                this.itemData = data;
            }
        },
    },
    mounted(){
        this.itemData = this.item;
        this.filters = {
            company_id : 'all',
        }
    }
}
</script>
