<template>
    <span>
        <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" persistent scrollable max-width="950px">
            <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
            <v-card class="create-member">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">View Member</v-card-title>
                <v-card-text>
                    
                    <div class="top-holder">
                        <div class="subheading font-weight-medium mb-2">Basic Information</div>
                        <div class="top-input__holder">
                            <v-text-field :rules="requiredRules" color="accent" label="Last Name"  v-model='form.last_name' outline readonly></v-text-field>
                            <v-text-field :rules="requiredRules" color="accent" label="First Name" v-model='form.first_name' outline readonly></v-text-field>
                            <v-text-field color="accent" label="Middle Name" v-model='form.middle_name' outline readonly></v-text-field>
                            <v-text-field color="accent" label="Mother's Maiden Name" v-model='form.mothers_maiden_name' outline readonly></v-text-field>
                            <v-menu
                                v-model="datepicker_info"
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
                                    v-model="form.birth_date"
                                    :default="form.birth_date"
                                    label="Birthdate"
                                    append-icon="event"
                                    outline
                                    readonly
                                ></date-format>
                                <v-date-picker v-model="form.birth_date" @input="datepicker_info = false" :max="new Date().toISOString().substr(0, 10)" min="1950-01-01"></v-date-picker>
                            </v-menu>
                            <gender-select-box 
                                v-model="form.gender"
                                :rules="requiredRules" 
                                :default="form.gender" 
                                color="accent" 
                                outline 
                                disabled
                            >
                            </gender-select-box>
                            <marital-status-select-box 
                                v-model="form.marital_status"
                                :rules="requiredRules" 
                                :default="form.marital_status" 
                                color="accent" 
                                outline 
                                disabled
                            >
                            </marital-status-select-box>
                            <v-text-field color="accent" label="Contact Number"  v-model="form.contact_number" outline readonly></v-text-field>
                            <v-text-field color="accent" label="Email Address" v-model="form.email" outline readonly></v-text-field>
                            <div></div>
                        </div>
                        <v-textarea outline name="input-7-4" label="Permanent Address" v-model="form.permanent_address"></v-textarea>
                        <div class="present-address">
                            <v-checkbox @change="presentAddress" label="Same with Permanent Address?" v-model="check_same_address" disabled></v-checkbox>
                            <v-textarea outline name="input-7-4" label="Present Address" :disabled="check_same_address" v-model="form.present_address"></v-textarea>
                        </div>
                </div>
                    <div class="mid1-holder">
                        <div class="subheading font-weight-medium mb-2">Dependents</div>
                        <div class="mid1-input__holder">
                            <v-data-table
                                :headers="headers"
                                :items="list"
                                 hide-actions
                            >
                                <template slot="items" slot-scope="props">
                                    <td>
                                        <v-text-field  
                                            label="Full Name" 
                                            v-model="list[props.index].full_name" 
                                            outline
                                            readonly
                                        ></v-text-field>
                                    </td>           
                                    <td>
                                        <v-menu
                                            v-model="dependents_menu[props.index]"
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
                                                v-model="list[props.index].birth_date"
                                                :default="list[props.index].birth_date"
                                                label="Birthdate"
                                                append-icon="event"
                                                outline
                                                readonly
                                            ></date-format>
                                            <v-date-picker v-model="list[props.index].birth_date" @input="datepicker_dependent = false" :max="new Date().toISOString().substr(0, 10)" min="1950-01-01"></v-date-picker>
                                        </v-menu>
                                    </td>   
                                    <td>
                                        <relationship-select-box 
                                            v-model="list[props.index].relationship"
                                            :default="list[props.index].relationship"
                                            color="accent" 
                                            style="width: 200px"
                                            outline 
                                            disabled
                                        >
                                        </relationship-select-box>
                                        <!-- <v-select style="width: 200px" color="accent" :items="relationship" label="Relationship" outline v-model="list[props.index].relationship"></v-select> -->
                                    </td> 
                                    <td class="justify-start layout px-0">
                                        <!-- <v-icon medium class="mr-2" @click="addRow" color="green"> add </v-icon>
                                        <v-icon medium class="mr-2" @click="listIndex(props.index)" color="red"> close </v-icon> -->
                                     </td>                       
                                </template>
                            </v-data-table>
                                        <!-- CREATE -->
                        </div>
                    </div>
                    <div class="mid2-holder">
                        <div class="subheading font-weight-medium mb-2">Government</div>
                        <div class="mid2-input__holder">
                            <v-text-field color="accent" label="Social Security System Number" outline v-model="form.sss_number" readonly></v-text-field>
                            <v-text-field color="accent" label="PhilHealth Number" outline v-model="form.philhealth_number" readonly></v-text-field>
                            <v-text-field color="accent" label="Taxpayer Identification Number" outline v-model="form.tin" readonly></v-text-field>
                            <v-text-field color="accent" label="Pag-IBIG Number" outline v-model="form.pagibig_number" readonly></v-text-field>
                        </div>
                    </div>
                    <div class="bottom-holder">
                        <div class="bottom-holder__title mb-2">
                            <div class="subheading font-weight-medium">Carewell Details</div>
                            <div class="btn-holder">
                                <!-- <v-btn class="btn-holder--width m-0" dark depressed color="info" @click="enableChangeCompany">
                                    <v-icon class="mr-4">mdi-square-edit-outline</v-icon>Change Company
                                </v-btn> -->
                                <!-- <v-btn depressed color="success">
                                    <v-icon class="mr-3">mdi-rotate-3d</v-icon> Transaction
                                </v-btn> -->
                                <transaction-member 
                                    class="btn-holder--width" 
                                    :item_member_id="form.id"
                                />
                            </div>
                        </div>
                        <div class="bottom-input__holder">
                            <global-select-box
                                v-model="form.company_ref_id"
                                item-text="name"
                                item-value="id"
                                api-link="api/select/member/company"
                                label="Select Company"
                                :disabled="enable_change_company"
                                :rules="requiredRules"
                                :filters={is_archived:0}
                                :is-load="dialog"
                                :default="form.company_ref_id"
                                return-object
                                outline
                                @input="getEmitData($event)"
                            ></global-select-box>

                            <v-select color="accent"
                                :rules="requiredRules" 
                                :items="select_deployment"
                                item-text="name"
                                item-value="id" 
                                label="Select Deployment" 
                                outline 
                                :disabled="enable_change_company"
                                v-model="form.deployment"
                            >
                            </v-select>

                             <global-select-box
                                v-model="form.mode_of_payment"
                                item-text="name"
                                item-value="id"
                                api-link="api/payment_mode"
                                label="Select Mode of Payment"
                                :default="form.mode_of_payment"
                                :rules="requiredRules"
                                :disabled="enable_change_company"
                                :filters={is_archived:0}
                                :is-load="dialog"
                                outline
                            >
                            </global-select-box>

                            <v-select color="accent"
                                :rules="requiredRules" 
                                :items="select_coverage_plan" 
                                label="Select Coverage Plan"
                                item-text="name"
                                item-value="id" 
                                outline 
                                :disabled="enable_change_company"
                                v-model="form.coveragePlan"
                            >
                            </v-select>

                             <v-text-field 
                                color="accent" 
                                label="Employee Number" 
                                :disabled="enable_change_company" 
                                outline 
                                v-model="form.employee_number"
                             >
                             </v-text-field>
                        </div>
                    </div>
                
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <!-- <v-btn color="accent" depressed type="submit" ref="submit_button" :loading="loading">UPDATE</v-btn> -->
                </v-card-actions>
            </v-card>
            </v-form>
        </v-dialog>
    </span>
