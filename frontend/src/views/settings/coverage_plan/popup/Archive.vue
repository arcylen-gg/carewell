<template>
    <span>
        <!-- <span block color="primary" dark @click="loadDialog"><slot>Open Dialog</slot></span> -->
        <span @click="loadDialog" style="cursor: pointer">
            <slot><v-icon>{{ !item.is_archived ? 'mdi-delete' : 'mdi-restore'}}</v-icon> Archive Data</slot>
        </span>
        <v-dialog v-model="dialog" persistent max-width="400px">
            <v-card>

                <!-- Dialog Title -->
                <v-card-title
                class="headline primary"
                color="primary" 
                dark
                primary-title
                >
                <span class="headline white--text">{{ !item.is_archived ? 'Archive' : 'Restore'}} Coverage Plan</span>
                </v-card-title>

                <!-- Dialog Content -->
                <v-card-text class="pa-0">
                    <v-container fluid grid-list-lg>
                        <v-form ref="form"  @submit.prevent="save" lazy-validation>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12 p-1 class="font-weight-regular bluegrey--text">
                                    You are about to {{ !item.is_archived ? 'archive' : 'restore'}} <span class="font-weight-bold black--text">"{{ item.name }}"</span> coverage plan. Are you sure you want to do this?
                                </v-flex>
                            </v-layout>
                            <input type="submit" ref="submit_button" class="hide-submit">
                        </v-form>
                    </v-container>
                </v-card-text>
                <!-- Dialog Buttons -->
                <v-card-actions
                color="red"
                class="headline primary"
                primary-title
                >
                    <v-spacer></v-spacer>
                    <v-btn color="red lighten-3" small flat @click="close" :disabled="loading">No</v-btn>
                    <v-btn color="blue lighten-3" small flat @click.native="submit" :disabled="loading">Yes</v-btn>
                </v-card-actions>
                <v-card-actions class="grey lighten-3 pa-0 ma-0" v-if="loading">
                    <v-progress-linear slot="progress" color="primary" class="pa-0 ma-0" indeterminate></v-progress-linear>
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
          default: 'archiveCoveragePlan'
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
            axios.patch(`/api/coverage_plan/${this.item.id}`, this.item)
            .then(response => {
                EventBus.$emit(this.eventName, response)
                this.toaster(response.data.data, response.status)
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
            this.dialog = true
            this.reloadTable()
        }
    },
    mounted () {
    }
  }
</script>