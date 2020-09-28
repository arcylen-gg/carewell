<template>
<span>
    <span @click="loadDialog">
                <slot></slot>
    </span>
    <v-dialog v-model="dialog" scrollable persistent max-width="500px">
        <v-card class="edit-payment">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Edit Payment Method</v-card-title>
            <v-form ref="form" @submit.prevent="save">
                <v-card-text>
                    <div class="main-holder">
                        <v-text-field class="c-input" color="accent" v-model="form.name" label="Name" :rules="requiredRules" outline></v-text-field>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed type="submit" :loading="loading">UPDATE</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</span>
</template>

<script>
    export default {
        data :() => ({
            dialog: false,
            requiredRules: [
                v => !!v || 'Input is required',
            ],
            loading : false,
        }),
        props : {
            eventName: {
                type: String,
                default: 'editPaymentMethod'
            },
            form : {
                type:Object
            },
        },
        methods:{
            loadDialog(){
                this.dialog = true
            },
            save(){
                if(this.$refs.form.validate()){
                    this.loading = true
                    axios.put('api/payment-method/'+this.form.id,this.form)
                    .then(response=>{
                        this.toaster(response.data,response.status)
                        EventBus.$emit(this.eventName, response)  
                        this.close();
                    })
                    .catch(error => {
                        this.toaster(error.response.data,error.response.data.status)
                        EventBus.$emit(this.eventName, error) 
                    })
                    .finally(()=>{
                        // this.clear();
                        this.loading = false;
                    })
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
                EventBus.$emit(this.eventName)
                this.dialog = false
                this.clear()
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .edit-payment {
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