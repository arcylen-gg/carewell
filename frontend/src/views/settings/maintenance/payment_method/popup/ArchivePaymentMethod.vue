<template>
<span>
    <span @click="loadDialog">
                <slot></slot>
    </span>
    <v-dialog v-model="dialog" scrollable persistent max-width="400px">
        <v-card class="archive-payment">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">{{formTitle}}</v-card-title>
            <v-form ref="form" @submit.prevent="save">
                <v-card-text>
                    <div class="main-holder">
                        You are about to {{ !form.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{ form.name }}"</span> payment method. Are you sure you want to do this?
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed type="submit" :loading="loading">{{form.is_archived == 0 ? 'ARCHIVE' : 'RESTORE'}}</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</span>
</template>

<script>
    export default {
        data :() => ({
            formTitle: 'Archive Details',
            dialog: false,
            loading : false,
        }),
        props : {
            form : {
                type:Object
            },
            eventName: {
                type: String,
                default: 'archivePaymentMethod'
            }
        },
        methods:{
            loadDialog(){
                this.dialog = true
                this.form.is_archived == 0 ? this.formTitle = 'Archive Details' : this.formTitle = 'Restore Details'
            },
            save(){
                this.form.for_archive = true
                this.loading = true;
                axios.put('api/payment-method/'+this.form.id,this.form)
                .then(response=>{
                    this.toaster(response.data.data,response.data.message)
                    EventBus.$emit(this.eventName, response)  
                })
                .catch(error => {
                    this.toaster(error.response.data.data,error.response.data.message)
                    EventBus.$emit(this.eventName, error)  
                })
                .finally(()=>{
                    this.loading = false;
                    this.close();
                })
            },
            close(){
                this.dialog = false
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .archive-payment {
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