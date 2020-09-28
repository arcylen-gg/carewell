<template>
    <div class="create-payable__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
            <div class="holder-two-column" v-if="!$route.query.status || $route.query.status === 'edit'">
                <span></span>
                <v-btn color="accent" depressed type="submit" ref="submit_button" :loading="loading">SAVE</v-btn>
            </div>
            <div class="holder-two-column">
                <div class="holder-two-column adjust-text">
                    Provider:
                    <div class="text-xs-center">
                        <strong>{{ form.provider ? form.provider.name : '' }}</strong>
                    </div>
                </div>
                <div class="holder-two-column adjust-text">
                    SOA Number:
                    <div class="text-xs-center">
                        <strong>{{ form.soa_number }}</strong>
                    </div>
                </div>
                <div class="holder-two-column adjust-text">
                    Received Date:
                    <div class="text-xs-center">
                        <strong>{{ form.received_date | mixin_change_date_format }}</strong>
                    </div>
                </div>
                <div class="holder-two-column adjust-text">
                    Due Date:
                    <div class="text-xs-center">
                        <strong>{{ form.due_date | mixin_change_date_format }}</strong>
                    </div>
                </div>
                <div class="holder-two-column adjust-text">
                    Preparation Date:
                    <div class="text-xs-center">
                        <strong>{{ form.created_at | mixin_change_date_format }}</strong>
                    </div>
                </div>
                <div class="holder-two-column adjust-text">
                    Preparation By:
                    <div class="text-xs-center">
                        <strong>{{ form.user ? form.user.full_name : '' }}</strong>
                    </div>
                </div>
            </div>
            <div>
                <payable-payee-table :availment_data="availment.payee" ref="provider_payee"><span slot="title"><br>HOSPITAL FEE</span></payable-payee-table>
            </div>
             <div>
                <payable-payee-table :availment_data="availment.doctor" ref="provider_doctor"><span slot="title"><br>PHYSICIAN FEE</span></payable-payee-table>
            </div>
        </v-form>
    </div>
