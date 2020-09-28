<<template>
<span>
    <span @click="loadDialog">
            <slot></slot>
    </span>
    <v-dialog v-model="dialog" persistent scrollable max-width="700px">
        <v-card class="add-doctor">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">View Doctor</v-card-title>
            <v-card-text>
                <v-form ref="form" @submit.prevent="save">
                <div class="main-holder" :items="item">
                    <div class="add-doctor__holder">
                        <v-text-field color="accent" v-model="item.first_name" label="First Name" outline readonly></v-text-field>
                        <v-text-field color="accent" v-model="form.middle_name" label="Middle Name" outline readonly></v-text-field>
                        <v-text-field color="accent" v-model="form.last_name" label="Last Name" outline readonly></v-text-field>
                    </div>
                    <div class="add-doctor___detailholder">
                            <v-text-field color="accent" v-model="form.contact_number" label="Contact Number" outline readonly></v-text-field>
                            <v-text-field color="accent" v-model="form.email" label="Email Address" outline readonly></v-text-field>
                    </div>
                    <div class="add-doctor___detailholder">
                            <v-text-field color="accent" v-model="form.tin" label="TIN" outline readonly></v-text-field>
                            <v-text-field color="accent" v-model="form.tax" label="Vatable or Non-Vatable" outline readonly></v-text-field>
                    </div>
                    <div v-for="(listItem,key) in list" :key="key">
                        <v-layout>
                            <v-flex >
                                <v-flex d-flex>
                                <v-text-field color="accent" v-model="list[key].provider.name" label="Network Provider" outline readonly></v-text-field>
                                </v-flex>
                            </v-flex>
                        </v-layout>
                    </div>
                </div>
                <input type="submit" ref="submit_button" class="hide-submit">
                </v-form>
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
            item: [],
            provider: [],
            list: [],
        }),
        props : {
            eventName : {
                type : String,
                default : 'editDoctor'
            },
            form : {
                type : Object
            },
        },
        methods : {
            loadDialog() {
                this.dialog = true;
                this.item = this.form
                this.list = this.form.doctor_provider
            },
            submit() {
                this.$refs.submit_button.click()
            },
            close(){
                this.$refs.form.reset();
                this.dialog = false
            },
            
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .add-doctor {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

        .main-holder
        {
            padding: 30px 30px 0px;
            border: 1px solid #ededed;
        }

        .add-doctor__holder {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-column-gap: 30px;

            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            .btn-holder
            {
                text-align: center;
            }
        }
        
        .add-doctor___detailholder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
        }
    }
</style>