<template>
     <!-- <span @click="loadDialog">
            <slot></slot>
        </span> -->
    <v-dialog v-model="dialog" persistent scrollable max-width="500px">
        <v-btn slot="activator" depressed color="accent"><i class="fas fa-plus mr-3"></i>Create Bank</v-btn>
        <v-card class="coverages-description">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Create Bank</v-card-title>
            <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
                <v-card-text>
                        <div class="main-holder">
                            <v-text-field :rules="requiredRules" color="accent" v-model="form.name" label="Name" outline></v-text-field>
                        </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>    
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed type="submit" ref="submit_button" :loading="loading">SAVE</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
       data: () => ({
                loading: false,
                form: {
                    name: null
                },
                dialog: false,
                requiredRules: [
                    v => !!v || 'Input is required',
                ],

            }),
        props : {
        item:{
            type: Object,
        },
        eventName: {
          type: String,
          default: 'createBank'
        }
        },
        methods : {
            save(){
            this.loading = true;
            if(this.$refs.form_submit.validate())
            {
                axios.post('/api/bank', this.form).then(response =>
                {
                    EventBus.$emit(this.eventName, response)
                    this.toaster(response.data, response.status)
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
        loadDialog(){
            this.dialog = true
        },
        close() {
            this.clear()
            EventBus.$emit(this.eventName)
            this.dialog = false
        },
        submit() {
            this.$refs.submit_button.click()
        },
        clear(){
            this.$refs.form_submit.reset()
        }
           
        },
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

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