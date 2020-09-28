<template>
    <div class="create-collection__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="top-holder-one">
            <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
            <div class="top-holder-two">
                <div/>
                <div class="pull-right"> 
                    <b-dropdown id="ddown1" size="lg" right text="Actions" class="mb-2 pull-right ml-auto float-right">
                        <b-dropdown-item @click="save(1)">Save & Close</b-dropdown-item>
                        <b-dropdown-item @click="save(0)">Save & New</b-dropdown-item>
                    </b-dropdown>
                    <!-- <v-select
                        class="resize-action-submit float-right "
                        :items="submit_action"
                        item-text="text"
                        item-value="value"
                        box
                        :loading="loading"
                        :disabled="loading"
                        label="Action"
                        @change="save($event)"
                    ></v-select> -->
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
                    :filters="{is_archived:0}"
                    api-link="api/company"
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
                    :rules="requiredRules"
                    v-model="items.payment_term"
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
                    offset-y full-width min-width="290px"
                    :disabled="cal_date_disabled"
                >
                    <date-format
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.cal_date" 
                        :default="new Date().toISOString().substr(0,10)"
                        label="Cal Date"
                        append-icon="event" 
                        readonly 
                        outline
                    >
                    </date-format>
                    <!-- <v-text-field 
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.cal_date" 
                        label="Cal Date"
                        append-icon="event" 
                        readonly 
                        outline
                    >
                    </v-text-field> -->
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.cal_date"
                        min="1950-01-01" 
                        @change="cal_date_save"
                    ></v-date-picker>
                </v-menu>
                
                <v-menu 
                    v-model="due_date" 
                    ref="due_date_menu" 
                    :close-on-content-click="false" 
                    :nudge-right="40" 
                    lazy 
                    transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px" 
                    disabled
                >
                    <!-- <v-text-field 
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.due_date" 
                        label="Due Date"
                        append-icon="event" 
                        readonly 
                        outline
                    >
                    </v-text-field> -->
                    <date-format
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.due_date" 
                        label="Due Date"
                        append-icon="event" 
                        readonly 
                        outline
                    ></date-format>
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.due_date"
                        min="1950-01-01" 
                        @change="due_date_save"
                        >
                    </v-date-picker>
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
                    <!-- <v-text-field 
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.start_date" 
                        label="Start Date"
                        append-icon="event" 
                        readonly 
                        outline
                    >
                    </v-text-field> -->
                    <date-format
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.start_date" 
                        label="Start Date"
                        append-icon="event" 
                        readonly 
                        outline
                    ></date-format>
                    <v-date-picker 
                        ref="basic_info_picker"
                        v-model="items.start_date"
                        min="1950-01-01"
                        @change="start_info_save"
                    >
                    </v-date-picker>
                </v-menu>
                <v-menu 
                    ref="end_info_menu" 
                    :close-on-content-click="false" 
                    v-model="end_menu" 
                    :nudge-right="40" 
                    lazy 
                    transition="scale-transition" 
                    offset-y 
                    full-width 
                    min-width="290px"
                >
                    <!-- <v-text-field 
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.end_date" 
                        label="End Date"
                        append-icon="event" 
                        readonly 
                        outline
                    >
                    </v-text-field> -->
                    <date-format
                        slot="activator" 
                        :rules="requiredRules" 
                        v-model="items.end_date" 
                        label="End Date"
                        append-icon="event" 
                        readonly 
                        outline
                    ></date-format>
                    <v-date-picker 
                        ref="basic_info_picker" 
                        v-model="items.end_date"
                        min="1950-01-01" 
                        @change="end_info_save"
                    >
                    </v-date-picker>
                </v-menu>
            </div>
        </v-form>
        <!-- <div class="loader" v-if="loading == true"></div> -->
            <div class="vld-parent">
                <loading :active.sync="isLoading" 
                :can-cancel="true" 
                :on-cancel="onCancel"
                :is-full-page="fullPage"></loading>
            </div>
        </div>
    </div>
