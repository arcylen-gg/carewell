<template>
    <v-dialog v-model="popup_benefit_schedule" scrollable max-width="950px" lazy>
        <v-btn class="ml-0" color="success" depressed slot="activator" style="width: 100%; height: 40px;">Image Schedule</v-btn>
        <v-card class="benefit-schedule">
            <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Benefit of Schedule</v-card-title>
            <v-card-text>
                <div class="schedule-table">
                    <v-data-table :headers="schedule_headers" :items="schedule_items" class="elevation-1">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left py-2"><img :src="props.item.image_contract" style="height: 100px;"></td>
                            <td class="text-xs-left">
                                <v-btn color="info" depressed dark>{{ props.item.action }}</v-btn>
                            </td>
                        </template>
                    </v-data-table>
                </div>
            </v-card-text>
             <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="tertiary" depressed dark @click="popup_benefit_schedule = false">CLOSE</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data() {
            return {
                popup_benefit_schedule: false,
                schedule_headers: [{
                        text: 'IMAGE BENEFIT OF SCHEDULE',
                        value: 'image_contract'
                    },
                    {
                        text: 'ACTION',
                        value: 'action'
                    },
                ],
                schedule_items: [{
                        value: false,
                        image_contract: require('../../../assets/carewell-logo.jpg'),
                        action: 'Delete',
                    },
                ],
            }
        },
        watch: {
            availment_menu(val) {
                val && this.$nextTick(() => (this.$refs.availment_picker.activePicker = 'YEAR'))
            },
        },
        methods: {
            availment_save(date) {
                this.$refs.availment_menu.save(date)
            },
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .benefit-schedule {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .v-card {
            border: 1px solid #ededed;
        }
    }
</style>