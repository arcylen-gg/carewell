<template>
    <span>
        <span @click="loadDialog" style="cursor: pointer">
            <slot><v-icon>mdi-eye</v-icon> View Data</slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="500px">
            <v-card class="edit-description">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">View Plan Description</v-card-title>
                <v-card-text>
                    <div class="main-holder">
                        <v-text-field color="accent" label="Name" v-model="item.name" outline  readonly  ></v-text-field>
                        <v-select color="accent"
                            readonly 
                            :items="select_type" 
                            label="Select Type" 
                            v-model="item.procedure_type_id"
                            item-text="name"
                            item-value="id" 
                            solo flat></v-select>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                </v-card-actions>
                <v-card-actions class="grey lighten-3 pa-0 ma-0" v-if="loading">
                    <v-progress-linear slot="progress" color="grey" class="pa-0 ma-0" indeterminate></v-progress-linear>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default { 
        data() {
            return {
                type: ['APE', 'Laboratory'],
                popup_edit_plan_description: false,
                status:0,
                select_type:[],
                filters:{},
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
            getData(statusValue = 0) {
                this.filters.is_archived = statusValue
                this.getType(this.filters)
                },
            getType(filters){
                 axios.get('api/procedures_type'+this.generateFilterURL(filters))
                .then(response=>{
                    this.select_type = response.data.data.data
                })
                .catch(error=>{

                })
                },
            save(){
                this.loading = true
                if(this.$refs.form_submit.validate())
                {
                    axios.put(`/api/procedures/${this.item.id}`, this.item)
                    .then(response => {
                        EventBus.$emit(this.eventName, response)
                        this.close()
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
                this.dialog = false
            },
            loadDialog() {
                this.dialog = true
                this.getData()
            }
            },
        mounted(){
            // this.getData();
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