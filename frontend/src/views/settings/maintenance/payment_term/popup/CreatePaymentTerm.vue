<template>
    <v-dialog v-model="dialog" scrollable persistent max-width="500px">
        <v-btn slot="activator" depressed color="accent"><i class="fas fa-plus mr-3"></i>Create Payment Term</v-btn>
        <v-card class="create-existing">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Create Payment Term</v-card-title>
            <v-form ref="form" @submit.prevent="save">
                <v-card-text>
                    <div class="main-holder">
                        <v-text-field color="accent" v-model="form.name" label="Name" :rules="requiredRules" outline></v-text-field>
                        <v-text-field color="accent" v-model="form.number"  type="Number" label="Number of Days" :rules="requiredRules" outline></v-text-field>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed type="submit" :loading="loading">SAVE</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data :() => ({
            form : {},
            dialog: false,
            requiredRules: [
                v => !!v || 'Input is required',
            ],
            loading :false,
        }),
        props : {
            eventName: {
                type: String,
                default: 'createPaymentTerm'
            }
        },
        methods:{
            save(){
                if(this.$refs.form.validate()){
                    this.loading = true;
                    axios.post('api/payment-term',this.form)
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
                    this.toaster('Fill up all fields',400)
                }
            },
            close(){
                this.$refs.form.reset();
                this.dialog = false
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .create-existing {
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