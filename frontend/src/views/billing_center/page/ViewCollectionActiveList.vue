<template>
    <div class="view-collection__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="top-holder-one">
            <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
             <div class="top-holder-two">
                <v-text-field slot="activator" v-model="items.reference_number" label="Reference Number" append-icon="reorder" readonly outline></v-text-field>
                <div class="pull-right">
                    <v-btn color="accent" depressed type="submit" ref="submit_button"  class="mb-2 pull-right float-right">Close</v-btn>
                </div>
            </div>
            <v-autocomplete readonly :items="company" v-model="items.company_id" color="accent" item-value="id" item-text="name" label="Select Company" outline></v-autocomplete>
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
                    readonly
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
                    readonly
                >
                </global-select-box>
                <date-format readonly v-model="items.payment_cal_date" color="accent" label="Cal Date" outline></date-format>
                <date-format readonly v-model="items.payment_due_date" color="accent" label="Due Date" outline></date-format>
                <date-format readonly v-model="items.payment_start_date" color="accent" label="Payment Start" outline></date-format>
                <date-format readonly v-model="items.payment_end_date" color="accent" label="Payment End" outline></date-format>
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
        components:{
            paidCountList,
            CalMemberTable
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
                { text: 'View CAL', disabled: true } 
            ],
            payment_term: [],
            start_info_menu: [],
            start_menu:false,
            end_info_menu: [],
            end_menu:false,
            payment_mode:[],
            company:[],
            filters:{},
            requiredRules: [
                v => !!v || 'Input is required',
            ],

        }),
        methods : {
            async showData() {
                this.loading = true;

                const {data : getCAL} = await axios.get(`/api/cal/${this.$route.query.id}`);

                this.items = await getCAL;

                this.loading = await false;

                // this.loading = true
                // //this.clearData()
                // axios.get(`/api/cal/${this.$route.query.id}`)
                // .then(response=> {
                //     this.items = response.data
                //     console.log(this.items)
                //     this.loading = false
                // })
                // .catch(error => {
                //     console.log(error)
                // })
            },
            getPaymentTerm() {
                for ( let i = 0; i <= 31; i++)
                {
                    if(i != 0){
                        this.payment_term.push({days: i, text: i + (i == 1 ? ' day' : ' days')});
                    }
                }
            },
            start_info_save(date) {
                this.$refs.start_info_menu.save(date)
            },
            end_info_save(date) {
                this.$refs.end_info_menu.save(date)
            },
            axiosGet(){
                this.filters.is_archived = 0
                this.getPaymentMode(this.filters)
                this.getCompany(this.filters)
                this.getPaymentTerm()
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
            save() {
               this.redirectIfNotExisting()
            },
            redirectIfNotExisting()
            {
                this.$router.push('/billing-&-collection-center')
            },
            async showAll(){
                await this.showData();
                await this.axiosGet();
            },
        },
        mounted() {
           if(this.$route.query.id)
            {
                this.showAll()
            }
            else
            {
                this.redirectIfNotExisting()
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .view-collection__plan {
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