<template>
    <span>
        <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="1000px" lazy>
            <v-card class="edit-company">
                <v-card-title class="title" style="background-color: #303E55; color: #fff;">View Company</v-card-title>
                <v-card-text>
                    <v-form ref="form"  @submit.prevent="save">
                        <div class="top-holder">
                            <div class="top-holder__others">
                                <v-text-field color="accent" label="Company Name" v-model="form.name" :rules="requiredRules" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Email Address" v-model="form.email" :rules="requiredRules" outline readonly></v-text-field>
                            </div>
                            <div class="top-holder__others_extra">
                                <v-text-field color="accent" label="Company Code" v-model="form.code" :rules="requiredRules" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Tel / Mobile Number" v-model="form.contact_number" :rules="requiredRules" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Account Type" v-model="form.account_type" :rules="requiredRules" outline readonly></v-text-field>
                            </div>
                            <v-textarea color="accent" label="Company Address" v-model="form.address" :rules="requiredRules" outline readonly></v-textarea>
                            <div class="top-holder__others">
                                <v-text-field color="accent" label="Policy Effective Date" v-model="form.policy_effective_date" :rules="requiredRules" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Policy Expiry Date" v-model="form.policy_expiry_date" :rules="requiredRules" outline readonly></v-text-field>
                            </div>
                            <div class="subheading font-weight-medium mb-2">Contact Person</div>
                            <div class="top-holder__contact">
                                <v-text-field color="accent" label="First Name" v-model="contact_person[0].first_name" :rules="requiredRules" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Middle Name" v-model="contact_person[0].middle_name" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Last Name" v-model="contact_person[0].last_name" :rules="requiredRules" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Position" v-model="contact_person[0].position" :rules="requiredRules" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Tel. / Mobile Number" v-model="contact_person[0].contact_number" :rules="requiredRules" outline readonly></v-text-field>
                                <v-text-field color="accent" label="First Name" v-model="contact_person[1].first_name" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Middle Name" v-model="contact_person[1].middle_name" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Last Name" v-model="contact_person[1].last_name" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Position" v-model="contact_person[1].position" outline readonly></v-text-field>
                                <v-text-field color="accent" label="Tel. / Mobile Number" v-model="contact_person[1].contact_number" outline readonly></v-text-field>
                            </div>
                        </div>
                        <div class="bottom-holder">
                            <v-tabs dark slot="extension" color="primary" grow>
                                <v-tabs-slider color="#DF7E00"></v-tabs-slider>
                                <v-tab v-for="item in procedure_items" :key="item.TabTitle">
                                    {{ item.TabTitle }}
                                </v-tab>
                                <v-tab-item>
                                    <v-card flat>
                                        <v-card-text>
                                            <div v-for="(file_item, key) in files_attached" :key="'files-'+key">
                                                <span>
                                                    <a :href="file_item.source" target="_blank">{{file_item.name}}</a> 
                                                </span>
                                            </div>
                                            <div class="select-coverage" v-for="(coverage_plan_item,key) in coverage" :key="key">
                                                <v-autocomplete 
                                                    v-model="coverage[key].coverage_plan_id" 
                                                    color="accent" 
                                                    :items="coverageList"
                                                    item-text="name"
                                                    item-value="id"
                                                    label="Select Coverage Plan"
                                                    :rules="requiredRules" 
                                                    flat
                                                    outline
                                                    disabled
                                                    @change="limitPerFilter(coverage,key,coverage[key].coverage_plan_id)">
                                                </v-autocomplete>
                                                <!-- <v-icon medium class="mr-2" color="green" @click="listIndex(coverage,key,'add')" v-if="key + 1 == coverage.length"> add </v-icon> -->
                                                <!-- <v-icon medium class="mr-2" color="red" @click="listIndex(coverage,key,'remove','coverage')" v-if="key"> close </v-icon> -->
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-tab-item>
                                <v-tab-item>
                                    <v-card flat>
                                        <v-card-text>
                                            <div class="select-coverage" v-for="(deploymentItem,key) in deployment" :key="key">
                                                <v-text-field color="accent" label="Deployment" v-model="deployment[key].name" :rules="requiredRules" outline readonly></v-text-field>
                                                <!-- <v-icon medium class="mr-2" color="green" @click="listIndex(deployment,key,'add')" v-if="key + 1 == deployment.length"> add </v-icon> -->
                                                <!-- <v-icon medium class="mr-2" color="red" @click="listIndex(deployment,key,'remove','deployment')" v-if="key"> close </v-icon> -->
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-tab-item>
                            </v-tabs>
                        </div>
                        <input type="submit" ref="submit_button" class="hide-submit">
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <!-- <v-btn color="accent" depressed @click.native="submit" :loading="loading">UPDATE</v-btn> -->
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default {
        data : () => ({
            dialog: false,
            contact_person:[
                {first_name: null, middle_name: null, last_name: null, position:null, contact_number: null},
                {first_name: null, middle_name: null, last_name: null, position:null, contact_number: null},
            ],
            // contact_person:[],
            requiredRules: [
                v => !!v || 'Input is required',
            ],
            procedure_items: [
                {TabTitle: 'Coverage Plan'}, 
                {TabTitle: 'Deployment'}
            ],
            deployment:[{name: null}],
            coverage:[{id: null}],
            coverageList: [],
            files:[],
            files_attached:[],
            files_deleted:[],
            loading: false,
            selectOptions:[],
        }),
        props : {
            form:{
                type: Object
            },
            eventName : {
                type: String,
                default : 'editCompany'
            },
        },
        methods: {
            removeFile(key){
                this.files_deleted.push(this.files_attached[key].id);
                this.files_attached.splice(key,1)
            },
            setData(companyData){
                this.coverage = companyData.company_coverage_plan;

                this.deployment = companyData.company_deployment;

                this.contact_person = companyData.company_contact_person;
                if(this.contact_person.length <= 1)
                {
                    this.contact_person.push({first_name: null, middle_name: null, last_name: null, position:null, contact_number: null})
                }

                this.coverageList = [];
                this.coverageList = [...this.selectOptions];
           
                companyData.company_coverage_plan.forEach((data,key)=>{
                    this.coverageList = [...this.coverageList,data.coverage_plan];
                });

                this.files_attached = companyData.files;
            },
            async getDataOptions() {
                const coveragePlanAPI =  axios.get('/api/select/coverage-plan');

                const [coverage_plan] = await Promise.all([coveragePlanAPI])

                const { data : coveragePlanData } = coverage_plan;

                let filteredCoveragePlan = coveragePlanData.filter(coverage => coverage.is_used_in_company == false);

                this.selectOptions = filteredCoveragePlan;
            },
            async loadDialog(){
                const { data: companyData } = await axios.get('/api/show/company/'+this.form.id);

                console.log({companyData});
                
                await this.getDataOptions();

                await this.setData(companyData);


                this.dialog = await true;
            },
            checkDuplicateInObject(propertyName, inputArray) {
                var seenDuplicate = false,
                    testObject = {};

                inputArray.map(function(item) {
                    var itemPropertyName = item[propertyName];   
                    if (itemPropertyName in testObject) {
                        testObject[itemPropertyName].duplicate = true;
                        item.duplicate = true;
                        seenDuplicate = true;
                    }
                    else {
                        testObject[itemPropertyName] = item;
                        delete item.duplicate;
                    }
                });
                return seenDuplicate;
            },
            limitPerFilter(baseArray,key,id){
                var filteredArray = baseArray
                var list = filteredArray.filter(filtered => filtered.coverage_plan_id == id)
                if(list.length > 1)
                {
                    this.toaster('This coverage plan is already selected',400)
                    this.coverage.splice(key,1)
                }
            },
            listIndex(arrayAffected,index, method,type)
            {
                var limit = 1
                if(method == 'add')
                {
                    var data = {name: ''}
                    arrayAffected.push(data)
                }
                else
                {
                    if(type == 'deployment'){
                        var size = Object.keys(arrayAffected[index]).length;
                        if(size > 1)
                        {
                            axios.patch(`/api/company_deployment/${arrayAffected[index]['id']}`, arrayAffected[index])
                            .then(response => {
                                arrayAffected.splice(index, 1);
                            })
                            .catch(error => {
                                this.toaster('You cannot remove this because it is tag to other data.', 300)
                            })
                            .finally(() => {
                                this.loading = false
                            })
                        }else{
                            arrayAffected.splice(index, 1);    
                        }
                     }else{
                        if(arrayAffected.length > limit)
                        {
                            arrayAffected.splice(index, 1);
                        }
                        else
                        {
                        this.toaster(`Cannot be less than ${limit}.`,400)
                        }
                     }
                }
            },
            submit() {
                this.$refs.submit_button.click()
            },
            removeDuplicates(myArr, prop) {
                return myArr.filter((obj, pos, arr) => {
                    return arr.map(mapObj => mapObj[prop]).indexOf(obj[prop]) === pos;
                });
            },
            save(){
                if(this.$refs.form.validate())
                {
                    this.loading = true;
                    const formData = new FormData();

                    formData.append('folder', 'App\\Models\\Company')
                    formData.append('contact_person',JSON.stringify(this.contact_person))
                    formData.append('coverage', JSON.stringify(this.removeDuplicates(this.coverage,'coverage_plan_id')))
                    formData.append('deployment', JSON.stringify(this.deployment))
                    formData.append('form', JSON.stringify(this.form))
                    formData.append('files_delete', JSON.stringify(this.files_deleted))

                    if(this.files.length != 0)
                    {
                        for (var i = 0; i < this.files.length; i++) 
                        {
                            formData.append('files[]', this.files[i], this.files[i].name);
                            formData.append('extensions[]', this.files[i].extension);
                            formData.append('sizes[]', this.files[i].size);
                            formData.append('names[]', this.files[i].name);
                        }
                    }
                    else
                    {
                        formData.append('files', 'no_file');
                    }
                    var checkDup = this.checkDuplicateInObject('name', this.deployment)
                    if(!checkDup)
                    {
                        axios.post('api/company/'+this.form.id,formData)
                        .then(response=>{
                            this.toaster(response.data,response.status)
                            EventBus.$emit(this.eventName, response)
                            this.close();
                        })
                        .catch(error => {
                            this.toaster(error.response.data,error.response.status)
                            EventBus.$emit(this.eventName, error)
                        })
                        .finally(()=>{
                            this.loading = false;
                        })
                    }
                    else
                    {
                        this.toaster('Deployment have duplicate name',400)
                    }
                }
                else
                {
                    this.toaster(`Fill up all fields.`,400)
                }
                
            },
            close(){
                EventBus.$emit(this.eventName, '')
                this.dialog = false
            },
        },
        created(){
                EventBus.$on('setUploadData',(data)=>{
                    this.files = data;
                });
            }
    }
</script>


<style lang="scss">
    @import "../../../scss/app.scss";

    .edit-company {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .top-holder {
            border: 1px solid #ededed;
            padding: 15px 15px 0px;
            margin-bottom: 10px;

            .top-holder__others {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
            }

            .top-holder__others_extra {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                grid-column-gap: 50px;
            }

            .top-holder__contact {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                grid-column-gap: 10px;
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

            .v-card {
                border: 1px solid #ededed;
            }

            .upload-file {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 40px;
                grid-row-gap: 20px;
            }

            .select-coverage {
                display: flex;
            }

            .v-text-field {
                font-size: 15px;
                padding-right: 10px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }
        }
    }
</style>