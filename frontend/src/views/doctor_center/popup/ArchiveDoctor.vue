<template>
    <span>
       <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="700px">
            <v-card class="edit-doctor">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">{{formTitle}}</v-card-title>
                <v-card-text>
                    <v-form ref="form"  @submit.prevent="save">
                        <div class="main-holder">
                            You are about to {{ !form.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{form.first_name}} {{form.middle_name}} {{form.last_name}}"</span>. Are you sure you want to do this?
                        </div>
                        <input type="submit" ref="submit_button" class="hide-submit">
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="dialog = false">CANCEL</v-btn>
                    <v-btn color="accent" depressed @click.native="submit" :loading="loading">{{form.is_archived == 0 ? 'ARCHIVE' : 'RESTORE'}}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default {
        data : () => ({
            formTitle: 'Archive Doctor',
            dialog: false,
            item: [],
            loading: false,
        }),
        props : {
            form:{
                type: Object
            },
            eventName : {
                type: String,
                default : 'archiveDoctor'
            }
        },
        methods : {
            loadDialog(){
                this.dialog = true;
                this.item = this.form
                this.form.is_archived == 0 ? this.formTitle = 'Archive Doctor' : this.formTitle = 'Restore Doctor'
            
            },
            submit() {
                this.$refs.submit_button.click()
            },
            save(){
                this.loading = true;
                this.form.for_archive = true
                axios.put('api/doctor/'+this.form.id,this.form)
                .then(response=>{
                    this.toaster(response.data.data,response.data.message)
                    EventBus.$emit(this.eventName, response)
                    this.close();
                })
                .catch(error => {

                })
                .finally(()=>{
                    this.loading = false,
                    this.close();
                })
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

    .edit-doctor {
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