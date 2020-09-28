<template>
    <span>
        <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" persistent scrollable max-width="1000px">
            <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
            <v-card class="create-member">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Create Member</v-card-title>
                <v-card-text>
                    
                    <div class="top-holder">
                        <div class="subheading font-weight-medium mb-2">Basic Information</div>
                        <div class="top-input__holder">
                            <v-text-field :rules="requiredRules" color="accent" label="Last Name"  v-model="form.last_name"  outline></v-text-field>
                            <v-text-field :rules="requiredRules" color="accent" label="First Name" v-model="form.first_name" outline></v-text-field>
                            <v-text-field color="accent" label="Middle Name" v-model="form.middle_name" outline></v-text-field>
                            <v-text-field color="accent" label="Mother's Maiden Name" v-model="form.mothers_maiden_name" outline></v-text-field>
                            <v-menu
                                v-model="datepicker_info"
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
                                    v-model="form.birth_date"
                                    label="Birthdate"
                                    append-icon="event"
                                    outline
                                    readonly
                                ></date-format>
                                <!-- <v-text-field
                                    slot="activator"
                                    v-model="form.birth_date"
                                    label="Birthdate"
                                    append-icon="event"
                                    outline
                                    readonly
                                ></v-text-field> -->
                                <v-date-picker v-model="form.birth_date" @input="datepicker_info = false" :max="new Date().toISOString().substr(0, 10)" min="1950-01-01"></v-date-picker>
                            </v-menu>

                            <gender-select-box 
                                v-model="form.gender"
                                color="accent" 
                                outline 
                            ></gender-select-box>

                            <marital-status-select-box 
                                v-model="form.marital_status"
                                color="accent" 
                                outline 
                            ></marital-status-select-box>

                            <v-text-field 
                                color="accent" 
                                label="Contact Number"  
                                v-model="form.contact_number" 
                                outline
                            ></v-text-field>

                            <v-text-field 
                                color="accent" 
                                label="Email Address" 
                                v-model="form.email" 
                                outline
                            ></v-text-field>

                            <div></div>
                        </div>

                        <v-textarea 
                            outline 
                            name="input-7-4" 
                            label="Permanent Address" 
                            v-model="form.permanent_address"
                        ></v-textarea>

                        <div class="present-address">
                            <v-checkbox 
                                @change="presentAddress" 
                                label="Same with Permanent Address?" 
                                v-model="check_same_address" 
                            ></v-checkbox>

                            <v-textarea 
                                outline 
                                name="input-7-4" 
                                label="Present Address" 
                                :disabled="check_same_address" 
                                v-model="form.present_address"
                            ></v-textarea>
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
                                        >
                                        </v-text-field>
                                    </td>           
                                    <td>
                                        <v-menu
                                            v-model="dependents_menu[props.index]"
                                            :close-on-content-click="false"
                                            :nudge-right="0"
                                            lazy
                                            transition="scale-transition"
                                            offset-y
                                            full-width
                                            min-width="200px"
                                        >
                                            <date-format
                                               slot="activator"
                                                v-model="list[props.index].birth_date"
                                                label="Birthdate"
                                                append-icon="event"
                                                outline
                                                readonly
                                            ></date-format>
                                            <!-- <v-text-field
                                            slot="activator"
                                            v-model="list[props.index].birth_date"
                                            label="Birthdate"
                                            append-icon="event"
                                            outline
                                            readonly
                                            ></v-text-field> -->
                                            <v-date-picker v-model="list[props.index].birth_date" @input="datepicker_dependent = false" :max="new Date().toISOString().substr(0, 10)" min="1950-01-01"></v-date-picker>
                                        </v-menu>
                                    </td>   
                                    <td>
                                        <relationship-select-box 
                                            v-model="list[props.index].relationship"
                                            color="accent" 
                                            style="width: 200px"
                                            outline 
                                        >
                                        </relationship-select-box>
                                    </td> 
                                    <td>
                                        <v-icon medium class="mr-2" @click="addRow" color="green"> add </v-icon> 
                                        <v-icon medium class="mr-2" @click="listIndex(props.index)" color="red"> close </v-icon>
                                     </td>                       
                                </template>
                            </v-data-table>
                        </div>
                    </div>
                                        <!-- CREATE -->
                    <div class="mid2-holder">
                        <div class="subheading font-weight-medium mb-2">Government</div>
                        <div class="mid2-input__holder">
                            <v-text-field color="accent" label="Social Security System Number" outline v-model="form.sss_number"></v-text-field>
                            <v-text-field color="accent" label="PhilHealth Number" outline v-model="form.philhealth_number"></v-text-field>
                            <v-text-field color="accent" label="Taxpayer Identification Number" outline v-model="form.tin"></v-text-field>
                            <v-text-field color="accent" label="Pag-IBIG Number" outline v-model="form.pagibig_number"></v-text-field>
                        </div>
                    </div>
                    <div class="bottom-holder">
                        <div class="bottom-holder__title mb-2">
                            <div class="subheading font-weight-medium">Carewell Details</div>
                            <div class="btn-holder">
                                <v-btn dark depressed color="info" v-if="enable_change_company">
                                    <v-icon class="mr-3">mdi-square-edit-outline</v-icon> Change Company
                                </v-btn>
                                <transaction-member v-if="enable_change_company"/>
                            </div>
                        </div>
                        <div class="bottom-input__holder">
                            <global-select-box
                                v-model="form.company_ref_id"
                                item-text="name"
                                item-value="id"
                                api-link="api/select/member/company"
                                label="Select Company"
                                :rules="requiredRules"
                                :filters={is_archived:0}
                                :is-load="dialog"
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
                                v-model="form.deployment"
                            >
                            </v-select>

                            <global-select-box
                                v-model="form.mode_of_payment"
                                item-text="name"
                                item-value="id"
                                api-link="api/payment_mode"
                                label="Select Mode of Payment"
                                :rules="requiredRules"
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
                                v-model="form.coveragePlan"
                            >
                            </v-select>

                            <v-text-field color="accent" label="Employee Number" outline v-model="form.employee_number"></v-text-field>
                        </div>
                    </div>
                
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed type="submit" ref="submit_button" :loading="loading">SAVE</v-btn>
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
        mixins : [GlobalMixins,MemberMixins],
        data: () => ({
                datepicker_dependent: false,
                datepicker_info: false,
                popup_create_member: false,
                check_same_address: false,
                dependents_menu: [],
                select_deployment: [],
                select_coverage_plan: [],
                form: {
                    birth_date: '',
                    present_address: '',
                },
                dialog: false,
                selected: {},
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
                filters:{},
                payment_mode: [],
                requiredRules: [
                    v => !!v || 'Input is required',
                ],
                loading : false,
                enable_change_company: true,
        }),
        props : {
            item:{
                type: Object,
            },
            eventName: {
                type: String,
                default: 'createMember'
            }
        },
        methods: {
            getEmitData(data){
                console.log({data})
                this.form.company_ref_id = data;
                this.getDeployment();
                return;
            },
            save(){
                this.form.dependents = this.list

                if(this.$refs.form_submit.validate())
                {
                    this.loading = true;
                    // if(this.form.dependents.length >= 1)
                    // {
                        axios.post('/api/member', this.form).then(response =>
                        {
                            
                            EventBus.$emit(this.eventName, response)
                            this.toaster(response.data, response.status)
                            this.$refs.form_submit.reset();
                            this.close()
                            // this.get_count()
                        })
                        .catch(errors =>
                        {
                            this.error_message  = errors.response;
                            EventBus.$emit(this.eventName, this.error_message)
                            this.toaster(this.error_message.data, errors.response.status)
                        })
                        .finally(()=>{
                            this.loading = false
                        });
                    // }
                    // else
                    // {
                    //     this.toaster("Fill Up At Least One Dependent", 400)
                    // }
                }
                else
                {
                    this.loading     = false;
                }
            
            },
            loadDialog(){
                this.dialog = true
                this.enable_change_company= false
                this.list = []
                this.addRow()
            }
        },
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
            padding: 15px;
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