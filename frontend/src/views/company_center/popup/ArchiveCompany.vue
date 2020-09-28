<template>
    <span>
        <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" scrollable persistent max-width="400px" lazy>
            <v-card class="archive-company">
                <v-card-title class="title" style="background-color: #303E55; color: #fff;">{{formTitle}}</v-card-title>
                <v-card-text>
                    <v-form ref="form"  @submit.prevent="save">
                        <div class="top-holder">
                            You are about to {{ !form.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{ form.name }}"</span>. Are you sure you want to do this?
                        </div>
                        <input type="submit" ref="submit_button" class="hide-submit">
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
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
            loading: false,
        }),
        props : {
            form:{
                type: Object
            },
            eventName : {
                type: String,
                default : 'archiveCompany'
            }
        },
        methods: {
            loadDialog(){
                this.dialog = true;
                this.form.is_archived == 0 ? this.formTitle = 'Archive Details' : this.formTitle = 'Restore Details'
            },
            submit() {
                this.$refs.submit_button.click()
            },
            save(){
                this.loading = true;
                this.form.for_archive = true
                axios.put('api/company/'+this.form.id,this.form)
                .then(response=>{
                    this.toaster(response.data.data,response.data.message)
                    EventBus.$emit(this.eventName, response)
                    // this.get_count()
                })
                .catch(error => {
                    this.toaster(error.response.data.data,error.response.data.message)
                    EventBus.$emit(this.eventName, response)
                })
                .finally(()=>{
                    this.loading = false;
                    this.close();
                })
            },
            close(){
                this.dialog = false
            },
            pickFileContract() {
                this.$refs.imageContract.click()
            },
            pickFileSchedule() {
                this.$refs.image.click()
            },

            onFilePickedContract(e) {
                const files = e.target.files
                if (files[0] !== undefined) {
                    this.imageNameContract = files[0].name
                    if (this.imageNameContract.lastIndexOf('.') <= 0) {
                        return
                    }
                    const fr = new FileReader()
                    fr.readAsDataURL(files[0])
                    fr.addEventListener('load', () => {
                        this.imageUrlContract = fr.result
                        this.imageFileContract = files[0] // this is an image file that can be sent to server...
                    })
                } else {
                    this.imageNameContract = ''
                    this.imageFileContract = ''
                    this.imageUrlContract = ''
                }
            },
            onFilePickedSchedule(e) {
                const files = e.target.files
                if (files[0] !== undefined) {
                    this.imageNameSchedule = files[0].name
                    if (this.imageNameSchedule.lastIndexOf('.') <= 0) {
                        return
                    }
                    const fr = new FileReader()
                    fr.readAsDataURL(files[0])
                    fr.addEventListener('load', () => {
                        this.imageUrlSchedule = fr.result
                        this.imageFileSchedule = files[0] // this is an image file that can be sent to server...
                    })
                } else {
                    this.imageNameSchedule = ''
                    this.imageFileSchedule = ''
                    this.imageUrlSchedule = ''
                }
            }
        }
    }
</script>


<style lang="scss">
    @import "../../../scss/app.scss";

    .archive-company {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .top-holder {
            border: 1px solid #ededed;
            padding: 15px 15px 0px;
            margin-bottom: 10px;

            .top-holder__others {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
            }

            .top-holder__contact {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                grid-column-gap: 10px;
            }

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
        }

        .bottom-holder {
            padding: 15px;
            border: 1px solid #ededed;

            .v-card {
                border: 1px solid #ededed;
            }

            .upload-file {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 40px;
                grid-row-gap: 20px;
            }

            .select-coverage {
                display: flex;
            }

            .v-text-field {
                font-size: 15px;
                padding-right: 10px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }
        }
    }
</style>