<template>
<span>
    <span @click="loadDialog">
            <slot></slot>
    </span>
    <v-dialog v-model="dialog" persistent scrollable max-width="700px">
        <v-card class="add-doctor">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Edit Doctor</v-card-title>
            <v-card-text>
                <v-form ref="form" @submit.prevent="save">
                <div class="main-holder">
                    <div class="add-doctor__holder">
                        <v-text-field color="accent" v-model="form.first_name" :rules="requiredRules" label="First Name" outline></v-text-field>
                        <v-text-field color="accent" v-model="form.middle_name" label="Middle Name" outline></v-text-field>
                        <v-text-field color="accent" v-model="form.last_name" :rules="requiredRules" label="Last Name" outline></v-text-field>
                    </div>
                    <div class="add-doctor___detailholder">
                            <v-text-field color="accent" v-model="form.contact_number" label="Contact Number" outline></v-text-field>
                            <v-text-field color="accent" v-model="form.email" label="Email Address" outline></v-text-field>
                    </div>
                    <div class="add-doctor___detailholder">
                        <v-text-field color="accent" v-model="form.tin" label="Taxpayer Identification Number" outline></v-text-field>
                        <v-select color="accent" v-model="form.tax" label="Vatable or Non-vatable" :items="taxable" :rules="requiredRules" outline></v-select>
                    </div>
                    <div v-for="(listItem,key) in list" :key="key">
                    <v-layout row wrap>
                        <v-flex >
                            <v-flex class="col-six-l" d-flex>
                                <global-select-box
                                    v-model="list[key].provider_id" 
                                    :default="list[key].provider_id"
                                    :rules="requiredRules" 
                                    :is-load="dialog"
                                    :filters="{is_archived:0}"
                                    api-link="api/provider"
                                    label="Select Network Provider" 
                                    item-text="name" 
                                    item-value="id" 
                                    color="accent" 
                                    outline
                                >
                                </global-select-box>
                            </v-flex>
                            
                            <v-flex xs2 sm2 md2 class="col-six-r" d-flex>
                                <v-icon medium class="mr-2" @click="listIndex(key, 'add')" color="green"> add </v-icon>
                                <v-icon medium class="mr-2" @click="listIndex(key, 'remove')" color="red"> close </v-icon>
                            </v-flex>
                        </v-flex>
                    </v-layout>
                </div>
                </div>
                <input type="submit" ref="submit_button" class="hide-submit"> 
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="tertiary" depressed dark @click="dialog = false">CANCEL</v-btn>
                <v-btn color="accent" depressed @click.native="submit" :loading="loading">UPDATE</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</span>
</template>

<script>
    export default {
        data : () => ({
            dialog: false,
            item: [],
            list: [],
            taxable: ['VATABLE', 'NON-VATABLE'],
            provider: [],
            requiredRules: [
                v => !!v || 'Input is required',
            ],
            loading: false,
        }),
        props : {
            eventName : {
                type : String,
                default : 'editDoctor'
            },
            form : {
                type : Object
            },
        },
        methods : {
            loadDialog() {
                this.dialog = true;
                this.provider = this.selectOptions
                this.list = this.form.doctor_provider
            },
            submit() {
                this.$refs.submit_button.click()
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
            save(){
                if(this.$refs.form.validate()){
                    this.loading = true;
                    var checkDup = this.checkDuplicateInObject('provider_id', this.list)
                    if(!checkDup)
                    {
                        this.form.list = this.list
                        axios.put('api/doctor/'+this.form.id,this.form)
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
                        this.loading = false;
                        this.toaster('Network Provider is already added in your list',400)
                    }
                }
                
            },
            close(){
                // this.$refs.form.reset();
                // this.list = new Array;
                this.dialog = false
            },
            listIndex(index, method, limit = 1)
            {
                if(method == 'add')
                {
                    var data = {name: ''}
                    this.list.push(data)
                }
                else
                {
                    if(this.list.length > limit)
                    {
                        this.list.splice(index, 1);
                    }
                    else
                    {
                        this.toaster(`Cannot be less than ${limit}.`,400)
                    }
                }
            },
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .col-six-l
    {
        width: 80%;
        float : left;
        cursor : pointer;
        position : relative;
        padding : 5px;
        left: 12px;
    }
    .col-six-r
    {
        width: 20%;
        float : right;
        cursor : pointer;
        position : relative;
        padding : 5px;
        top : 20px;
    }
    .add-doctor {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

        .main-holder
        {
            padding: 30px 30px 0px;
            border: 1px solid #ededed;
        }

        .add-doctor__holder {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-column-gap: 30px;

            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            .btn-holder
            {
                text-align: center;
            }
        }
        
        .add-doctor___detailholder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
        }
    }
</style>