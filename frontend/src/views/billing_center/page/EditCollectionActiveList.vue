<template>
    <div class="edit-collection__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="top-holder-one">
            <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
            <div class="top-holder-two">
                <div/>
                <div class="pull-right">
                    <v-btn 
                        color="primary" 
                        depressed type="submit" 
                        ref="submit_button" 
                        class="mb-2 pull-right float-right"
                        :loading="loading"
                    >
                        Update
                    </v-btn>
                </div>
                <v-text-field 
                    slot="activator" 
                    v-model="items.reference_number" 
                    label="Reference Number"
                    append-icon="reorder" outline
                ></v-text-field>
                <div/>
            </div>   

            <div class="top-holder-two">            
                <global-select-box
                    :rules="requiredRules" 
                    v-model="items.company_id" 
                    api-link="api/company"
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
                <!-- <global-select-box
                    :rules="requiredRules"
                    v-model="items.deployment_id"
                    api-link="api/company_deployment"
                    :default="items.deployment_id" 
                    is-load
                    color="accent" 
                    item-value="id" 
                    item-text="name" 
                    label="Select Deployment" 
                    outline
                >
                </global-select-box> -->
                <v-select color="accent"
                    :rules="requiredRules" 
                    :items="select_deployment_filtered"
                    item-text="name"
                    item-value="id" 
                    label="Select Deployment" 
                    outline 
                    v-model="items.deployment_id"
                >
                </v-select>
            </div>

            <div class="top-holder-two">
                <global-select-box
                    :rules="requiredRules"
                    v-model="items.payment_mode_id" 
                    :filters="{is_archived:0}"
                    :default="items.payment_mode_id" 
                    api-link="api/payment_mode"
                    is-load
                    color="accent" 
                    item-value="id" 
                    item-text="name" 
                    label="Select Payment Mode" 
                    outline
                >
                </global-select-box>

                <global-select-box
                    ref="payment_term"
                    :rules="requiredRules"
                    v-model="items.payment_term_id"
                    :default="items.payment_term_id"
                    :filters="{is_archived:0}"
                    api-link="api/payment-term"
                    is-load
                    color="accent" 
                    item-value="id" 
                    item-text="name" 
                    label="Terms of Payment"  
                    outline
                    return-object
                    @input="addToCALDate($event)"
                >
                </global-select-box>

                <v-menu 
                    ref="cal_date_menu" 
                    :close-on-content-click="false" 
                    v-model="cal_date" 
                    :nudge-right="40" 
                    lazy transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px"
                >
                    <date-format 
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.payment_cal_date" 
                        label="Cal Date"
                        append-icon="event" 
                        readonly 
                        outline
                    />
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.payment_cal_date"
                        min="1950-01-01" 
                        @change="cal_date_save"
                    />
                </v-menu>
                <v-menu 
                    ref="due_date_menu" 
                    :close-on-content-click="false" 
                    v-model="due_date" 
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
                        :rules="requiredRules" 
                        v-model="items.payment_due_date" 
                        label="Due Date"
                        append-icon="event" 
                        readonly 
                        outline 
                    />
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.payment_due_date"
                        min="1950-01-01" 
                        @change="due_date_save" 
                    />
                </v-menu>
                <v-menu 
                    ref="start_info_menu" 
                    :close-on-content-click="false" 
                    v-model="start_menu" 
                    :nudge-right="40" 
                    lazy 
                    transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px"
                >
                    <date-format 
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.payment_start_date" 
                        label="Start Date"
                            append-icon="event" 
                        readonly 
                        outline
                    />
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.payment_start_date"
                        min="1950-01-01" @
                        change="start_info_save"
                    />
                </v-menu>
                <v-menu 
                    ref="end_info_menu" 
                    :close-on-content-click="false" 
                    v-model="end_menu" 
                    :nudge-right="40" 
                    lazy transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px"
                >
                    <date-format 
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.payment_end_date" 
                        label="End Date"
                        append-icon="event" 
                        readonly 
                        outline
                    />
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.payment_end_date"
                        min="1950-01-01" 
                        @change="end_info_save"
                    />
                </v-menu>
            </div>
            <paid-count-list ref="paidCountList"/>
        </v-form>
        </div>
        <!-- <cal-member-table :cal_members="items.cal_member"></cal-member-table> -->
    </div>
