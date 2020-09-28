<template>
    <v-dialog v-model="popup_diagnosis" persistent scrollable max-width="600px">
        <v-btn  slot="activator" depressed color="accent"><i class="fas fa-plus mr-3"></i>Create New Diagnosis/Illness</v-btn>
        <v-card class="create-diagnosis">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Create New Diagnosis/Illness</v-card-title>
            <v-form ref="form_submit"  @submit.prevent="save" lazy-validation>
                <v-card-text>
                    <div class="main-holder">
                        <input type="submit" ref="submit_button" class="hide-submit">
                        <!-- <b-form-group> -->
                            <v-checkbox v-model="item.is_sub_diagnosis" name="is_sub_diagnosis" label="Is Sub Diagnosis/Illness of"></v-checkbox><br> 
                            <!-- <b-form-checkbox  v-model="item.is_sub_diagnosis" name="is_sub_diagnosis">Is Sub Diagnosis/Illness of</b-form-checkbox><br> -->
                            <v-select v-if="item.is_sub_diagnosis"
                                :disabled="!item.is_sub_diagnosis"
                                v-model="item.sub_diagnosis" 
                                color="accent" 
                                :items="parent_diagnosis"
                                item-text="name"
                                item-value="id" 
                                label="Select Parent Diagnosis" 
                                solo flat></v-select>
                        <!-- </b-form-group> -->
                        <v-text-field color="accent" label="Name" v-model="item.name" :rules="requiredRules" outline></v-text-field>
                        <v-textarea color="accent" label="Description" v-model="item.description" solo flat></v-textarea>
                        <v-layout>
                            <v-checkbox v-model="item.covered_first_year" name="covered_first_year" label="Covered First Year"></v-checkbox>
                            <v-checkbox v-model="item.covered_after_year" name="covered_after_year" label="Covered After a Year"></v-checkbox>
                            <v-checkbox v-model="item.exclusion" name="exclusion" label="Exclusion"></v-checkbox>
                        </v-layout>
                        <!-- <b-form-group v-model="row" class="display-block" row>
                            <b-form-checkbox v-model="item.covered_first_year" name="covered_first_year">Covered First Year</b-form-checkbox>
                            <b-form-checkbox v-model="item.covered_after_year" name="covered_after_year">Covered After a Year</b-form-checkbox>
                            <b-form-checkbox v-model="item.exclusion" name="exclusion">Exclusion</b-form-checkbox>
                        </b-form-group> -->
                    </div>
                </v-card-text>
            </v-form>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="tertiary" depressed dark @click="close">Cancel</v-btn>&nbsp;
                <v-btn color="accent" @click.native="submit" :loading="loading" depressed>Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data() {
            return {
                popup_diagnosis: false,
                is_sub_diagnosis : false,
                
                row : true,
                loading: false,
                parent_diagnosis : [],
                requiredRules: [ v => !!v || 'This input cannot be blank.',],
                diagnosis: { name: null,description: null,covered_first_year: null,covered_after_year: null,exclusion: null,},
                item:{
                    is_sub_diagnosis : false,
                    covered_first_year : false,
                    covered_after_year : false,
                    exclusion : false,
                },
                filters:{}
            }
        }, 
        props : {
            eventName: {
              type: String,
              default: 'createDiagnosis'
            },
        },
        methods : {
            save(){
                this.loading = true;
                if(this.$refs.form_submit.validate())
                {
                    axios.post('/api/diagnosis_list', this.item).then(response =>
                    {
                        EventBus.$emit(this.eventName, response)
                        this.toaster(response.data, response.status)
                        this.$refs.form_submit.reset()
                        this.getParent()
                        this.close()
                    })
                    .catch(errors =>
                    {
                        this.error_message  = errors.response;
                        this.toaster(this.error_message.data, errors.response.status)
                    })
                    .finally(()=>{
                        this.loading = false
                    });
                }
                else
                {
                    this.loading     = false;
                }
            },
           
            getParent(filters = 0){
                axios.get('api/diagnosis_list'+this.generateFilterURL(filters))
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
            close(){
                EventBus.$emit(this.eventName)
                this.popup_diagnosis = false
                this.item.is_sub_diagnosis = false
                this.item.covered_first_year = false
                this.item.covered_after_year = false
                this.item.exclusion = false
            },
            clear(){
                this.$refs.form_submit.reset()
                
            },
        },
        mounted(){
            this.getParent();
        },
 
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .create-diagnosis {
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
                margin: 5px;
            }
        }
    }
</style>