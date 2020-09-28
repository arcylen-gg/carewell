<template>
    <div class="create-coverage__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="top-holder">
            <span></span>
            <div class="pull-right">
                <b-dropdown id="ddown1" size="lg" right text="Actions" class="mb-2 pull-right ml-auto float-right">
                    <b-dropdown-item @click="save('save&close')">Save & Close</b-dropdown-item>
                    <b-dropdown-item @click="save('save&new')">Save & New</b-dropdown-item>
                </b-dropdown>
            </div>

            <v-text-field v-model="form.name" color="accent" label="Plan Name" outline></v-text-field>
            <currency-format 
                v-model="form.monthly_premium" 
                color="accent" 
                label="Monthly Premium"
                outline 
                reverse
            >
            </currency-format>
            <v-layout class="my-0 py-0">
                <v-flex md-5 py-0>
                    <v-text-field type="number" v-model="form.age_bracket_from" :value="form.age_bracket_from" @change="validateAgeBracket()" color="accent" label="Age Bracket - From" outline></v-text-field>
                </v-flex>
                <v-flex md-2 py-0 class="align-center justify-center layout">
                    <div class="title pb-3">-</div>
                </v-flex>
                <v-flex md-5 py-0>
                    <v-text-field type="number" v-model="form.age_bracket_to" @change="validateAgeBracket()" color="accent" label="Age Bracket - To" outline></v-text-field>
                </v-flex>
            </v-layout>
            <currency-format 
                v-model="form.handling_fee" 
                color="accent" 
                label="Handling Fee"
                outline 
                reverse
            >
            </currency-format>
            <currency-format 
                v-model="form.processing_fee" 
                color="accent" 
                label="Processing Fee"
                outline 
                reverse
            >
            </currency-format>
            <currency-format 
                v-model="form.card_fee" 
                color="accent" 
                label="Card Fee"
                outline 
                reverse
            >
            </currency-format>
            <currency-format 
                v-model="form.hospital_income_benefit" 
                color="accent" 
                label="Hospital Income Benefits (HIB)"
                outline 
                reverse
            >
            </currency-format>

            <global-select-box
                v-model="form.pre_existing_id"
                api-link="/api/pre-existing"
                item-value="id" 
                item-text="name" 
                label="Pre-Existing" 
                outline
                is-load
                :filters="{is_archived:0}"
            >
            </global-select-box>

            <!-- <v-autocomplete v-model="form.pre_existing_id" color="accent" :items="pre_existing" item-value="id" item-text="name" label="Pre-Existing" outline></v-autocomplete> -->
            
            <currency-format 
                v-model="form.annual_benefit_limit" 
                color="accent" 
                label="Annual Benefit Limit (ABL)"
                outline 
                reverse
            >
            </currency-format>
            <currency-format 
                v-model="form.maximum_benefit_limit" 
                color="accent" 
                label="Maximum Benefit Limit (MBL)"
                outline 
                reverse
            >
            </currency-format>
            <div></div>
            <v-radio-group v-model="form.max_limit_per" row>
                <v-radio label="Year" value="1"></v-radio>
                <v-radio label="Illness / Disease" value="2"></v-radio>
            </v-radio-group>
        </div>
        <div class="bottom-holder">
            <div class="title text-dark font-weight-bold mb-2">TYPE OF BENEFITS</div>
            <v-divider></v-divider>
            <div class="benefits-holder mt-3">
                <div v-for="(type, index) in benefits" :key="type.name" class="button-holder">
                    <div class="body-2 font-weight-medium">{{ type.name }}</div>
                    <coverage-select-procedure :item="type" :benefit_index="index" :for_MBL="form.max_limit_per -1"/>
                    <span class="caption ml-2">{{ type.item_count }} Items Selected</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => ({
            form : {
                age_bracket_from : 1,
                age_bracket_to : 0,
                age_bracket : [],
                coverage_plan_benefits: [],
                handling_fee: 0,
                max_limit_per: "1",
            },
            pre_existing: [],
            breadcrumbs: [
                { text: 'Settings', disabled: false }, 
                { text: 'Coverage Plan', disabled: false }, 
                { text: 'Create', disabled: true } 
            ],
        }),
        
        methods : {
            clearData() {
                for(let i = 0; i < this.benefits.length; i++)
                {
                    this.benefits[i].item_count = 0
                }
            },
            getPreExistings() {
                axios.get('/api/pre-existing')
                .then(response=> {
                    this.pre_existing = response.data.data.data
                })
            },
            save(type = 'save&close') {
                
                console.log(this.form)
                if(!this.form.name)
                {
                    this.toaster("Plan name is required.", 300)
                    return false
                }

                if(!this.form.monthly_premium)
                {
                    this.toaster("Monthly premium is required.", 300)
                    return false
                }
                if(!this.form.coverage_plan_benefits.length || (this.form.coverage_plan_benefits && this.form.coverage_plan_benefits[0] && !this.form.coverage_plan_benefits[0].item_count))
                {
                    this.toaster("Please select atleast one benefits and procedures.", 300)
                    return false
                }

                if(!this.form.maximum_benefit_limit)
                {
                    this.toaster("Maximum Benefit Limit is required.", 300)
                    return false
                }
                // this.form.monthly_premium = this.form.monthly_premium.replace(' ','').replace(',','').replace('₱','')
                // this.form.handling_fee = this.form.handling_fee.replace(',','').replace(' ','').replace('₱','')
                // this.form.processing_fee = this.form.processing_fee.replace(',','').replace(' ','').replace('₱','')
                // this.form.card_fee = this.form.card_fee.replace(',','').replace(' ','').replace('₱','')
                // this.form.hospital_income_benefit = this.form.hospital_income_benefit.replace(',','').replace(' ','').replace('₱','')
                // this.form.annual_benefit_limit = this.form.annual_benefit_limit.replace(',','').replace(' ','').replace('₱','')
                // this.form.maximum_benefit_limit =  this.form.maximum_benefit_limit.replace(',', '').replace(' ','').replace('₱','');

                console.log(this.form)
                axios.post('/api/coverage_plan', this.form)
                .then(response => {
                    if(type == 'save&close')
                    {
                        this.form = {}
                        for(let i = 0; i < this.benefits.length; i++)
                        {
                            this.benefits[i].item_count = 0
                        }
                        EventBus.$emit('clearProcedures')
                        this.toaster(response.data, response.status)
                        this.$router.push('/settings/coverage-plan')
                    }
                    else if(type == 'save&new')
                    {
                        this.form.name = null
                        this.toaster(response.data, response.status)
                    }
                    else
                    {
                        this.toaster("Hmmmmmmm?", 300)
                    }
                })
                .catch(error => {
                    this.toaster(error.response.data, error.response.status)
                })
            },
            validateAgeBracket(){
                if(this.form.age_bracket_from >= this.form.age_bracket_to)
                {
                    this.form.age_bracket_to = parseFloat(this.form.age_bracket_from ) + 1
                }
                this.form.age_bracket[0] = this.form.age_bracket_from
                this.form.age_bracket[1] = this.form.age_bracket_to
            },
            loadEventBuses(){
                EventBus.$off('selectProcedures');
                EventBus.$on('selectProcedures', (data) => {
                    this.benefits[data.benefit_index].item_count = data.item_count
                    this.form.coverage_plan_benefits[data.benefit_index] = data
                })
            },
            // isNumber: function(evt) {
            //   evt = (evt) ? evt : window.event;
            //   var charCode = (evt.which) ? evt.which : evt.keyCode;
            //   if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46 && charCode !== 44) {
            //     evt.preventDefault();;
            //   } else {
            //     return true;
            //   }
            // },
        },
        mounted() {
            this.clearData()
            // this.getPreExistings()
            this.validateAgeBracket()
            this.loadEventBuses()
            EventBus.$on('addCoveragePlanProcedures', () => {
                this.get_procedure_types()
            });
            
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .create-coverage__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .top-holder {
            border: 1px solid #ededed;
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

        .bottom-holder {
            padding: 15px;
            border: 1px solid #ededed;
            background-color: #ffffff;
            
            .benefits-holder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
                grid-row-gap: 15px;
            }
        }
    }

    
</style>