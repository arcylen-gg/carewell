<template>
    <span>
        <span @click="loadDialog">
                <slot></slot>
            </span>
        <v-dialog v-model="dialog" persistent scrollable max-width="500px">
            
            <v-card class="edit-description">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Edit Plan Description</v-card-title>

                <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
                    <v-card-text>
                        <div class="main-holder">
                            <v-text-field color="accent" label="Name" v-model="item.name" outline></v-text-field>
                            <global-select-box
                                v-model="item.procedure_type_id"
                                item-text="name"
                                item-value="id"
                                api-link="api/procedures_type"
                                label="Select Type"
                                :filters={is_archived:0}
                                :is-load="dialog"
                                :default="item.procedure_type_id"
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
                loading:false,
                dialog: false,
            }
        },
        props : {
            item:{
                type: Object,
            },
            eventName: {
                type: String,
                default: 'editPlanDescription'
            }
        },
         methods : {
            save(){
                this.loading = true
                if(this.$refs.form_submit.validate())
                {
                    axios.put(`/api/procedures/${this.item.id}`, this.item)
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
                    this.loading     = false;
                }
            },
            close() {
                EventBus.$emit(this.eventName)
                this.dialog = false
            },
            loadDialog(){
                this.dialog = true
            }
        },
        mounted(){
        },
    }
</script>

<style lang="scss">
    @import "../../../../../scss/app.scss";

    .edit-description {
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