</template>

<script>
import GlobalMixins from '../../../otherMixins/GlobalMixins';
import MemberMixins from '../mixins/MemberMixins';
    export default {
        data() {
            return {
                datepicker_dependent: false,
                datepicker_info: false,
                popup_create_member: false,
                basic_info_date: null,
                basic_info_menu: false,
                check_same_address: false,
                requiredRules: [
                    v => !!v || 'Input is required',
                ],
                dependents_menu: [],
                select_deployment: [],
                select_coverage_plan: [],
                filters:{},
                dialog: false,
                headers: [
                    { text: 'Full Name', sortable: false , align: 'center' } ,
                    { text: 'Birth Date', sortable: false , align: 'center' } ,
                    { text: 'Relationship', sortable: false , align: 'center' },
                    { text: '', sortable: false , align: 'center' },
                ],
                tablePagination:{
                    rowsPerPage: -1,
                },
                list: [],
                company_id: '',
                deployment_id:'',
                deployment:null,
                payment_id: '',
                coverage_plan: '',
                loading : false,
                enable_change_company: true,
            }
        },
        props : {
            form:{
                type: Object,
                company_id: ''  
            },
            eventName: {
                type: String,
                default: 'editMember'
            }
        },
        methods: {
            getEmitData(data){
                this.form.company_ref_id = data;
                this.getDeployment();
                return;
            },
            enableChangeCompany(){
                this.enable_change_company = !this.enable_change_company;
            },
            save(){
                this.loading = true;

                this.form.dependents = this.list;

                if(typeof this.form.company_ref_id === "number")
                {   
                    this.form.company_ref_id = {...this.form.company}
                }

                if(this.$refs.form_submit.validate())
                {
                    axios.patch(`/api/member/${this.form.id}`, this.form)
                    .then(response => {
                        EventBus.$emit(this.eventName,response)
                        this.toaster(response.data, response.status)
                        this.close()
                    })
                    .catch(error => {
                        this.toaster(error.response.data, error.status)
                    })
                    .finally(() => {
                        this.loading = false
                         this.enable_change_company = false
                    })
                    }
                else
                {
                    this.loading     = false;
                }
            },
            setData(memberData){
                this.form.mode_of_payment = memberData.company.payment_mode_id
                this.form.company_ref_id =  memberData.company.company_id

                this.select_deployment.push(memberData.company.company_deployment)
                this.form.deployment = this.select_deployment[0]

                 this.select_coverage_plan.push(memberData.company.coverage_plan)
                this.form.coveragePlan = this.select_coverage_plan[0]

                this.form.employee_number = memberData.company.company_ref_id
            },
            setDependentAndAddress(memberData){
                if(memberData.dependents.length == 0)
                {
                    this.addRow();
                }
                else
                {
                    this.list = memberData.dependents
                    if(memberData.permanent_address === memberData.present_address)
                    {
                        this.check_same_address = true
                    }
                }
            },
            async loadDialog(){
                const { data: memberData } = await axios.get('/api/show/member/'+this.form.id);

                await this.setData(memberData);

                await this.setDependentAndAddress(memberData);

                this.dialog = await true
            },
        },
        mounted(){
        },
        mixins : [GlobalMixins,MemberMixins],
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .create-member {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .top-holder {
            border: 1px solid #ededed;
            padding: 15px;
            margin-bottom: 10px;

            .top-input__holder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
            }

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            textarea {
                height: 55px;
            }

            .present-address {
                .v-input__slot {
                    margin-bottom: 0px;
                }

                .v-input {
                    margin-top: 0px;
                }

                label {
                    margin-bottom: 0px;
                }

                textarea {
                    height: 55px;
                }
            }
        }

        .mid1-holder {
            padding: 15px;
            border: 1px solid #ededed;
            margin-bottom: 10px;

            .mid1-input__holder {
                display: grid;
                grid-template-columns: 1fr;
                grid-column-gap: 20px;

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

        .mid2-holder {
            border: 1px solid #ededed;
            padding: 15px;
            margin-bottom: 10px;

            .mid2-input__holder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;

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

        .bottom-holder {
            padding: 10px;
            border: 1px solid #ededed;

            .bottom-holder__title
            {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                .subheading{
                    align-self: center;
                }
                .btn-holder
                {
                    justify-self: end;
                    margin-left: 25px;

                    .btn-holder--width
                    {
                        width: 18rem;
                    }
                }
            }
            .bottom-input__holder
            {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;

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
    }
</style>