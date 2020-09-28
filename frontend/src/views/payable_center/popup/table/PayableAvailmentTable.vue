<template>
    <div class="payable-availment">
        <div class="payable-availment-table">
            <div class="table-search-field">
                <h4>APPROVAL LIST</h4>
                <span></span>
                <span></span>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Search Approval Number"
                    single-line
                    hide-details
                    solo flat
                    class="c-input"
                ></v-text-field>
            </div>
        </div>
        <v-data-table 
            v-model="selected"
            :headers="headers" 
            item-key="id"
            :items="tableAvailmentData.item" 
            :pagination.sync="pagination"
            :loading = "tableAvailmentData.tableLoading"
            select-all
            :search="search" 
            :custom-filter="customFilter"
            class="elevation-1"
        >
            <template slot="items" slot-scope="props">
                <td>
                    <v-checkbox
                        v-model="props.selected"
                        primary
                        hide-details
                    />
                </td>
                <td><v-btn @click="adjustAvailment(props.item.id)">{{ props.item.approval_id }}</v-btn></td>
                <td>{{ props.item.benefit_type ? props.item.benefit_type.name : "" }}</td>
                <td nowrap><show-for-each-data :forEachData="{obj:props.item.availment_doctor,propertyAffected:'doctor'}"></show-for-each-data></td>
                <td nowrap><show-for-each-data :forEachData="{obj:props.item.availment_procedure,propertyAffected:'procedure'}"></show-for-each-data></td>
                <!-- <td>{{ props.item.diagnosis.name }}</td> -->
                <td>{{ props.item.diagnosis ?  props.item.diagnosis.name : ""}}</td>
                <td>{{ props.item.chief_complaint }}</td>
                <td>{{ props.item.member.company.universal_id }}</td>
                <td>{{ props.item.member.company.carewell_id }}</td>
                <td>{{ props.item.member.fullname }}</td>
                <td>{{ props.item.member.birth_date | mixin_change_date_format }}</td>
                <td>{{ props.item.member.age }}</td>
                <td>{{ props.item.member.gender }}</td>
                <td>{{ props.item.member.company.company.name }}</td>
                <td>{{ props.item.provider.name }}</td>
                <td>{{ props.item.date_avail | mixin_change_date_format }}</td>
                <td>{{ props.item.provider_payee_fee }}</td>
                <td>{{ props.item.doctor_fee }}</td>
                <td nowrap>{{ currency_format('₱',props.item.grand_total) }}</td>
                <td>
                    <currency-format
                        v-model="props.item.payable_amount"
                        :disabled="!props.selected"
                        reverse
                    >
                    </currency-format>
                </td>
                <td nowrap>{{ currency_format('₱', getBalance(props.item.grand_total,props.item.payable_amount)) }}</td>
                <td>
                    <v-text-field
                        v-model="props.item.remarks"
                        :disabled="!props.selected"
                    >
                    </v-text-field>
                </td>
            </template>
        </v-data-table>
    </div>
</template>
<script>
import ShowForEachData from '../sub_component/ShowForEachData'

export default {
    components:{
        ShowForEachData
    },
    data : () => ({
        search :'',
        tableLoading : false,
        selected: [],
        headers: [
            {text: 'APPROVAL NUMBER',value: 'approval_id'}, 
            {text: 'BENEFIT TYPE',value: 'benefit_type'}, 
            {text: 'DOCTORS',value: 'doctor'},
            {text: 'PROCEDURES',value: 'procedure'},
            {text: 'DIAGNOSIS',value: 'diagnosis'},
            {text: 'CHIEF COMPLAINT',value: 'chief_complaint'},
            {text: 'UNIVERSAL ID',value: 'universal_id'}, 
            {text: 'CAREWELL ID',value: 'carewell_id'}, 
            {text: 'MEMBER NAME',value: 'full_name'}, 
            {text: 'BIRTHDATE',value: 'birthdate'}, 
            {text: 'AGE',value: 'age'}, 
            {text: 'GENDER',value: 'gender'}, 
            {text: 'COMPANY',value: 'company_id'}, 
            {text: 'PROVIDER',value: 'provider_id'}, 
            {text: 'DATE AVAIL',value: 'date_avail'}, 
            {text: 'HOSPITAL FEE',value: 'hospital_fee'}, 
            {text: 'PROFESSIONAL FEE',value: 'professional_fee'}, 
            {text: 'AMOUNT',value: 'amount'}, 
            {text: 'PAYMENT AMOUNT',value: 'payment_amount'}, 
            {text: 'BALANCE',value: 'balance'}, 
            {text: 'REMARKS',value: 'remarks'}, 
        ],
        pagination:{
             rowsPerPage: -1 // -1 for All",
        },
    }),
    props:{
        tableAvailmentData : {
            type: Object,
            default : {}
        },
    },
    watch: {
        'tableAvailmentData.selected' : {
            handler :function (newValue,oldValue)
            {             
                if(newValue.length !== oldValue.length)
                {
                   this.selected = newValue 
                }
            },
            deep: true
        },
    },
    methods : {
        getBalance(grand_total,payable_amount)
        {
            if(!payable_amount)
            {
                payable_amount = 0;
            }

            let balance = grand_total - payable_amount;

            return balance
        },
        customFilter(items, search, filter) {
            search = search.toString().toLowerCase()
            // return items.filter(row => filter(row["member"]["fullname"], search)); // if full name
            return items.filter(row => filter(row["approval_id"], search));
        },
        adjustAvailment(itemID){
            let routeData = this.$router.resolve('/availment-center/edit?id='+itemID+"&is_adjust=1");
            window.open(routeData.href, '_blank');
        }
    }
}
</script>

<style lang="scss">
    .payable-availment{
        margin-top: 25px;

        .payable-availment-table{
            margin-bottom: 25px;
            
            .table-search-field{
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                justify-content: center;
                align-self: center;
            }
        }
    }
</style>

