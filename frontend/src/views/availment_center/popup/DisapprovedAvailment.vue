<template>
    <span>
        <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="500px">
        <v-card class="coverage-description">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Disapproved Availment</v-card-title>
            <v-card-text>
                <v-form ref="form" @submit.prevent="save" lazy-validation>
                    <div class="main-holder">
                        <span>Do you really want to disapproved this availment <strong>{{form.approval_id}}</strong> ? <br> Kindly fillup remarks for the reason. </span>
                        <v-textarea v-model="item.status_remarks" label="Remarks" color="accent" :rules="requiredRules" outline></v-textarea>
                    </div>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="tertiary" depressed dark @click="dialog = false" >CANCEL</v-btn>
                        <v-btn color="accent" depressed @click.native="submit" type="submit">SUBMIT</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card-text>
        </v-card>
        <input type="submit" ref="submit_button" class="hide-submit">
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
            item: [],
        }),
        props : {
            eventName : {
                type:String,
                default: 'disapprovedAvailment'
            },
            form : {
                type: Object
            }
        },
        methods:{
            loadDialog(){
                this.dialog = true
                this.item = this.form
            },
            submit() {
                this.$refs.submit_button.click()
            },
            save(){
                if(this.$refs.form.validate())
                {
                    this.item.status = 'disapproved'
                    this.item.for_disapproved = true
                    axios.put('api/availment/'+this.item.id,this.item)
                    .then(response => {
                        EventBus.$emit(this.eventName, response)
                        this.toaster(response.data, response.status)
                        this.close()
                    })
                    .catch(error => {
                        this.error_message  = error.response.data.data
                        EventBus.$emit(this.eventName)
                        this.toaster(this.error_message, error.response.status)
                    })
                    .finally(() => {
                        this.loading = false
                    })
                }
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