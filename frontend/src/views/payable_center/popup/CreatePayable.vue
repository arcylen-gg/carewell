<template>
    <div class="create-payable__plan">
       <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
            <div class="three-column">
                <span></span>
                <span></span>
                <span></span>
                <v-btn color="tertiary" depressed dark @click="closeDialog">CANCEL</v-btn>
                <v-btn color="accent" depressed type="submit" ref="submit_button" :loading="loading">SAVE</v-btn>
            </div>
            <div class="holders">
                <div class="holder-two-column">
                    <!-- <global-select-box
                        v-model="filters.provider_id"
                        :default="filters.provider_id"
                        :filters="{is_archived:0}"
                        :rules="requiredRules"
                        api-link="api/provider"
                        label="Select Provider"
                        color="accent" 
                        item-text="name"
                        item-value="id"
                        is-load
                        solo
                        flat
                        @input="getEmitData($event)"
                    >
                    </global-select-box> -->
                    <global-select-box
                        v-model="filters.provider_id"
                        :default="filters.provider_id"
                        :filters="{is_archived:0}"
                        :rules="requiredRules"
                        api-link="api/select/payable/provider_create"
                        label="Select Provider"
                        color="accent" 
                        item-text="name"
                        item-value="id"
                        is-load
                        solo
                        flat
                        @input="getEmitData($event)"
                    >
                    </global-select-box>
                    <v-text-field v-model="form.soa_number" placeholder="SOA NUMBER" class="c-input" :rules="requiredRules" solo flat></v-text-field>
                </div>
                <div class="holder-three-column">
                    <div v-if="!locations">
                        <global-select-box
                            ref="payment_term"
                            v-model="form.payment_term_id"
                            :filters="{is_archived:0}"
                            :rules="requiredRules"
                            :default="form.payment_term_id"
                            api-link="api/payment-term"
                            label="Select Payment Term"
                            color="accent" 
                            item-text="name"
                            item-value="id"
                            is-load
                            outline
                            return-object
                            @input="addToDueDate($event)"
                        ></global-select-box>
                    </div>
                    <div v-else>
                        <global-select-box
                            v-model="form.payment_term"
                            :filters="{is_archived:0}"
                            :rules="requiredRules"
                            api-link="api/payment-term"
                            label="Select Payment Term"
                            color="accent" 
                            item-text="name"
                            item-value="id"
                            is-load
                            outline
                            return-object
                            @input="addToDueDate($event)"
                        ></global-select-box>
                    </div>
                    <v-menu
                        ref="received_date_menu"
                        v-model="date_received"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        lazy
                        transition="scale-transition"
                        offset-y
                        full-width
                        min-width="290px"
                        :disabled="received_disable"
                    >
                        <date-format
                            slot="activator"
                            v-model="form.received_date"
                            :default="form.received_date"
                            label="Received Date"
                            append-icon="event"
                            outline
                            readonly
                            :rules="requiredRules"
                        ></date-format>
                        <v-date-picker 
                            v-model="form.received_date" 
                            @input="date_received = false" 
                            @change="date_received_save"
                            min="1950-01-01"
                        ></v-date-picker>
                    </v-menu>

                    <v-menu
                        v-model="date_due"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        lazy
                        transition="scale-transition"
                        offset-y
                        full-width
                        min-width="290px"
                        disabled
                    >
                        <date-format
                            slot="activator"
                            v-model="form.due_date"
                            :default="form.due_date"
                            label="Due Date"
                            append-icon="event"
                            outline
                            readonly
                            :rules="requiredRules"
                        ></date-format>
                        <v-date-picker v-model="form.due_date" @input="date_due = false" min="1950-01-01"></v-date-picker>
                    </v-menu>
                </div>
            </div>
        <payable-availment-table :tableAvailmentData="{item: item, tableLoading: tableLoading, selected:selected}" ref="availmentTable"></payable-availment-table>
        </v-form>
    </div>
</template>
<script>
import PayableAvailmentTable from './table/PayableAvailmentTable'
import GlobalMixins from '../../../otherMixins/GlobalMixins';

