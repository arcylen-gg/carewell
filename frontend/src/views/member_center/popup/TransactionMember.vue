<template>
    <v-dialog v-model="dialog" scrollable max-width="950px" lazy>
        <v-btn class="ml-0 btn-holder--width" color="success" depressed slot="activator" @click="loadDialog">
            <v-icon class="mr-3">mdi-rotate-3d</v-icon> Transaction History
        </v-btn>
        <v-card class="transaction-member">
            <v-card-title class="title" style="background-color: #303E55; color: #fff;">Transaction</v-card-title>
            <v-card-text>
                <v-tabs dark slot="extension" color="primary" grow>
                    <v-tabs-slider color="#DF7E00"></v-tabs-slider>
                    <v-tab v-for="item in procedure_items" :key="item.TabTitle">
                        {{ item.TabTitle }}
                    </v-tab>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <div class="payment-history">
                                    <member-payment-history :item="payment_history" :select_company="select_company"></member-payment-history>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <div class="availment-history">
                                    <member-availment-history :memberData="memberData" :item="availment_history" :select_company="select_company" :select_benefit="select_benefit"></member-availment-history>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                </v-tabs>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import MemberPaymentHistory from './sub_component/MemberPaymentHistory';
import MemberAvailmentHistory from './sub_component/MemberAvailmentHistory';
    export default {
        components : {
            MemberPaymentHistory,
            MemberAvailmentHistory
        },
        data() {
            return {
                dialog: false,
                procedure_items: [
                    {TabTitle: 'Payment History'}, 
                    {TabTitle: 'Availment History'}
                ],
                payment_history: [],
                availment_history: [],
                select_company: [],
                select_benefit: [],
                member_id : {},
                memberData:{},
            }
        },
        computed: {
        },
        props : {
            item_member_id: {
                type: Number
            }
        },  
        watch: {
            availment_menu(val) {
                val && this.$nextTick(() => (this.$refs.availment_picker.activePicker = 'YEAR'))
            },
        },
        methods: {
            async loadDialog(){
                const {data : memberTransaction} = await axios.get('/api/show/member/transaction-history/'+this.item_member_id);
                console.log({memberTransaction})
                
                this.availment_history = await memberTransaction.availment;

                this.payment_history = await memberTransaction.cal_member;

                this.member_id = this.item_member_id;

                this.memberData = {
                    id : this.item_member_id,
                    full_name : memberTransaction.fullname
                }

                this.loadCompany();

                this.loadBenefits();
            },
            async loadCompany(){
                let memberCompanyAPI = await axios.get('/api/member_company/'+this.item_member_id)
                let getCompany = await memberCompanyAPI.data.map(item => {
                    return {
                        id: item.company.id,
                        name: item.company.name,
                    }
                });
                this.select_company = [{id:'all', name:'All'},...getCompany];
            },
            async loadBenefits(){
                let benefitAPI = await this.getBenefits();
                let getBenefitType = await benefitAPI.data.data.data.map(item => {
                    return {
                        id: item.id,
                        name: item.name
                    }
                });
                this.select_benefit = [{id:'all', name:'All'},...getBenefitType];
            },
            getBenefits(){
                let filters = {
                    is_archived : 0
                }

                return axios.get('/api/benefit_type'+this.generateFilterURL(filters))
            },
            availment_save(date) {
                this.$refs.availment_menu.save(date)
            },
        },
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .transaction-member {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }

        .v-card {
            border: 1px solid #ededed;
        }

        .payment-history {
            .payment-history__menu {
                display: grid;
                grid-template-columns: repeat(3, 1fr);

                .v-text-field {
                    font-size: 15px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                    }
                }

                .v-btn {
                    height: 40px;
                }
            }

            table {
                thead {
                    tr {
                        th {
                            text-align: center !important;
                        }
                    }
                }
            }
        }

        .availment-history {
            .availment-history__menu {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-column-gap: 10px;

                .v-btn {
                    height: 40px;
                }

                .v-text-field {
                    font-size: 13px;

                    .v-input__slot {
                        border: 1px solid #919191 !important;
                    }
                }
            }
        }

    }
</style>