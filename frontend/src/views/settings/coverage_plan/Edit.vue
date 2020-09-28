<template>
    <div class="edit-coverage__plan">
        <v-dialog
            v-model="loading"
            hide-overlay
            persistent
            width="300"
            >
            <v-card
                color="primary"
                dark
            >
                <v-card-text>
                Loading data...
                <v-progress-linear
                    indeterminate
                    color="white"
                    class="mb-0"
                ></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="top-holder">
            <span></span>
            <div class="pull-right">
                <b-dropdown id="ddown1" size="lg" right text="Actions" class="mb-2 pull-right ml-auto float-right">
                    <b-dropdown-item @click="save('update')">Update Coverage Plan</b-dropdown-item>
                    <b-dropdown-item @click="save('update&close')">Update Coverage Plan & Close</b-dropdown-item>
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
                    <v-text-field type="number" v-model="form.age_bracket_from" @change="validateAgeBracket()" color="accent" label="Age Bracket - From" outline></v-text-field>
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
                :default="form.pre_existing_id"
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
            <v-radio-group  v-model="form.max_limit_per" row>
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
                    <coverage-select-procedure :item="type" :benefit_index="index" :form="type.coverage_plan_benefit" :for_MBL="form.max_limit_per -1"/>
                    <span class="caption ml-2">{{ type.item_count }} Items Selected</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => ({
            loading: false,
            form : {
                age_bracket_from : 1,
                age_bracket_to : 0,
                age_bracket : [],
                coverage_plan_benefits: null
            },
            pre_existing: [],
            breadcrumbs: [
                { text: 'Settings', disabled: false }, 
                { text: 'Coverage Plan', disabled: false }, 
                { text: 'Edit', disabled: true } 
            ],
        }),
        methods : {
            clearData() {
                for(let i = 0; i < this.benefits.length; i++)
                {
                    this.benefits[i].item_count = 0
                }
            },
            showData() {
                this.loading = true
                this.clearData()
                axios.get(`/api/coverage_plan/${this.$route.query.id}`)
                .then(response=> {
                    this.form = response.data
                    console.log(this.form,'form')
     
                    if(typeof this.form != "object")
                    {
                        this.redirectIfNotExisting()
                    }
                    else
                    {
                        this.getCoveragePlanBenefits(this.form.coverage_plan_benefits)
                    }
                    this.loading = false
                })
                .catch(error => {
                    console.log(error)
                })
            },
            getCoveragePlanBenefits(benefits) {
                this.benefits.forEach((data, key) => {
                    benefits.forEach((d, k) => {
                        if(d.benefit_type_id == data.id)
                        {
                            this.benefits[key].item_count               = d.item_count
                            this.benefits[key].coverage_plan_benefit    = d
                        }
                    })
                })
            },
            getPreExistings() {
                axios.get('/api/pre-existing')
                .then(response=> {
                    this.pre_existing = response.data.data.data
                })
            },
            save(action) {
                
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

                if(!this.form.coverage_plan_benefits.length || !this.form.coverage_plan_benefits[0].item_count)
                {
                    this.toaster("Please select atleast one benefits and procedures.", 300)
                    return false
                }

                this.validateAgeBracket()
                console.log(this.form)
                axios.put(`/api/coverage_plan/${this.form.id}`, this.form)
                .then(response => {
                    this.toaster(response.data, response.status)
                    if(action != "update")
                    {
                        window.location.href = "/settings/coverage-plan"
                    }
                    else
                    {
                        window.location.reload()
                    }
                })
                .catch(error => {
                    this.toaster(error.response.data, error.status)
                })
                .finally(() => {
                    this.loading = false
                })
            },
            validateAgeBracket(){
                this.form.age_bracket = []
                if(this.form.age_bracket_from >= this.form.age_bracket_to)
                {
                    this.form.age_bracket_to = parseFloat(this.form.age_bracket_from ) + 1
                }
                this.form.age_bracket[0] = this.form.age_bracket_from
                this.form.age_bracket[1] = this.form.age_bracket_to
            },
            redirectIfNotExisting()
            {
                this.$router.push('/settings/coverage-plan')
            },
            loadEventBuses(){
                EventBus.$on('selectProcedures', (data) => {
                    this.benefits[data.benefit_index].item_count = data.item_count
                    this.form.coverage_plan_benefits[data.benefit_index] = data
                })
            },
            // getEmitData(data){
            //     console.log({data},'asfa')
            // }
        },
        mounted() {
            // this.$nextTick(()=>{
                if(this.$route.query.id)
                {
                    this.showData();
                    // this.getPreExistings()
                    this.validateAgeBracket()
                    this.loadEventBuses()
        
                }
                else
                {
                    this.redirectIfNotExisting()
                }
            // })
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .edit-coverage__plan {
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