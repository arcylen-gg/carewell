<template>
    <span>
        <!-- <span block color="primary" dark @click="loadDialog"><slot>Open Dialog</slot></span> -->
        <span @click="loadDialog" style="cursor: pointer">
            <slot><v-icon>{{ !item.is_archived ? 'mdi-delete' : 'mdi-restore'}}</v-icon> Archive Data</slot>
        </span>
        <v-dialog v-model="dialog" persistent max-width="400px">
            <v-card class="edit-description">
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">
                    <span class="headline white--text">{{ !item.is_archived ? 'Archive' : 'Restore'}} User Position</span>
                </v-card-title>
                <!-- Dialog Content -->
                <v-card-text>
                    <v-form ref="form"  @submit.prevent="save" lazy-validation>
                        <div class="main-holder">
                            You are about to {{ !item.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{ item.name }}"</span> position. Are you sure you want to do this?
                        </div>
                        <input type="submit" ref="submit_button" class="hide-submit">
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed @click.native="submit" :disabled="loading">{{item.is_archived == 0 ? 'ARCHIVE' : 'RESTORE'}}</v-btn>
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
    data: () => ({
        form : {},
        dialog: false,
        loading: false,
        requiredRules: [
            v => !!v || 'This input cannot be blank.',
        ],
    }),
    computed : {
    },
    props : {
        eventName: {
          type: String,
          default: 'archiveUserPosition'
        },
        item: {
            type: Object,
        }
    },
    methods: {
        reloadTable() {

        },
        save() {
            this.loading = true
            this.item.for_archive = 1
            axios.put(`/api/user_position/${this.item.id}`, this.item)
            .then(response => {
                EventBus.$emit(this.eventName, response)
                this.toaster(response.data.data, response.status)
                this.get_procedure_types()
                this.close()
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
        submit() {
                this.$refs.submit_button.click()
            },
        close() {
            this.dialog = false
            this.formTitle = null
        },
        clear() {
            this.$refs.form.reset()
        },
        loadDialog() {
            EventBus.$on('positionAccess', (data)=> {
                data.forEach((d, k) => {
                    this.item.user_position_accesses[k].types = d.types
                })
            })
            this.dialog = true
            this.reloadTable()
        }
    },
    mounted () {
    }
  }
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

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