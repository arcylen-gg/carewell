<template>
    <v-dialog v-model="dialog" scrollable max-width="500px">
        <span slot="activator"><slot><i class="fas fa-plus mr-3"></i>Create Plan Description</slot></span>
        <v-card class="coverages-description">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Create Plan Description</v-card-title>
            <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
                <v-card-text>
                    <div class="main-holder">
                        <v-text-field color="accent" label="Name" v-model="form.name" outline></v-text-field>
                        <global-select-box
                            v-model="form.procedure_type_id"
                            item-text="name"
                            item-value="id"
                            api-link="api/procedures_type"
                            label="Select Type"
                            :filters={is_archived:0}
                            :is-load="dialog"
                            :default="form.procedure_type_id"
                            solo
                            flat
                        >
                        </global-select-box>
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
        data() {
            return {
                dialog: false,
                loading: false,
            }
        },
        props : {
            eventName: {
              type: String,
              default: 'createProcedures'
            },
            form: {
                type: Object,
                default: function () {
                    return {
                        name: null,
                        procedure_type_id: 1
                    }
                }
            }   
        },
        methods : {
            save(){
                this.loading = true;
                if(this.$refs.form_submit.validate())
                {
                    axios.post('/api/procedures', this.form).then(response =>
                    {
                        EventBus.$emit(this.eventName, response)
                        // EventBus.$emit('addCoveragePlanProcedures', response)
                        EventBus.$emit('addCoveragePlanProceduresVuex', response)
                        this.toaster(response.data, response.status)
                        this.clear()
                        this.close()
                    })
                    .catch(errors =>
                    {
                        this.toaster(errors.response.data, errors.response.data)
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
            },   
        },
        mounted(){
    
        },

    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .coverages-description {
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