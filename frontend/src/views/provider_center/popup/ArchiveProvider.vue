<template>
    <span>
       <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="700px">
            <v-card class="edit-provider">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">{{formTitle}}</v-card-title>
                <v-card-text>
                    <v-form ref="form"  @submit.prevent="save">
                        <div class="main-holder">
                            You are about to {{ !form.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{ form.name }}"</span> provider. Are you sure you want to do this?
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
            formTitle: 'Archive Details',
            dialog: false,
            error_message: null,
            loading: false,
        }),
        props : {
            form:{
                type: Object
            },
            eventName : {
                type: String,
                default : 'archiveProvider'
            }
        },
        methods : {
            loadDialog(){
                this.dialog = true;
                this.form.is_archived == 0 ? this.formTitle = 'Archive Details' : this.formTitle = 'Restore Details'
            },
            submit() {
                this.$refs.submit_button.click()
            },
            save(){
                this.form.for_archive = true
                this.loading = true
                axios.put('api/provider/'+this.form.id,this.form)
                .then(response => {
                    EventBus.$emit(this.eventName, response)
                    this.toaster(response.data.data, response.status)
                    this.close()
                    // this.get_count()
                })
                .catch(error => {
                    this.error_message  = error.response.data.data
                    EventBus.$emit(this.eventName)
                    this.toaster(this.error_message, error.response.status)
                })
                .finally(() => {
                    this.loading = false
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