<template>
    <div class="mark-collection__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
        <div class="top-holder">
            <div class="pull-right">
                <v-btn color="accent" depressed type="submit" ref="submit_button" class="mb-2 pull-right float-right">Save & Close</v-btn>
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
                        v-model="items.collection_date" 
                        label="Collection Date"
                        append-icon="event" 
                        readonly 
                        outline
                    />
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.collection_date"
                        min="1950-01-01" 
                        @change="start_info_save"
                    />
                </v-menu>
                <v-text-field type='number' :rules="requiredRules" v-model="items.or_number" color="accent" label="OR No" outline></v-text-field>  
                <v-text-field type='number' :rules="requiredRules" v-model="items.check_no" color="accent" label="Cheque No" outline></v-text-field> 
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
                        v-model="items.check_date" 
                        label="Cheque Date"
                        append-icon="event" 
                        readonly 
                        outline
                    />
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.check_date"
                        min="1950-01-01" 
                        @change="check_info_save"
                        />
                </v-menu> 
                <global-select-box
                    v-model="items.bank_id"
                    :rules="requiredRules"
                    api-link="/api/bank"
                    label="Select Bank"
                    item-text="name"
                    item-value="id"
                    outline
                    is-load
                >
                </global-select-box>
                <currency-format 
                    v-model="items.amount" 
                    label="Amount"
                    color="accent"
                    :rules="requiredRules"
                    outline 
                    reverse 
                >
                </currency-format>
                <v-textarea :rules="requiredRules" v-model="items.remarks" color="accent" label="Remarks" outline></v-textarea>
                <div class="my-0 py-0" style="text-align:center !important;">
                    <v-flex xs12 class="import-drag-drop-one" :rules="requiredRules" >
                        <file-uploader></file-uploader>
                    </v-flex>
                </div>
                <v-label>&nbsp;</v-label>
                <v-label>&nbsp;</v-label>
                <v-label>Collection Active List Details</v-label>
                <v-label>&nbsp;</v-label>
                <global-select-box
                    ref="company"
                    readonly 
                    api-link="api/company"
                    v-model="items.company_id" 
                    :filters="{is_archived:0}"
                    :default="items.company_id"
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
                    v-model='items.payment_mode_id' 
                    :filters="{is_archived:0}"
                    :default="items.payment_mode_id"
                    is-load
                    color="accent" 
                    item-value="id" 
                    item-text="name" 
                    label="Select Payment Method" 
                    outline
                ></global-select-box>

                <v-text-field readonly :rules="requiredRules" v-model="items.member_number" color="accent" label="Number of Member" outline></v-text-field>
                <currency-format
                    reverse 
                    :rules="requiredRules" 
                    v-model="items.total_amount_paid" 
                    color="accent" 
                    label="Amount Billed" 
                    outline
                >
                </currency-format>
            </div> 
        </div>
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
                    <td class="text-xs-center">{{ props.item.monthly_premium | mixin_change_currency_format}}</td>
                    <td class="text-xs-center"><paid-count-dialog ref="paidCountDialog" :item="import_item" :paid_count="props.item.period_count" :member_id="props.item.member_id">{{ props.item.period_count }}</paid-count-dialog></td>
                    <td class="text-xs-center">{{ props.item.paid_amount | mixin_change_currency_format}}</td>
                    <td class="text-xs-center">{{ props.item.member.is_archived == 0 ? 'active' : 'inactive' }}</td>
                </template>
              </v-data-table>
            </template> 
             
        </v-form>  
    </div>
</template>

<script>
    import paidCountDialog from '../page/popup/PaidCount'
    export default {
        components:{
            paidCountDialog,
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
            // items : [{
            //     member_number: '',
            // }],
            items:{},
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
            filters:{},
            cal_items:[],
            test:[],
            requiredRules: [
                    v => !!v || 'Input is required',
                ],
            files:[]

        }),
        methods : {
            start_info_save(date) {
                this.$refs.start_info_menu.save(date)
            },
            check_info_save(date) {
                this.$refs.check_info_menu.save(date)
            },
            async showData(){
                let responseApi = await axios.get(`/api/cal/${this.$route.query.id}`);
                const {data: responseData} = await responseApi;
                this.items.company_id = responseData.company_id;
                this.items.payment_mode_id = responseData.payment_mode_id;
                this.items.payment_term_id = responseData.payment_term_id;
                this.items.member_number = responseData.cal_member.length;
                this.items.total_amount_paid = responseData.cal_member.reduce((acc,item) => acc += item.paid_amount, 0);
                // this.import_item = responseData.cal_member;
                this.getMemberData(responseData.cal_member);
            },
            getMemberData(member_data){
                let arrayData = [];
                let memberRecord = member_data;
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
            save(){
                if(this.$refs.form_submit.validate())
                {
                    this.items.cal_members = this.import_item
                    if(this.items.cal_members.length == 0)
                    {
                        this.toaster("You cannot proceed when there is no cal member(s). Please import cal member first.", 500)
                    }
                    else
                    {
                        this.items.id = this.$route.query.id;
                        this.items.company =  this.$refs.company.selectOptions.find(item=> item.id == this.items.company_id);
                        
                        const formData = new FormData();
                    
                        formData.append('folder', 'App\\Models\\CalStatus')
                        formData.append('items',JSON.stringify(this.items))
                    
                        if(this.files.length == 0)
                        {
                            this.toaster('Please select atleast one file.', 300)
                        }
                        else
                        {
                            for (var i = 0; i < this.files.length; i++)
                            {
                                formData.append('files[]', this.files[i], this.files[i].name);
                                formData.append('extensions[]', this.files[i].extension);
                                formData.append('sizes[]', this.files[i].size);
                                formData.append('names[]', this.files[i].name);
                            }

                            axios.post('/api/cal_status', formData)
                            .then(response =>{
                                // this.$refs.form_submit.reset()
                                this.toaster(response.data, response.status)
                                this.redirectIfNotExisting()
                            })
                            .catch(errors =>{
                                this.toaster(errors.response.data, errors.response.status)
                            })
                            .finally(()=>{

                            });
                        }
                    }
                }
            },
            // amount(amount, textfield){
            //     if(textfield == 'amount'){
            //         this.items[textfield] = this.currency_format('₱',parseFloat(amount));
            //      }else if(textfield == 'paid_amount'){
            //         this.items.total_paid_amount = this.currency_format('₱',parseFloat(amount));
            //      }
            // },
            redirectIfNotExisting()
            {
                this.$router.push('/billing-&-collection-center')
            },
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
        mounted () 
        {
            this.showData();
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