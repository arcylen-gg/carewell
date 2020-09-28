<template>
    <span>
        <v-dialog v-model="dialog" persistent max-width="350px">
            <v-btn style="width: 100%; height: 50px;" slot="activator" color="success" depressed @click="loadDialog"><v-icon large class="mr-3">mdi-cloud-download</v-icon>Download
            Template</v-btn>
        <v-card class="coverage-description">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Download Template</v-card-title>
            <v-card-text>
                <v-form ref="form" @submit.prevent="save" lazy-validation>
                    <div class="main-holder">
                        <v-autocomplete 
                            v-model="company_id" 
                            :rules="requiredRules" 
                            label="Select Company" 
                            outline 
                            flat 
                            :items="item" 
                            color="accent" 
                            item-value="id" 
                            item-text="name"
                        />
                    </div>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="tertiary" depressed dark @click="dialog = false" >CANCEL</v-btn>
                        <v-btn color="accent" depressed type="submit">DOWNLOAD</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card-text>
        </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default {
        data :() => ({
            dialog: false,
            requiredRules: [
                v => !!v || "Input is required"
            ],
            item : [],
            company_id : null,
            filters : {},
        }),
        methods:{
            loadDialog(){
                this.getCompany();
            },
            save(){
                if(this.$refs.form.validate())
                {
                    window.open(this.$axios.defaults.baseURL+"/api/export/member?company_id="+this.company_id,"_blank");
                    this.close();
                }
            },
            getCompany(statusValue = 0){
                this.filters.is_archived = this.status = statusValue
                axios.get('api/select/company_list'+this.generateFilterURL(this.filters))
                .then(response=>{
                    this.item = response.data
                })
                .catch(error=>{

                })
            },
            clear(){
                this.$refs.form.reset();
            },
            close(){
                this.dialog = false
            },
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";


    .coverage-description {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .main-holder {
            border: 1px solid #ededed;
            padding: 40px;

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #ced4da !important;
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