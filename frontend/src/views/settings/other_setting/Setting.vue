<template> 
    <div class="admin-panel" v-if="route">
        <v-btn @click="save" dark>SAVE</v-btn>
        <accounting-period ref="accountingPeriod"></accounting-period>
        <other-setting-try></other-setting-try>
        
    </div>
</template>

<script>
import AccountingPeriod from './accounting_period/SettingAccountingPeriod';
import OtherSettingTry from './accounting_period/OtherSettingTry';
import { settingData } from './SettingStore.js';
    export default {
        components : {
            AccountingPeriod,
            OtherSettingTry
        },
        data: () => ({
            route : null,
            form : {},
        }),
        methods : {
            setAccountingPeriod(data){
                let accountingPeriod = {
                    date_from : data.convertedDateFrom,
                    date_to : data.convertedDateTo,
                    payment_mode : data.form.mode_of_payment
                }
                settingData.commit('accountingPeriod',accountingPeriod);
            },
            save(){
                this.setAccountingPeriod(this.$refs.accountingPeriod);
                // console.log(this.$refs.accountingPeriod,'refs');
                console.log(settingData.state.form)
            }
        },
        mounted(){
            this.route = this.get_page_access(this.pages, this.$route.name)
        },
    
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";
</style>