<template>
    <span>
       <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="700px">
            <v-card class="edit-provider">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Create Provider</v-card-title>
                <v-card-text>
                    <div class="main-holder">
                        <div class="edit-provider__holder">
                            <v-text-field color="accent" v-model="form.name" label="Provider Name" :rules="requiredRules" outline readonly></v-text-field>
                            <v-select color="accent" v-model="form.rate_rvs" :items="provider_rate" :rules="requiredRules" label="Select Rate/RVS" outline readonly></v-select>
                            <v-text-field color="accent" v-model="form.contact_person" label="Contact Person" outline readonly></v-text-field>
                            <v-text-field color="accent" v-model="form.tel_number" label="Tel. No." outline readonly></v-text-field>
                            <v-text-field color="accent" v-model="form.contact_number" label="Mobile No." outline readonly></v-text-field>
                            <v-text-field color="accent" v-model="form.email" label="Email Address" :rules="requiredRules" outline readonly></v-text-field>
                        </div>
                        <v-textarea name="input-7-4" v-model="form.address" label="Address" outline readonly></v-textarea>
                        <div v-for="(listItem,key) in list" :key="key">
                            <v-layout row wrap>
                                <v-flex xs12 sm12 md12>
                                    <v-flex xs12 sm12 md12 mx-3 justify-center d-flex>
                                        <v-text-field color="accent" label="Payee" v-model="list[key].name" :rules="requiredRules" outline readonly></v-text-field>
                                    </v-flex>
                                </v-flex>
                            </v-layout>
                        </div>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="dialog = false">CANCEL</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default {
        data : () => ({
            dialog: false,
            provider_rate: ['201'],
            requiredRules: [
                v => !!v || 'Input is required',
            ],
            headers: [
                { text: '', value: 'number', sortable: false, width:'1%' },
                { text: 'Payee Name', value: 'payee', sortable: false, width:'90%' } ,
            ],
            selected:[],
            list: [],
            tablePagination:{
                rowsPerPage: -1,
            },
        }),
        props : {
            form:{
                type: Object
            },
        },
        methods : {
            async loadDialog(){
                let get_provider_data = await axios.get('/api/provider/'+this.form.id);

                const {data: provider_data} = get_provider_data;

                this.list = provider_data.provider_payee;

                this.dialog = await true;
            },
            close(){

                this.dialog = false
            },
            
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .edit-provider {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .v-text-field {
            font-size: 15px;

            .v-input__slot {
                border: 1px solid #919191 !important;
            }
        }

        .main-holder {
            border: 1px solid #ededed;
            padding: 15px 15px 0px;
        }

        .edit-provider__holder {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 50px;



            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            .btn-holder {
                text-align: center;
            }
        }
    }
</style>