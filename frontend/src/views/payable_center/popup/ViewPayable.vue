<template>
     <div class="create-payable__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="holders">
            <div class="holder-two-column">
                <div class="holder-two-column" style="border: 1px solid #919191;">
                    <span>Provider: </span> 
                    <strong>{{form.provider ? form.provider.name : ''}}</strong>
                </div>
                <div class="holder-two-column" style="border: 1px solid #919191;">
                    <span>SOA Number: </span> 
                    <strong>{{ form.soa_number }}</strong>
                </div>
            </div>
            <div class="holder-three-column">
                <div class="holder-two-column">
                    <span>Payment Term: </span>  
                    <strong>{{form.payment_term ? form.payment_term.name : ''}}</strong>
                </div>
                <div class="holder-two-column">
                    <span>Received Date: </span>  
                    <strong>{{ form.received_date | mixin_change_date_format}}</strong>
                </div>
                <div class="holder-two-column">
                    <span>Due Date: </span>  
                    <strong>{{ form.due_date | mixin_change_date_format }}</strong>
                </div>
            </div>
        </div>
        <h4>APPROVAL LIST</h4>
         <v-data-table 
            :headers="headers" 
            item-key="id"
            :items="form.payable_availment" 
            :pagination.sync="pagination"
            class="elevation-1"
        >
            <template slot="items" slot-scope="props">
                <td>{{props.item.availment.approval_id}}</td>
                <td>{{props.item.availment.benefit_type.name}}</td>
                <td nowrap>
                    <span v-for="(doctors, index ) in props.item.availment.availment_doctor" :key="index">
                        {{doctors.doctor.name}}
                    </span>
                </td>
                <td>
                    <span v-for="(procedures, index ) in props.item.availment.availment_procedure" :key="index">
                        {{procedures.procedure.name}}
                    </span>
                </td>
                <td>{{props.item.availment.diagnosis ?  props.item.availment.diagnosis.name : ""}}</td>
                <td>{{props.item.availment.chief_complaint}}</td>
                <td nowrap>{{props.item.availment.member.company.universal_id}}</td>
                <td nowrap>{{props.item.availment.member.company.carewell_id}}</td>
                <td nowrap>{{props.item.availment.member.fullname}}</td>
                <td nowrap>{{ props.item.availment.member.birth_date | mixin_change_date_format }}</td>
                <td nowrap>{{props.item.availment.member.age}}</td>
                <td nowrap>{{props.item.availment.member.gender}}</td>
                <td nowrap>{{props.item.availment.member.company.company.name}}</td>
                <td nowrap>{{props.item.availment.provider.name}}</td>
                <td>{{ props.item.availment.date_avail | mixin_change_date_format}}</td>
                <td nowrap>{{ currency_format('₱',props.item.availment.provider_payee_fee) }}</td>
                <td nowrap>{{ currency_format('₱',props.item.availment.doctor_fee) }}</td>
                <td nowrap>{{ currency_format('₱',props.item.availment.grand_total)}}</td>
                <td nowrap>{{ currency_format('₱',props.item.payable_amount)}}</td>
                <td nowrap>{{ currency_format('₱',getBalance(props.item.availment.grand_total, props.item.payable_amount) ) }}</td>
                <td>{{props.item.remarks}}</td>
                <td>{{props.item.availment.date_avail | mixin_change_date_format}}</td>
                <td>{{ change_date_format(props.item.availment.date_avail) }}</td>
                <td>{{ currency_format('₱',props.item.availment.grand_total)}}</td>
            </template>
        </v-data-table>
     </div>
</template>
<script>
import ShowForEachData from './sub_component/ShowForEachData';

export default {
    components:{
        ShowForEachData
    },
    data: () => ({
        breadcrumbs: [
            { text: 'Dashboard', disabled: false }, 
            { text: 'Payable Center', disabled: false }, 
            { text: 'View Payable', disabled: true } 
        ],
        form:{},
        headers: [
            // {text: 'APPROVAL NUMBER',value: 'approval_id'}, 
            // {text: 'BENEFIT TYPE',value: 'benefit_type'},  
            // {text: 'DOCTORS',value: 'doctor'},
            // {text: 'PROCEDURES',value: 'procedure'},
            // {text: 'DIAGNOSIS',value: 'diagnosis'},
            // {text: 'UNIVERSAL ID',value: 'universal_id'}, 
            // {text: 'CAREWELL ID',value: 'carewell_id'}, 
            // {text: 'MEMBER NAME',value: 'full_name'}, 
            // {text: 'COMPANY',value: 'company_id'}, 
            // {text: 'PROVIDER',value: 'provider_id'}, 
            // {text: 'DATE AVAIL',value: 'date_avail'}, 
            // {text: 'AMOUNT',value: 'amount'}, 
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
    methods:{
        getBalance(grand_total,payable_amount)
        {
            if(!payable_amount)
            {
                payable_amount = 0;
            }

            let balance = grand_total - payable_amount;

            return balance
        },
        async getData(id){
            let apiPayable = await axios.get('api/payable/'+id);
            const {data: apiPayableData} = apiPayable;

            let filters={
                id:  apiPayableData.payment_term_id
            }
            let apiPaymentTerm = await axios.get('api/payment-term'+this.generateFilterURL(filters));
            const {data: { data : { data : paymentTerm } } } = apiPaymentTerm; // paymentTerm = apiPaymentTerm.data.data.data
            const paymentTermData = paymentTerm[0];
  
            this.form = apiPayableData;
            this.form.payment_term = paymentTermData;
            console.log(this.form)
        }
    },
    mounted(){
        this.getData(this.$route.query.id);
    }
}
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .create-payable__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }
        
        .holders {
            border: 1px solid #919191;
            background-color: #fff;
            div {
                padding: 0px;
            }
            .holder-two-column {
                padding: 15px;
                display: grid;
                background-color: #fff;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
            }

            .holder-three-column {
                padding: 15px;
                display: grid;
                background-color: #fff;
                grid-template-columns: repeat(3, 1fr);
                grid-column-gap: 50px;
            }
        }
    }


</style>
