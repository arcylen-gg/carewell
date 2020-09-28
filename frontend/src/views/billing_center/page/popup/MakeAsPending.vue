<template>
    <span>

        <span slot="activator" @click="make_as_pending = true" style="cursor: pointer">
            <slot></slot>
        </span>
        <v-dialog v-model="make_as_pending" scrollable persistent max-width="650px">   
            <v-card class="edit-user">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">{{formTitle}}</v-card-title>
                <v-form ref="form" @submit.prevent="save">
                    <v-card-text>
                        <div class="main-holder">
                            You are about to make this as <strong>"pending"</strong>. Are you sure you want to do this?
                        </div>
                        <v-textarea v-model="item.remarks" label="Remarks"></v-textarea>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                        <v-btn color="accent" depressed type="submit">Make As Pending</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
    export default {
        data : () => ({
            formTitle: 'Make As pending',
            make_as_pending: false,
        }),
        props : {
            item : {
                type:Object
            },
            eventName: {
                type: String,
                default: 'markaspending'
            }
        },
        methods : {
            loadDialog(){
                this.make_as_pending = true;
            },
            save(){
                console.log(this.item)
                if(this.item.cal_member_count == 0)
                {
                    this.toaster("You cannot proceed when there is no cal member(s). Please import cal member first.", 300)
                }
                else
                {
                    axios.put(`/api/cal_status/${ this.item.id }`,this.item)
                    .then(response => {
                        this.toaster(response.data, response.status)
                        this.close()
                    })
                    .catch(error => {
                        this.toaster(error.response.data, error.response.status) 
                    })
                    .finally(() => {
                        this.loading = false
                    })
                    
                }
            },
            close(){
                this.make_as_pending = false
                this.$router.push('/billing-&-collection-center')
            }
        },
        mounted(){

        }

    }
</script>

<!-- <style lang="scss">
    @import "../../../../../scss/app.scss";

    .edit-user {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .main-holder {
            border: 1px solid #ededed;
            padding: 40px;

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #ced4da !important;
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
</style> -->