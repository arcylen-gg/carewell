<template>
    <v-dialog v-model="dialog" scrollable persistent max-width="700px">
        <v-btn slot="activator" depressed color="accent"><i class="fas fa-plus mr-3"></i>Create User</v-btn>
        <v-card class="create-user">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Create User</v-card-title>
            <v-form ref="form" @submit.prevent="save">
                <v-card-text>
                    <div class="create-user__holder">
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
                    <v-btn color="accent" depressed type="submit" :loading="loading">SAVE</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data : () => ({
            form : {},
            dialog: false,
            requiredRules: [
                v => !!v || 'Input is required',
            ],
            showPass: false,
            showPassConfirm: false,
            loading:false,
        }),
        props : {
            eventName: {
                type: String,
                default: 'createAdmin'
            },
        },
        methods:{
            save(){
                if(this.$refs.form.validate()){
                    this.loading = true;
                    if(this.form.password === this.form.password_confirmation)
                    {
                        axios.post('api/user',this.form)
                        .then(response=>{
                            this.toaster(response.data,response.status)
                            EventBus.$emit(this.eventName, response)
                            this.close();
                        })
                        .catch(error => {
                            this.toaster(error.response.data,error.response.status)
                            EventBus.$emit(this.eventName, error)
                        })
                        .finally(()=>{
                            this.loading = false;
                        })
                    }
                    else
                    {
                        this.toaster('Password does not match.', 400)
                    }
               
                }
                else
                {
                    this.toaster('Fill up all fields.', 400)
                }
            },
            close(){
                this.$refs.form.reset();
                this.dialog = false
            }
        },
    }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

    .create-user {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .create-user__holder {
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