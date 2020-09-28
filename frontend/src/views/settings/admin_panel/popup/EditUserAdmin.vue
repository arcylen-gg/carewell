<template>
    <span>
        <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="700px">
            <v-card class="edit-user">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Edit Details</v-card-title>
                <v-form ref="form" @submit.prevent="save">
                    <v-card-text>
                        <div class="edit-user__holder">
                            <v-text-field color="accent" v-model="form.first_name" label="First Name" :rules="requiredRules" outline></v-text-field>
                            <v-text-field color="accent" v-model="form.middle_name" label="Middle Name" outline></v-text-field>
                            <v-text-field color="accent" v-model="form.last_name" label="Last Name" :rules="requiredRules" outline></v-text-field>
                            <v-text-field color="accent" v-model="form.email" label="Email" :rules="requiredRules" outline></v-text-field>
                            <v-text-field
                                color="accent"
                                v-model="form.password"
                                :append-icon="showPass ? 'visibility_off' : 'visibility'"
                                :rules="requiredRules"
                                :type="showPass ? 'text' : 'password'"
                                name="input-10-2"
                                label="Password"
                                class="input-group--focused"
                                @click:append="showPass = !showPass"
                                outline
                            ></v-text-field>
                            <v-text-field
                                color="accent"
                                v-model="form.password_confirmation"
                                :append-icon="showPassConfirm ? 'visibility_off' : 'visibility'"
                                :rules="requiredRules"
                                :type="showPassConfirm ? 'text' : 'password'"
                                name="input-10-2"
                                label="Confirm Password"
                                class="input-group--focused"
                                @click:append="showPassConfirm = !showPassConfirm"
                                outline
                            ></v-text-field>
                            <global-select-box
                                v-model="form.user_position_id" 
                                :rules="requiredRules" 
                                :is-load="dialog"
                                :filters="{is_archived:0}"
                                :default="form.user_position_id"
                                color="accent" 
                                api-link="api/user_position"
                                item-text="name"
                                item-value="id"
                                label="Position" 
                                outline
                            >
                            </global-select-box>
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
        data : () => ({
                dialog: false,
                user_position: ['Admin', 'All Access'],
                requiredRules: [
                    v => !!v || 'Input is required',
                ],
                showPass: false,
                showPassConfirm: false,
                loading : false,
        }),
        props : {
            form : {
                type:Object
            },
            eventName: {
                type: String,
                default: 'editAdmin'
            }
        },
        methods : {
            loadDialog(){
                this.dialog = true
                this.form.password = this.form.decrypt_password
            },
            save(){
                if(this.$refs.form.validate()){
                    this.loading = true;
                    axios.put('api/user/'+this.form.id,this.form)
                    .then(response=>{
                        this.toaster(response.data,response.status)
                        EventBus.$emit(this.eventName, response)
                        this.close();
                    })
                    .catch(error => {
                        this.toaster(error.response.data,error.response.status)
                        EventBus.$emit(this.eventName, error.response)
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
                EventBus.$emit(this.eventName, '')
                this.dialog = false
            }
        },
        mounted(){

        }

    }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

    .edit-user {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .edit-user__holder {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 50px;
            border: 1px solid #ededed;
            padding: 30px 30px 15px;
            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            .btn-holder
            {
                text-align: center;
            }
        }
    }
</style>