<template>
    <span>
       <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="700px">
            <v-card class="edit-provider">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Edit Provider</v-card-title>
                <v-card-text>
                    <v-form ref="form"  @submit.prevent="save">
                        <div class="main-holder">
                            <div class="edit-provider__holder">
                                <v-text-field color="accent" v-model="form.name" label="Provider Name" :rules="requiredRules" outline></v-text-field>
                                <v-select color="accent" v-model="form.rate_rvs" :items="provider_rate" :rules="requiredRules" label="Select Rate/RVS" outline></v-select>
                                <v-text-field color="accent" v-model="form.contact_person" label="Contact Person" outline></v-text-field>
                                <v-text-field color="accent" v-model="form.tel_number" label="Tel. No." outline></v-text-field>
                                <v-text-field color="accent" v-model="form.contact_number" label="Mobile Number" outline></v-text-field>
                                <v-text-field color="accent" v-model="form.email" label="Email Address" outline></v-text-field>
                            </div>
                            <v-textarea outline name="input-7-4" v-model="form.address" label="Address"></v-textarea>
                            <div v-for="(listItem,key) in list" :key="key">
                                <v-layout row wrap>
                                    <v-flex xs12 sm12 md12>
                                        <v-flex xs10 sm10 md10 class="col-six-l" d-flex>
                                            <v-text-field color="accent" label="Payee" v-model="list[key].name" :rules="requiredRules" outline></v-text-field>
                                        </v-flex>
                                        <v-flex xs2 sm2 md2 class="col-six-r" d-flex>
                                            <v-icon medium class="mr-2" @click="listIndex(key, 'add')" color="green"> add </v-icon>
                                            <v-icon medium class="mr-2" @click="listIndex(key, 'remove')" color="red"> close </v-icon>
                                        </v-flex>
                                    </v-flex>
                                </v-layout>
                            </div>
                            <!-- <v-data-table
                                    v-model="selected"
                                    :headers="headers"
                                    :items="list"
                                    class="elevation-1"
                                    :pagination.sync="tablePagination"
                                >
                                    <template slot="items" slot-scope="props">
                                        <td>{{props.index + 1}}</td>
                                        <td>
                                            <v-text-field v-model="list[props.index].name" class="pa-1 mt-1" :rules="requiredRules"></v-text-field>
                                        </td>
                                        <td class="justify-center">
                                            <v-icon medium class="mr-2" @click="listIndex(props.index, 'add')" color="green" v-if="props.index + 1 == list.length"> add
                                            <v-icon medium class="mr-2" @click="listIndex(props.index, 'remove')" color="red" v-if="props.index"> close </v-icon>
                                        </td>
                                    </template>
                            </v-data-table> -->
                        </div>
                        <input type="submit" ref="submit_button" class="hide-submit">
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
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
            provider_rate: ['2001','2009'],
            requiredRules: [
                v => !!v || 'Input is required',
            ],
            headers: [
                { text: '', value: 'number', sortable: false, width:'1%' },
                { text: 'Payee Name', value: 'payee', sortable: false, width:'90%' } ,
                { text: '', value: 'action', sortable: false, width:'9%' },
            ],
            selected:[],
            list: [
                {name: ''}
            ],
            tablePagination:{
                rowsPerPage: -1,
            },
            loading: false,
        }),
        props : {
            form:{
                type: Object
            },
            eventName : {
                type: String,
                default : 'editProvider'
            }
        },
        methods : {
            async loadDialog(){
                let get_provider_data = await axios.get('/api/provider/'+this.form.id);

                const {data: provider_data} = get_provider_data;

                this.list = provider_data.provider_payee;

                this.dialog = await true;
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
                    this.form.list = this.list
                    var checkDup = this.checkDuplicateInObject('name', this.list)
                    if(!checkDup)
                    {
                        axios.put('api/provider/'+this.form.id,this.form)
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
                        this.toaster('Payee have duplicate name.',400)
                    }
                    
                }
                else
                {
                    this.toaster('Fill up all fields.',400)
                }
                
            },
            clear(){
                this.$refs.form.reset();
            },
            close(){

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

    .edit-provider {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .v-text-field {
            font-size: 15px;

            .v-input__slot {
                border: 1px solid #919191 !important;
            }
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

        .main-holder {
            border: 1px solid #ededed;
            padding: 15px 15px 0px;
        }

        .edit-provider__holder {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 50px;



            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            .btn-holder {
                text-align: center;
            }
        }
    }
</style>