export default {
    components : {
        PayableAvailmentTable
    },
    mixins :[GlobalMixins],
    data : ()  => ({
        form : {},
        filters : {},
        date_received: false,
        date_due : false,
        date_prepared: false,
        loading: false,
        received_disable : true,
        requiredRules: [
            v => !!v || 'Input is required',
        ],
        breadcrumbs: [
            { text: 'Dashboard', disabled: false }, 
            { text: 'Payable Center', disabled: false }, 
            { text: 'Create Payable', disabled: true } 
        ],
        select_providers : [],
        select_paymentTerms : [],
        item : [],
        tableLoading: false,
        selected: [],
        received_date_menu: [],
        locations: true,
    }),
    props : {
        eventName: {
          type: String,
          default: 'createPayable'
        }
    },
    watch : {

    },
    methods : {
        addToDueDate(event){
            this.received_disable = false;
            let due_date = this.form.received_date;
            let newDueDate = new Date(due_date.substr(0,4), due_date.substr(5,2) ,due_date.substr(7,2));
            let date = newDueDate;
            let addDay = +due_date.substr(8,2) + event.number + 1;
            let newDate = new Date(date.getFullYear(),date.getMonth(),addDay).toISOString().substr(0,10);
            this.form.due_date = newDate;
        },
        date_received_save(date) {
            if(this.locations)
            {
                this.$refs.received_date_menu.save(date)
                this.addToDueDate(this.form.payment_term);
            }
            else
            {
                this.$refs.received_date_menu.save(date)
                let data = this.$refs.payment_term.selectOptions.find(item => item.id == this.form.payment_term_id.id)
                this.addToDueDate(data);
            }
        },
        async getEmitData(data){
            console.log({data})
            this.filters.provider_id = data;

            let filters = {
                provider_id : data,
                status : 'new'
            };

            this.tableLoading = await true;

            let provider_availment = await axios.get(`api/select/payable/provider/${data}/availment`+this.generateFilterURL(filters))

            const {data : availment} = provider_availment;

            this.item = availment;

            this.tableLoading =  await false;

        },
        checkSelectedData(selected){
            let chk_for_negative_balance = selected.every(function(item){
                let balance = item.grand_total - parseFloat(item.payable_amount)
                return balance >= 0;
            });

            return  chk_for_negative_balance;
        },
        save(){
            if(this.$refs.availmentTable.selected.length > 0)
            {
                let check_selected = this.checkSelectedData(this.$refs.availmentTable.selected);

                if(check_selected)
                {
                    if(this.$refs.form_submit.validate())
                    {
                        this.loading = true

                        this.form.selected = this.$refs.availmentTable.selected;
                        this.form.provider_id = this.filters.provider_id
                        this.form.user_id = this.$store.state.user.id
                        if(this.locations)
                        {
                            this.form.payment_term_id = this.form.payment_term.id;
                        }
                        else
                        {
                            this.form.payment_term_id = this.form.payment_term_id.id || this.form.payment_term_id
                        }

                        let id = this.$route.query.id;

                        if(id)
                        {
                            let savingURL = {method:"put",url: "/api/payable/"+id};
                            this.saving(savingURL);
                        }
                        else
                        {
                            this.saving({});
                        }
                    }
                }
                else
                {
                    this.toaster("Check for negative balance.",400);
                }
            }
            else
            {
                this.toaster("Select at least one availment.",400);
            }
          
        },
        saving({method="post", url="/api/payable"}){
            axios[method](url,this.form)
                .then(response => {
                    this.toaster(response.data,response.status);
                    EventBus.$emit(this.eventName, response);
                    this.$router.push('/payable-center');
                })
                .catch(error => {
                    this.toaster(error.response.data, error.response.status)
                })
                .finally(() => {
                    this.loading = false
                })
        },
        close() {
            this.clear()
            EventBus.$emit(this.eventName)
            this.dialog = false
        },
        closeDialog() {
            this.$router.push('/payable-center')
        },
        getPayable(id){
            let filters = {
                showAll : 1,
            }

            return axios.get('api/payable/'+id+this.generateFilterURL(filters))
        },
        getAvailment(provider_id){
            let filters = {
                status : 'new',
                showAll : 1,
                provider_id 
            }
            return axios.get(`api/select/payable/provider/${filters.provider_id}/availment`+this.generateFilterURL(filters))
            // return axios.get('api/availment'+this.generateFilterURL(filters))

        },
        async loadPayable(){
            let payable_id = this.$route.query.id;
            if(payable_id)
            {
                let payableAPI = await this.getPayable(payable_id);

                let availmentAPI = await this.getAvailment(payableAPI.data.provider_id);

                let payable_availment = await [...payableAPI.data.payable_availment].map(item =>item.availment);

                let get_amount_remarks = await [...payableAPI.data.payable_availment].map(item =>({
                    remarks:item.remarks,
                    payable_amount: item.payable_amount
                }));

                //insert data from get_amount_remarks to payable_availment
                await [...payable_availment].map((item,key)=>{
                    item.remarks = get_amount_remarks[key].remarks
                    item.payable_amount = get_amount_remarks[key].payable_amount
                });
       
                this.selected = payable_availment
       
                this.filters.provider_id = payableAPI.data.provider_id

                this.form = {
                    soa_number : payableAPI.data.soa_number,
                    received_date : payableAPI.data.received_date, 
                    due_date : payableAPI.data.due_date,
                    payment_term_id : payableAPI.data.payment_term_id,
                }

                let merge_availment = [...payable_availment,...availmentAPI.data];

                this.item = GlobalMixins.methods._removeDuplicates(merge_availment,'id');
            }
        },
        changeBreadcrumbsText(id){
            if(id)
            {
                let breadcrumbs_key = this.breadcrumbs.length-1;
                this.breadcrumbs.splice(breadcrumbs_key,1);
                this.breadcrumbs = [...this.breadcrumbs,{text:'Edit Payable',disabled: true}]
            }
        }
    },
    mounted(){
        this.changeBreadcrumbsText(this.$route.query.id);
        // this.setDate();
        this.loadPayable();
        this.form.received_date = new Date().toISOString().substr(0,10);
        this.locations = window.location.pathname == '/payable-center/create'
    }
}
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .create-payable__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }
        .three-column {
            border: 1px solid #919191;
            display: grid;
            background-color: #fff;
            grid-template-columns: repeat(5, 1fr);
            margin-bottom: 10px;
        }
        .holders{
            border: 1px solid #919191;
            background-color: #fff;
            div {
                padding: 0px;
            }
            .holder-two-column {
                margin-top: 20px;
                margin-left: 5px;
                margin-right: 5px;
                display: grid;
                background-color: #fff;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
                .v-text-field {
                    font-size: 15px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                    }
                }
            }

            .holder-three-column {
                margin-left: 5px;
                margin-right: 5px;
                display: grid;
                background-color: #fff;
                grid-template-columns: repeat(3, 1fr);
                grid-column-gap: 50px;
            }
        }
    }


</style>