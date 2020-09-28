<template>
    <div class="mark-collection__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
        <div class="top-holder">
            <div class="pull-right">
                <v-btn color="accent" depressed type="submit" ref="submit_button" class="mb-2 pull-right float-right">Close</v-btn>
            </div>
            <div class="top-holder-two">
                <v-label>Billing Details</v-label>
                <span></span>
                 <v-menu 
                    ref="start_info_menu" 
                    :close-on-content-click="false" 
                    v-model="start_menu" 
                    :nudge-right="40" 
                    lazy transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px"
                 >
                    <date-format 
                        :rules="requiredRules" 
                        slot="activator" 
                        v-model="form.collection_date" 
                        label="Collection Date"
                        append-icon="event" 
                        readonly 
                        outline
                    />
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="form.collection_date"
                        min="1950-01-01" 
                        @change="start_info_save"
                    />
                </v-menu>
                <v-text-field type='number' :rules="requiredRules" v-model="form.or_number" color="accent" label="OR No" outline></v-text-field>  
                <v-text-field type='number' :rules="requiredRules" v-model="form.check_no" color="accent" label="Cheque No" outline></v-text-field> 
                <v-menu 
                    ref="check_info_menu" 
                    :close-on-content-click="false" 
                    v-model="check_menu" 
                    :nudge-right="40" 
                    lazy 
                    transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px"
                >
                    <date-format 
                        :rules="requiredRules" 
                        slot="activator" 
                        v-model="form.check_date" 
                        label="Cheque Date"
                        append-icon="event" 
                        readonly 
                        outline
                    />
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="form.check_date"
                        min="1950-01-01" 
                        @change="check_info_save"
                        />
                </v-menu> 
                <global-select-box
                    v-model="form.bank_id"
                    :rules="requiredRules"
                    :default="form.bank_id"
                    api-link="/api/bank"
                    label="Select Bank"
                    item-text="name"
                    item-value="id"
                    outline
                    is-load
                >
                </global-select-box>
                <currency-format 
                    v-model="form.amount" 
                    label="Amount"
                    color="accent"
                    :rules="requiredRules"
                    outline 
                    reverse 
                >
                </currency-format>
                <v-textarea :rules="requiredRules" v-model="form.remarks" color="accent" label="Remarks" outline></v-textarea>
                <div class="my-0 py-0" style="text-align:center !important;">
                    <div class="my-0 py-0" style="text-align:center !important;">
                        <v-flex xs12 class="import-drag-drop-one">
                            <div v-for="(file_item, key) in files_attached" :key="'files-'+key">
                                <a v-bind:href="file_item.source" download>{{file_item.name}}</a>
                                <!-- <span> {{file_item.name}} {{file_item.id}} {{key}}</span> -->
                            </div>
                        </v-flex>
                    </div>
                </div>
                <v-label>&nbsp;</v-label>
                <v-label>&nbsp;</v-label>
                <v-label>Collection Active List Details</v-label>
                <v-label>&nbsp;</v-label>
                <global-select-box
                    ref="company"
                    readonly 
                    api-link="api/company"
                    v-model="form.company_id" 
                    :filters="{is_archived:0}"
                    :default="form.company_id"
                    is-load
                    color="accent" 
                    item-value="id" 
                    item-text="name" 
                    label="Select Company" 
                    outline
                >
                </global-select-box>

                <global-select-box
                    readonly 
                    api-link="api/payment_mode"
                    v-model='form.payment_mode_id' 
                    :filters="{is_archived:0}"
                    :default="form.payment_mode_id"
                    is-load
                    color="accent" 
                    item-value="id" 
                    item-text="name" 
                    label="Select Payment Method" 
                    outline
                ></global-select-box>

                <v-text-field readonly :rules="requiredRules" v-model="form.member_number" color="accent" label="Number of Member" outline></v-text-field>
                <currency-format
                    reverse 
                    :rules="requiredRules" 
                    v-model="form.total_amount_paid" 
                    color="accent" 
                    label="Amount Billed" 
                    outline
                >
                </currency-format>
            </div> 
        </div>
            <!-- <cal-member-table :cal_members="import_item"></cal-member-table> -->
            <template>
              <v-data-table
                :headers="headers"
                :items="import_item"
                class="elevation-1"
                align='center'
              >
                <template slot="headerCell" slot-scope="props">
                  <v-tooltip bottom>
                    <span slot="activator">
                      {{ props.header.text }}
                    </span>
                    <span>
                      {{ props.header.text }}
                    </span>
                  </v-tooltip>
                </template>
                <template slot="items" slot-scope="props">
                    <td class="text-xs-center">{{ props.item.member.company.universal_id  }}</td>
                    <td class="text-xs-center">{{ props.item.member.company.carewell_id }}</td>
                    <td class="text-xs-center">{{ props.item.member.first_name }}</td>
                    <td class="text-xs-center">{{ props.item.member.middle_name }}</td>
                    <td class="text-xs-center">{{ props.item.member.last_name }}</td>
                    <td class="text-xs-center">{{ props.item.member.birth_date | mixin_change_date_format }}</td>
                    <td class="text-xs-center">{{ props.item.member.company.company_deployment.name }}</td>
                    <td class="text-xs-center">{{ props.item.monthly_premium }}</td>
                    <td class="text-xs-center"><paid-count-dialog ref="paidCountDialog" :item="import_item" :paid_count="props.item.period_count" :member_id="props.item.member_id" :locations="false">{{ props.item.period_count }}</paid-count-dialog></td>
                    <td class="text-xs-center">{{ props.item.paid_amount }}</td>
                    <td class="text-xs-center">{{ props.item.member.is_archived == 0 ? 'active' : 'inactive' }}</td>
                </template>
              </v-data-table>
            </template> 
             
        </v-form>  
    </div>
