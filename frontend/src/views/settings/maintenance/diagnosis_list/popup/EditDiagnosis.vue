<template>
    <span>
        <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="popup_diagnosis" scrollable max-width="600px">
            <v-card class="view-diagnosis">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Update New Diagonis/Illness</v-card-title>
                <v-form ref="form_submit"  @submit.prevent="save" lazy-validation>
                    <v-card-text>
                        <div class="main-holder">
                            <input type="submit" ref="submit_button" class="hide-submit">
                            <!-- <b-form-group> -->
                                <v-checkbox v-model="is_sub_diagnosis" name="is_sub_diagnosis" label="Is Sub Diagnosis/Illness of"></v-checkbox><br> 
                                <!-- <b-form-checkbox  v-model="is_sub_diagnosis" name="is_sub_diagnosis">Is Sub Diagnosis/Illness of</b-form-checkbox><br> -->
                                <v-select v-if="is_sub_diagnosis"
                                    :disabled="!is_sub_diagnosis"
                                    v-model="item.parent_id" 
                                    color="accent" 
                                    :items="parent_diagnosis"
                                    item-text="name"
                                    item-value="id"
                                    label="Select Parent Diagnosis" 
                                    solo flat></v-select>
                            <!-- </b-form-group> -->
                            <v-text-field v-model="item.name" color="accent" label="Name" outline></v-text-field>
                            <v-textarea v-model="item.description" color="accent" label="Description" solo flat></v-textarea>
                            <v-layout>
                                <v-checkbox v-model="covered_first_year" name="covered_first_year" label="Covered First Year"></v-checkbox>
                                <v-checkbox v-model="covered_after_year" name="covered_after_year" label="Covered After a Year"></v-checkbox>
                                <v-checkbox v-model="exclusion" name="exclusion" label="Exclusion"></v-checkbox>
                            </v-layout>
                            <!-- <b-form-group v-model="row" class="display-block" row>
                                <b-form-checkbox v-model="covered_first_year" name="covered_first_year">Covered First Year</b-form-checkbox>
                                <b-form-checkbox v-model="covered_after_year" name="covered_after_year">Covered After a Year</b-form-checkbox>
                                <b-form-checkbox v-model="exclusion" name="exclusion">Exclusion</b-form-checkbox>
                            </b-form-group> -->
                        </div>
                    </v-card-text>
                </v-form>
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" @click.native="submit" :loading="loading" depressed>Save</v-btn>
                    <!-- <v-btn color="accent" depressed type="submit" ref="submit_button">SAVE</v-btn> -->
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>


<script>
    export default {
       data: () => ({
                popup_diagnosis: false,
                covered_first_year : false,
                covered_after_year : false,
                exclusion : false,
                is_sub_diagnosis : false,
                row : true,
                parent_diagnosis : [],
                filters: {},
                loading: false,
                list_parent: [],
                loading:false
            }),
        props : {
            item:{
                type: Object,
            },
            eventName: {
                type: String,
                default: 'editDiagnosis'
            }
        },
        methods : {
            save(event) {
                this.loading = true
                this.item.covered_first_year = this.covered_first_year
                this.item.covered_after_year = this.covered_after_year
                this.item.exclusion = this.exclusion
                this.item.is_sub_diagnosis = this.is_sub_diagnosis

                if(this.item.parent_id != null && this.is_sub_diagnosis == true){
                    this.list_parent.push(this.item.parent_id)
                }
                this.item.list_parent = this.list_parent;

                if(this.$refs.form_submit.validate())
                {
                    axios.put(`/api/diagnosis_list/${this.item.id}`, this.item)
                    .then(response => {
                        EventBus.$emit(this.eventName, response)
                        this.toaster(response.data, response.status)
                        this.close()
                    })
                    .catch(error => {
                        this.toaster(error.response.data, error.status)
                    })
                    .finally(() => {
                        this.loading = false
                    })
                }
                else
                {
                    this.loading = false
                }
            },
            close(){
                EventBus.$emit(this.eventName)
                this.popup_diagnosis = false
            },
            loadDialog(){

                this.popup_diagnosis = true
                this.getParent()
                this.list_parent = []
                if(this.item.parent_id){
                    this.list_parent.push(this.item.parent_id)
                }else{
                    this.list_parent.push('is_null')
                }
                this.isSubDiagnosis()

                
            },
            isSubDiagnosis(){
                this.covered_first_year = this.item.covered_first_year == 1 ? true : false
                this.covered_after_year = this.item.covered_after_year == 1 ? true : false
                this.exclusion = this.item.exclusion == 1 ? true : false
                if(this.item.parent_id)
                {
                    this.is_sub_diagnosis = true;
                }
                else
                {
                    this.is_sub_diagnosis = false;
                }
            },
            getParent(filters = 0){
                this.filters.id = this.item.id
                this.filters.is_archived = 0
                this.filters.parent_id = this.item.parent_id
                axios.post('api/diagnosis_list/select'+this.generateFilterURL(this.filters))
                .then(response => {
                    this.parent_diagnosis = response.data.data.data
                    
                })
                .catch(error => {

                })
                .finally(() => {

                })
            },
            submit() {
                this.$refs.submit_button.click()
            },
        }
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .edit-diagnosis {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .main-holder {
            border: 1px solid #ededed;
            padding: 40px;

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
            .v-btn
            {
                margin: 0px;
            }
        }
    }
</style>