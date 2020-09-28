<template>
    <v-data-table
        :headers="headers"
        :items="cal_members"
        class="elevation-1"
        align='center'
    >
        <template slot="items" slot-scope="props">
            <td class="text-xs-center">{{ props.item.member.company.universal_id  }}</td>
            <td class="text-xs-center">{{ props.item.member.company.carewell_id }}</td>
            <td class="text-xs-center">{{ props.item.member.first_name }}</td>
            <td class="text-xs-center">{{ props.item.member.middle_name }}</td>
            <td class="text-xs-center">{{ props.item.member.last_name }}</td>
            <td class="text-xs-center">{{ props.item.member.birth_date | mixin_change_date_format }}</td>
            <td class="text-xs-center">{{ props.item.member.company.company_deployment.name }}</td>
            <td class="text-xs-center">{{ props.item.monthly_premium }}</td>
            <td class="text-xs-center">{{ props.item.period_count }}</td>
            <td class="text-xs-center">{{ props.item.paid_amount }}</td>
            <td class="text-xs-center">{{ props.item.member.is_archived == 0 ? 'active' : 'inactive' }}</td>
        </template>
        <template slot="no-data">
            <v-alert :value="true" color="error" icon="warning">
                No CAL Members on this CAL.
            </v-alert>
        </template>
    </v-data-table>
</template>
<script>
export default {
    data : () => ({
        headers: [
            {text: 'Universal ID', value: 'member'}, 
            {text: 'Carewell ID',value: 'carewell_id'}, 
            {text: 'First Name',value: 'member'}, 
            {text: 'Middle Name',value: 'middle_name'}, 
            {text: 'Last Name',value: 'last_name'}, 
            {text: 'Birth Date',value: 'birth_date'}, 
            {text: 'Deployment',value: 'deployment'}, 
            {text: 'Monthly Premium',value: 'monthly_premium'}, 
            {text: 'Period Count',value: 'period_count'}, 
            {text: 'Paid Amount',value: 'paid_amount'}, 
            {text: 'Member Status',value: 'member'},
        ],
    }),
    props : {
        cal_members: {
            type: Array,
            default : () => []
        }
    }

}
</script>