</template>
<script>
import CalMemberTable from '../components/CalMemberTable';
import paidCountDialog from '../page/popup/PaidCount'

    export default {
        components : {
            CalMemberTable,
            paidCountDialog
        },
        data: () => ({
            form : {
            },
            loading_value : 10,
            breadcrumbs: [
                { text: 'Dashboard', disabled: false }, 
                { text: 'Billing Center', disabled: false }, 
                { text: 'Mark as Close CAL', disabled: true } 
            ],
            items : [],
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
            start_menu:false,
            start_info_menu: [],
            check_menu:false,
            check_info_menu: [],
            import_item: [],
            tab_items: [
                {TabTitle: 'Active',TabIcon: 'mdi-account-check' , value:'active',},
                {TabTitle: 'Inactive',TabIcon: 'mdi-menu' , value:'inactive',},
            ],
            active_tab: 'tab-0',
            // items: {},
            payment_mode:[],
            company:[],
            filters:{},
            cal_items:[],
            test:[],
            requiredRules: [
                    v => !!v || 'Input is required',
                ],
            files:[],
            files_attached:[],
            files_deleted:[],

        }),
        methods : {
            setData(data){
                
                // this.form = data;
                this.form.collection_date = data.cal_status.collection_date;
                this.form.or_number = data.cal_status.or_number;
                this.form.check_no = data.cal_status.check_number;
                this.form.check_date = data.cal_status.check_date;
                this.form.amount = data.cal_status.amount;
                this.form.remarks = data.cal_status.pending_remarks;
                this.form.bank_id = data.cal_status.bank_id;

                this.form.company_id = data.company_id;
                this.form.payment_mode_id = data.payment_mode_id;
                this.form.total_amount_paid = data.total_amount_paid;
                this.form.member_number = data.cal_member_count;

                this.getMemberData(data.cal_member);
            },
            getMemberData(data){
                let arrayData = [];
                let memberRecord = data;
                memberRecord.forEach((data,ind) => {
                    let dividend = Math.trunc(data.paid_amount / data.monthly_premium);
                    
                    arrayData.push({
                        id: data.id,
                        cal_id : data.cal_id,
                        member_id : data.member_id,
                        member : data.member,
                        monthly_premium : data.monthly_premium,
                        period_count : dividend,
                        paid_amount : data.paid_amount
                    });
                });
                
                this.import_item = arrayData;
            },
            async showData2(){
                this.loading = true;

                let calDataAPI = await axios.get(`/api/show/cal/${this.$route.query.id}`);

                const {data: calData} = calDataAPI;

                await this.setData(calData);

                this.files_attached = calData.files

                this.loading = await false;
            },
            start_info_save(date) {
                this.$refs.start_info_menu.save(date)
            },
            check_info_save(date) {
                this.$refs.check_info_menu.save(date)
            },
            showData() {
                this.loading = true
                //this.clearData()
                axios.get(`/api/cal/${this.$route.query.id}`)
                .then(response=> {
                    this.items = response.data
                    this.items.total_amount_paid = this.currency_format('₱',parseFloat(this.items.total_amount_paid)); 
                    this.getPaymentMode(this.items.payment_mode_id);
                    this.getCompany(this.items.company_id);
                    this.getData();
                    this.loading = false
                    this.files_attached = this.items.files
                })
                .catch(error => {
                    console.log(error)
                })
            },
            getData() {
                this.filters.id = this.$route.query.id
                axios.get('api/cal'+this.generateFilterURL(this.filters))
                .then(response => {
                    var a = 0
                    this.items.member_number = response.data.data.data[0].company.member_company.length   
                    this.items.import_item = response.data.data.data[0].cal_member;
                    this.items.collection_date = response.data.data.data[0].cal_status.collection_date;
                    this.items.or_number = response.data.data.data[0].cal_status.or_number
                    this.items.check_no = response.data.data.data[0].cal_status.check_number
                    this.items.check_date = response.data.data.data[0].cal_status.check_date
                    this.items.bank_name = response.data.data.data[0].cal_status.bank_name
                    this.items.amount = this.currency_format('₱',parseFloat(response.data.data.data[0].cal_status.amount))
                    this.items.remarks = response.data.data.data[0].cal_status.pending_remarks
                    this.items.import_item.forEach(function(item){
                        a += item.paid_amount
                    })
                    this.import_item = this.items.import_item
                })
                .catch(error => {

                })
                .finally(() => {

                })
               
            },
            getAxios(){
                this.filters.is_archived = 0
                this.getPaymentMode(this.filters)
                this.getCompany(this.filters)
            },
            getPaymentMode(filters) {
                axios.get('api/payment_mode'+this.generateFilterURL(filters))
                .then(response => {
                    this.payment_mode = response.data
                })
                .catch(error => {
                })
                .finally(() => {
                })
            },
             getCompany(filters) {
                axios.get('api/company'+this.generateFilterURL(filters))
                .then(response => {
                    this.company = response.data.data.data
                })
                .catch(error => {
                })
                .finally(() => {
                })
            },
            save(){
                this.redirectIfNotExisting()
            },
            amount(amount, textfield){
                if(textfield == 'amount'){
                    this.items.amount = this.currency_format('₱',parseFloat(amount));
                 }else if(textfield == 'paid_amount'){
                    this.items.paid_amount = this.currency_format('₱',parseFloat(amount));
                 }
            },
            redirectIfNotExisting()
            {
                this.$router.push('/billing-&-collection-center')
            }
        },
        created(){
            EventBus.$on('setUploadData',(data)=>{
                this.files = data;
            });
        },
        beforeDestroy () 
        {
            clearInterval(this.interval)
        },
        mounted() 
        {
            // this.showData();
            this.showData2();
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .mark-collection__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }


        .top-holder
        {
            border: 1px solid #ededed;
            padding: 15px;
            display: grid;
            background-color: #ffffff;
            grid-template-columns: repeat(1, 1fr);
            grid-column-gap: 50px;
            margin-bottom: 10px;
            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .top-holder-two {
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
            }

        }
        .import-drag-drop-one
        {
            border: dashed 3px #222C3C;
            width: 100%;
            padding : 30px !important;
            text-align : center;
            font-weight : bold;
            float : left;
            cursor : pointer;
            position : relative;
            margin-bottom: 10px;
        }

        .bottom-holder {
            padding: 15px;
            border: 1px solid #ededed;

            .benefits-holder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
                grid-row-gap: 15px;
            }
        }
    }

    
</style>