<template>
    <span>
       <!--  <span @click="popup_edit_procedures_type = true" slot="activator">
            <slot></slot>
        </span> -->
    <v-dialog persistent  v-model="popup_edit_procedures_type" scrollable max-width="500px">
        <span slot="activator">
            <slot></slot>
        </span>
        <v-card class="edit-procedures">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Edit Plan Description</v-card-title>
            <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
                <v-card-text>
                    <div class="main-holder">
                        <v-text-field color="accent" label="Name" v-model="item.name" outline ></v-text-field>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed type="submit" ref="submit_button" :loading="loading">UPDATE</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
    </span>
</template>

<script>
    export default {
        data() {
            return {
                type: ['APE', 'Laboratory'],
                popup_edit_procedures_type: false,
                loading: false
            }
        },
        props : {
            item:{
                type: Object,
            },
            eventName: {
                type: String,
                default: 'editProceduresType'
            }
        },
        methods : {
            save(){
                this.loading = true
                if(this.$refs.form_submit.validate())
                {
                    axios.put(`/api/procedures_type/${this.item.id}`, this.item)
                    .then(response => {
                        EventBus.$emit(this.eventName, "")
                        this.toaster(response.data, response.status)
                        this.get_procedure_types();
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
                    this.loading     = false;
                }
            },
            close() {
            EventBus.$emit(this.eventName)
            this.popup_edit_procedures_type = false
            },
        }

    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .edit-procedures {
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