</template>

<script>
import CalMemberTable from '../components/CalMemberTable';
import paidCountList from '../page/component/CollectionActiveListPaidCountList';

    export default {
        components : {
            CalMemberTable,
            paidCountList
        },
        computed:
        {
            select_deployment_filtered()
            {
                if (this.items.company_id)
                {
                    return this.select_deployment.filter(data => this.items.company_id === data.company_id);
                }
                else
                {
                    return this.select_deployment;
                }
            }
        },
        data: () => ({
            form : {
                age_bracket_from : 1,
                age_bracket_to : 0,
                age_bracket : [],
            },
            items: [],
            breadcrumbs: [
                { text: 'Dashboard', disabled: false }, 
                { text: 'Billing Center', disabled: false }, 
                { text: 'Update CAL', disabled: true } 
            ],
            start_info_menu: [],
            start_menu:false,
            end_info_menu: [],
            end_menu:false,
            cal_date_menu: [],
            cal_date:false,
            due_date_menu: [],
            due_date:false,
            filters:{},
            requiredRules: [
                    v => !!v || 'Input is required',
                ],
            loading: false,
            select_deployment: []
        }),
        methods : {
            async getAllCompanyDeployment()
            {
                try
                {
                    const response = await axios.get(`/api/company_deployment?showAll=1`);
                    return response.data.data;
                }
                catch (e)
                {
                    return null;
                }
            },
            addToCALDate(event){
                let cal_date = this.items.payment_cal_date
                let newCalDate = new Date(cal_date.substr(0,4), cal_date.substr(5,2) ,cal_date.substr(7,2))
                let date = newCalDate;
                let addDay = +cal_date.substr(8,2) + event.number + 1;
                let newDate = new Date(date.getFullYear(),date.getMonth(),addDay).toISOString().substr(0,10);
                this.items.payment_due_date = newDate;
                // console.log({day,addDay,newDate,newCalDate})
            },
            async showData() {
                this.loading = true;

                const {data : getCAL} = await axios.get(`/api/cal/${this.$route.query.id}`);

                this.items = await getCAL;

                this.loading = await false;
            },
            start_info_save(date) {
                this.$refs.start_info_menu.save(date)
            },
            end_info_save(date) {
                this.$refs.end_info_menu.save(date)
            },
            cal_date_save(date) {
                this.$refs.cal_date_menu.save(date)
                let data = this.$refs.payment_term.selectOptions.find(item => item.id == this.items.payment_term_id)
                this.addToCALDate(data);
            },
            due_date_save(date) {
                this.$refs.cal_date_menu.save(date)
            },
            save() {
               var s_date = new Date(this.items.payment_start_date);
                var e_date = new Date(this.items.payment_end_date);
                if(this.$refs.form_submit.validate())
                {
                    if(s_date > e_date){
                        var alert = 'Start date not be greater than end date.';
                        this.toaster(alert)
                    }
                    else
                    {
                          
                        this.items.payment_term_id = this.items.payment_term_id.id ||  this.items.payment_term_id
                   
                        axios.put(`/api/cal/${this.items.id}`, this.items)
                        .then(response => {
                            this.redirectIfNotExisting()
                            this.toaster(response.data, response.status)
                        })
                        .catch(error => {
                            this.toaster(error.response.data, error.status)
                        })
                        .finally(() => {
                            this.loading = false
                        })
                    }
                }
            },
            redirectIfNotExisting()
            {
                this.$router.push('/billing-&-collection-center')
            },
        },
        mounted() {
           if(this.$route.query.id)
            {
                this.showData();
            }
            else
            {
                this.redirectIfNotExisting()
            }
        },
        async created()
        {
            this.select_deployment = await this.getAllCompanyDeployment();
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .edit-collection__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }


        .top-holder-one 
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

            .v-input--radio-group {
                margin-top: 0px !important;

                .v-input__slot {
                    margin-bottom: 0px !important;
                    }
                }
            }
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