</template>
<script>
import PayablePayeeTable from '../popup/table/PayablePayeeTable';
export default {
    components : {
        PayablePayeeTable
    },
    data : ()  => ({
        form : {},
        filters : {},
        date_received: false,
        date_due : false,
        date_prepared: false,
        loading:false,
        requiredRules: [
            v => !!v || 'Input is required',
        ],
        breadcrumbs: [
            { text: 'Dashboard', disabled: false }, 
            { text: 'Payable Center', disabled: false }, 
            { text: 'Mark as Close', disabled: true } 
        ],
        select_data : [],
        availment: {
            payee : [],
            doctor : [],
        }
    }),
    props : {
        eventName: {
          type: String,
          default: 'createPayable'
        }
    },
    methods : {
        save(){
            this.form.provider_payee = this.$refs.provider_payee.availment_data;
            this.form.provider_doctor = this.$refs.provider_doctor.availment_data;

            if(this.$refs.form_submit.validate())
            {
                this.form.saving = 'save';
                this.methodSaving();
            }
            else
            {
                this.form.saving = 'draft';
                this.methodSaving();
            }
        },
        methodSaving(){
            let savingURL = {};
            if(this.$route.query.status  === "edit")
            {
                this.form.method = 'edit';
                savingURL = {method:"put",url:"/api/payable/mark-close/"+this.form.id}
            }
            else
            {
                savingURL = {method:"post",url:"/api/payable/mark-close"}
            }
            this.saving(savingURL);
        },
        saving({method="post",url="/api/payable/mark-close"}){
            axios[method](url,this.form)
            .then(response => {
                this.toaster(response.data,response.status);
                this.$router.push('/payable-center');
            })
            .catch(error => {
                console.log(error)
            })
            
            this.$router.push('/payable-center');
        },
        getDistinctPayable({arrayAffected = null}){
            const dataFunctions = {
                getDistinctID : x => x.id,
                forEachDistinctID : (data,key) => {
                    let payees = arrayAffected.filter((item) => data == item.id);
                
                    let sum = 0;
                    let approval_number = '';

                    let get_data = payees.forEach((data,key)=>{ 
                        sum += data.total_amount;
                        approval_number += data.approval_id + ', '
                    });

                    return_data.push({
                        id : payees[0].id,
                        name : payees[0].name,
                        total_amount  : sum,
                        approval_id : approval_number,
                    });
                },

            }

            let return_data = [];

            //step 1: get distinct payable id
            //step 2: foreach all ids got in step 1
            //step 2.1: filter each id 
            //step 2.2: sum all total amount of array got in step 2.1
            //step 2.3: push array and sum to return_data
            let get_data = [...new Set(arrayAffected.map(dataFunctions.getDistinctID))].forEach(dataFunctions.forEachDistinctID);

            return return_data;
        },
        getPayable(){
            let id = this.$route.query.id;

            let filters = {
                status : 'open'
            };

            return axios.get('api/payable/mark-close/'+id+this.generateFilterURL(filters));
        },
        getPayee(payable_availment){
            let availment_payee = [];
            let availment_doctor = [];

            let payee = [...payable_availment];

            payee.forEach((data,key)=> {
                //get provider payee and doctor per availment
                payee[key].provider_payee = data.availment.provider.provider_payee.find((item)=> item.id == data.availment.provider_payee_id);
                payee[key].provider_doctor = data.availment.provider.doctor_providers.find((item)=> item.doctor_id == data.availment.doctor_id);
                
                //push only if not undefined
                if(payee[key].provider_payee)
                {
                    availment_payee.push({
                        id : payee[key].provider_payee.id,
                        name : payee[key].provider_payee.name,
                        total_amount : data.availment.procedures_carewell_charged_total,
                        approval_id : data.availment.approval_id,
                    });
                }
              
                //push only if not undefined
                if(payee[key].provider_doctor)
                {
                    availment_doctor.push({
                        id : payee[key].provider_doctor.doctor_id,
                        name : payee[key].provider_doctor.doctor.name,
                        total_amount : data.availment.doctors_carewell_charged_total,
                        approval_id : data.availment.approval_id,
                    });
                }
            });

            return {
                availment_payee,
                availment_doctor
            }
        },
        addInfoPayee({objectAffected = null, propertyAffected = null, objParent = null}){
            let obj = [...objectAffected];

            if(objParent[propertyAffected].length > 0)
            {
                obj.forEach((data,key) => {
                    data.release_date = objParent[propertyAffected][key].release_date
                    data.check_number = objParent[propertyAffected][key].check_number
                    data.cv_number = objParent[propertyAffected][key].check_voucher_number
                    data.check_date = objParent[propertyAffected][key].check_date
                    data.bank_id = objParent[propertyAffected][key].bank_id
                    data.received_by = objParent[propertyAffected][key].received_by
                    data.table_id = objParent[propertyAffected][key].id
                })
            }

            return obj;
        },
        async loadPayable(){
            //get payable data and load all of its availments
            //get provider payee and doctor for each availment

            let payableAPI = await this.getPayable();

            const { data: response } = payableAPI;

            this.form = response;

            console.log(response.payable_availment)

            let payable_availment = await this.getPayee(response.payable_availment);
            
            this.availment = {
                payee : await this.getDistinctPayable({arrayAffected:payable_availment.availment_payee}),
                doctor : await this.getDistinctPayable({arrayAffected:payable_availment.availment_doctor}),
            }

            if(this.$route.query.status === "edit" || this.$route.query.status === "view")
            {
                this.availment.payee = await this.addInfoPayee({objectAffected: this.availment.payee,
                                                            propertyAffected:"payable_provider",
                                                            objParent: response});
   
  
                this.availment.doctor = await this.addInfoPayee({objectAffected: this.availment.doctor,
                                                                propertyAffected:"payable_doctor",
                                                                objParent: response}); 
            }
                                                 
            // console.log(response,'response');
            console.log(this.availment,'availment');
           
        },
    },
    mounted(){
        this.loadPayable();
    }
}
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .adjust-text{
        font-size: 15px;
    }

    .create-payable__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }
        
        .holder-two-column {
            border: 1px solid #919191;
            padding: 15px;
            display: grid;
            background-color: #ffffff;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 50px;
            margin-bottom: 10px;
            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

        }
    }


</style>