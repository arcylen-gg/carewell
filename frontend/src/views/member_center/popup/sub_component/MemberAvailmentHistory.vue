<template>
    <span>
        <div class="availment-history__menu">
            <v-select 
                color="accent" 
                :items="select_company" 
                item-text="name" 
                item-value="id" 
                label="Filter by Company"
                outline
                v-model="filters.company_id"
            />
            <v-select color="accent" 
                :items="select_benefit" 
                item-text="name" 
                item-value="id" 
                label="Filter by Type of Benefits"
                outline
                v-model="filters.benefit_id"
            />
            <v-menu
                v-model="datepicker_from"
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
                v-model="date_avail"
                label="Date"
                append-icon="event"
                outline
                readonly
                ></date-format>
                <v-date-picker v-model="avail_date" @input="datepicker_from = false" :max="new Date().toISOString().substr(0, 10)" min="1950-01-01"></v-date-picker>
            </v-menu>
            <v-btn color="accent" depressed @click="filterData">RUN REPORT</v-btn>
            <span></span>
            <span></span>
            <span></span>
            <v-btn depressed color="success" @click="exportAvailment">
                <v-icon class="mr-3">mdi-file-export</v-icon> Export Excel
            </v-btn>
        </div>
        <div class="availment-table">
            <v-data-table :headers="availment_headers" :items="itemData" class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left">{{ props.item.approval_id }}</td>
                    <td class="text-xs-left">{{ props.item.date_avail | mixin_change_date_format }}</td>
                    <td class="text-xs-left">{{ props.item.benefit_type.name || '' }}</td>
                    <td class="text-xs-left">{{ props.item.diagnosis ?  props.item.diagnosis.name : ''}}</td>
                    <td class="text-xs-left">{{ props.item.final_diagnosis }}</td>
                    <td class="text-xs-left">{{ props.item.grand_total }}</td>
                    <td class="text-xs-left">{{ parseFloat(props.item.procedures_patient_charged_total) + parseFloat(props.item.doctors_patient_charged_total) | mixin_change_currency_format }}</td>
                    <td class="text-xs-left">{{ parseFloat(props.item.procedures_carewell_charged_total) + parseFloat(props.item.doctors_carewell_charged_total) | mixin_change_currency_format }}</td>
                    <td class="text-xs-left">{{ props.item.provider.name || '' }}</td>
                </template>
            </v-data-table>
        </div>
    </span>
</template>

<script>
export default {
    data : () => ({
        availment_headers: [
            {text: 'APPROVAL NO.',value: 'approval_no'},
            {text: 'APPROVAL DATE',value: 'approval_date'},
            {text: 'TYPE OF BENEFITS',value: 'type_benfits'},
            {text: 'DIAGNOSIS',value: 'diagnosis'},
            {text: 'FINAL DIAGNOSIS',value: 'final_diagnosis'},
            {text: 'TOTAL AMOUNT',value: 'total_amount'},
            {text: 'CHARGED TO PATIENT',value: 'charge_patient'},
            {text: 'CHARGED TO CAREWELL',value: 'charge_carewell'},
            {text: 'PROVIDER',value: 'provider'},
        ],
        filters:{},
        itemData:[],
        datepicker_from: false,
        avail_date: '',
    }),
    computed: {
        date_avail : {
            get :function(){
                return this.avail_date.substr(0,4);
            },
            set :function(newValue){
                return this.avail_date = newValue;
            }
        }
    },
    props: {
        item : {
            type: Array,
            default : () => []
        },
        select_company : {
            type: Array,
            default : () => {}
        },
        select_benefit : {
            type: Array,
            default : () => {}
        },
        memberData :{
            type: Object,
            default: {}
        }
    },
    methods: {
        async filterData(){
            let filteredCompany = await this.filters.company_id !== 'all' ? this.item.filter((item) => item.company_id == this.filters.company_id) : this.item ;
            let filteredBenefit = await this.filters.benefit_id !== 'all' ? filteredCompany.filter((item)=>item.benefit_type_id == this.filters.benefit_id) : filteredCompany;
            let filteredDate = await this.date_avail ? filteredBenefit.filter((item)=>item.date_avail.substr(0,4) === this.date_avail) : filteredBenefit;
            this.itemData =  filteredDate; //all filteration
        },
        exportAvailment(){
            let filters = {
                member_id : this.memberData.id,
                company_id : this.filters.company_id,
                benefit_type_id : this.filters.benefit_id,
                date_avail : this.date_avail || ''
            }
            window.open(this.$axios.defaults.baseURL+"/api/export/member/availment_history"+this.generateFilterURL(filters),"_blank");
            this.date_avail = '';
        },
    },
    mounted(){
        this.itemData = this.item;
        this.filters = {
            company_id : 'all',
            benefit_id : 'all',
        };
        this.date_avail = new Date().toISOString().substr(0, 10).substr(0,4);
    }
}
</script>
