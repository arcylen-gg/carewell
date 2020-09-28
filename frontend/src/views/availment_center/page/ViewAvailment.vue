<template>
    <div class="create-availment__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <v-form ref="form" @submit.prevent="save" lazy-validation>
            <div class="top-holder">
                <div>
                <v-flex text-xs-right>
                    <v-btn color="accent" depressed type="submit">Save & Close</v-btn> 
                    <!-- <v-btn color="accent" depressed @click="save">Save & Close DUMMY</v-btn>  -->
                </v-flex>
                </div>
                <div class="top-holder-caller">
                    <caller-information ref="callerInformation"></caller-information>
                </div>
                <member-information ref="memberInformation"></member-information>
                <availment-information ref="availmentInformation"></availment-information>
            </div>
            <procedure-information ref="procedureInformation"></procedure-information>
            <span>&nbsp;</span>
            <physician-information ref="physicianInformation"></physician-information>
            <div class="top-holder">
                <span>&nbsp;</span>
                <payee-information ref="payeeInformation"></payee-information>
            </div>  
        </v-form>
    </div>
</template>

<script>
import AvailmentMixins from '../mixins/AvailmentMixins';
import { availmentData } from '../mixins/AvailmentStore.js';
import callerInformation from '../page/components/CallerInformation';
import memberInformation from '../page/components/MemberInformation';
import availmentInformation from '../page/components/AvailmentInformation';
import procedureInformation from '../page/components/ProcedureInformation';
import physicianInformation from '../page/components/PhysicianInformation';
import payeeInformation from '../page/components/PayeeInformation';

    export default {
        mixins :[
            AvailmentMixins,
        ],
        components:{
            callerInformation,
            memberInformation,
            availmentInformation,
            procedureInformation,
            physicianInformation,
            payeeInformation,
        },
        data: () => ({
            form : {},
            breadcrumbs: [
                { text: 'Dashboard', disabled: false }, 
                { text: 'Availment Center', disabled: false }, 
                { text: 'View Availment', disabled: true } 
            ],
            ret_val : 0,
        }),
        methods : {
            save(){
                this.$router.push('/availment-center');
            },
            setCallerData(data){
                this.$refs.callerInformation.form = {
                    caller_name : data.caller_name,
                    caller_position : data.caller_position,
                    caller_contact_number : data.caller_contact_number,
                    caller_date : data.caller_date,
                }
            },
            setMemberData(data){
                this.$refs.memberInformation.form = {
                    member : data.member
                }
        
                this.$refs.memberInformation.setMemberSelectOptions([data.member]);

                this.$refs.memberInformation.memberSelect();
            },
            setAvailmentData(data){
                let coverage_benefit_id = data.coverage_plan.coverage_plan_benefits.find(item => item.coverage_plan_id == data.coverage_plan_id && item.benefit_type_id == data.benefit_type_id);
                this.$refs.availmentInformation.form = {
                        provider : data.provider,
                        benefitType : {
                            id: coverage_benefit_id.id,
                            benefit_type :   data.benefit_type,
                            benefit_type_id : data.benefit_type_id,
                            // coverage_plan_id : data.coverage_plan_id,
                        },                               
                        date_avail : data.date_avail,
                        discharged_date : data.discharged_date,
                        chief_complaint : data.chief_complaint,
                        initial_diagnosis : data.initial_diagnosis,
                        final_diagnosis : data.final_diagnosis,
                        diagnosis_id : data.diagnosis_id,
                    }

                this.$refs.availmentInformation.providerSelect()
                this.$refs.availmentInformation.memberBenefitType();
            },
            setPayeeData(data){
                this.$refs.payeeInformation.form = {
                    provider_payee_id : data.provider_payee_id,
                    doctor_id : data.doctor_id
                }
            },
            setProcedureData(data){
                let arrayData = [];
  
                let availment_procedure = data.availment_procedure.map((data,key)=>{
                    arrayData.push({
                        procedure : {
                            procedure_id : data.procedures_id,
                            procedures : data.procedure
                        },
                        procedures_gross_amt : data.gross_amount,
                        procedures_phic_amt : data.phic_charity_swa,
                        procedures_patient_charge : data.patient_charged,
                        procedures_carewell_charge : data.carewell_charged,
                        procedures_charge_other : data.charge_other,
                        procedures_remarks : data.remarks,
                        procedures_is_disapproved : data.is_disapproved ? true : false
                    })
                });

                this.$refs.procedureInformation.procedure_list = arrayData
            },
            setPhysicianData(data){
                let arrayData = [];
                
                 let availment_doctor = data.availment_doctor.map((data,key) => {
                    arrayData.push({
                        doctor_id : data.doctor_id,
                        specialization_id: data.specialization_id,
                        doctor_rate_rvs : data.rate_rvs,
                        doctor_procedures_id: data.procedures_id,
                        doctor_fee: data.doctors_fee,
                        doctor_phic_amt: data.phic_charity_swa,
                        doctor_patient_charge: data.patient_charged,
                        doctor_carewell_charge: data.carewell_charged,
                        doctor_charge_other : data.charge_other, 
                    });
                });

                this.$refs.physicianInformation.physician_list = arrayData;
                availmentData.dispatch('physicianInformation',arrayData);
            },
            async showData(){
                const {data : apiData} = await axios.get(`/api/availment/${this.$route.query.id}`);

                this.form.dBData = apiData

                await this.setCallerData(apiData);
                await this.setMemberData(apiData);
                await this.setAvailmentData(apiData);
                await this.setPayeeData(apiData);
                await this.setProcedureData(apiData);
                await this.setPhysicianData(apiData);
            }
        },  
        mounted(){
            this.showData();
        },
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .create-availment__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }


        .top-holder
        {
            border: 1px solid #ededed;
            padding: 10px;
            display: grid;
            background-color: #ffffff;
            grid-template-columns: repeat(1, 1fr);
            grid-column-gap: 30px;
            margin-bottom: 10px;
            .v-text-field {
                font-size: 10px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .top-holder-caller {
                .holder
                {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    grid-column-gap: 30px;
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

            .top-holder-two {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 30px;
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
            .top-holder-three {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                grid-column-gap: 30px;
                margin-bottom: 10px;

                .v-text-field {
                font-size: 15px;

                    .v-input__slot {
                    border: 1px solid #919191!important;
                    }
                }
                :nth-child(3), :nth-child(1)
                {
                    text-align: left !important;
                }
            }
            .top-holder-four {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-column-gap: 30px;
                margin-bottom: 10px;

                .v-text-field {
                font-size: 15px;

                    .v-input__slot {
                    border: 1px solid #919191!important;
                    }
                }
                :nth-child(3), :nth-child(1)
                {
                    text-align: left !important;
                }
            }
            .total-table
            {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-column-gap: 50px;
                margin-bottom: 10px;
            }
            .tab-title
            {
                font-size : 18px !important;
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
        .col-six-l
        {
            width: 100%;
            float : left;
            cursor : pointer;
            position : relative;
            padding : 5px;
        }
        .col-six-r
        {
            width: 100%;
            float : right;
            cursor : pointer;
            position : relative;
            padding : 5px;
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