</template>

<script>
    // Import component
    import Loading from 'vue-loading-overlay';
    // Import stylesheet
    import 'vue-loading-overlay/dist/vue-loading.css';
    export default {
        
        data: () => ({
            items: [],
            breadcrumbs: [
                { text: 'Dashboard', disabled: false }, 
                { text: 'Billing Center', disabled: false }, 
                { text: 'Create CAL', disabled: true } 
            ],
            start_info_menu: [],
            start_menu:false,
            end_info_menu: [],
            end_menu:false,
            cal_date_menu: [],
            cal_date:false,
            cal_date_disabled:true,
            due_date_menu: [],
            due_date:false,
            filters:{},
            payment_mode:[],
            payment_term:[],
            company:[],
            items:{},
            requiredRules: [
                    v => !!v || 'Input is required',
                ],
            loading: false,
            submit_action:[
                {text:'Save and New', value:0},
                {text:'Save and Close', value:1},
            ],
            isLoading: false,
            fullPage: true,
            select_deployment: []
        }),
        components: {
            Loading
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
                // console.log(this.items.payment_term)
                // console.log({event})
                // this.item.cal_date
                this.cal_date_disabled  = false;
                let cal_date            = this.items.cal_date
                let newCalDate          = new Date(cal_date.substr(0,4), cal_date.substr(5,2) ,cal_date.substr(7,2))
                let date                = newCalDate;
                let addDay              = +cal_date.substr(8,2) + event.number + 1;
                let newDate             = new Date(date.getFullYear(),date.getMonth(),addDay).toISOString().substr(0,10);
                this.items.due_date     = newDate;
                // console.log({day,addDay,newDate,newCalDate})
            },
            start_info_save(date) {
                this.$refs.start_info_menu.save(date)
            },
            end_info_save(date) {
                this.$refs.end_info_menu.save(date)
            },
            cal_date_save(date) {
                this.$refs.cal_date_menu.save(date)
                // console.log(this.items.payment_term)
                this.addToCALDate(this.items.payment_term);
            },
            due_date_save(date) {
                this.$refs.cal_date_menu.save(date)
            },
            save(type) {
                this.isLoading = true;
                var s_date = new Date(this.items.start_date);
                var e_date = new Date(this.items.end_date);
                
                if(this.$refs.form_submit.validate())
                {
                    console.log(this.items,type)
                    if(s_date > e_date){
                        var alert = 'Start date not be greater than end date.';
                        this.toaster(alert)
                    }
                    else
                    {
                        this.loading = true;
                        this.items.payment_term_id = this.items.payment_term.id;
                        axios.post('/api/cal', this.items)
                        .then(response =>
                        {
                            EventBus.$emit('createBilling', response)
                            type == 0 ? this.$refs.form_submit.reset() : this.redirectIfNotExisting();
                            this.toaster(response.data, response.status)
                        })
                        .catch(errors =>
                        {
                            this.toaster(errors.response.data, errors.response.status)
                        })
                        .finally(()=>{
                            this.loading = false;
                            this.isLoading = false
                        });
                    }
                }
            },
            redirectIfNotExisting()
            {
                this.$router.push('/billing-&-collection-center')
            },
            reset(){
                this.$refs.form_submit.reset();
            }, 
            doAjax() {
                this.isLoading = true;
                // simulate AJAX
                setTimeout(() => {
                  this.isLoading = false
                },5000)
            },
            onCancel() {
              console.log('User cancelled the loader.')
            }
        },
        mounted() {
            this.items.cal_date = new Date().toISOString().substr(0,10)
        },
        async created()
        {
            this.select_deployment = await this.getAllCompanyDeployment();
            console.log(this.select_deployment);
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .create-collection__plan {
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

            .resize-action-submit{
                width: 15em;
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
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            overflow: visible;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
             position: fixed;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
            z-index: 999999999999;
            background: rgba(0,0,0,0.2);
            }

            /* Safari */
            @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    }

    
</style>