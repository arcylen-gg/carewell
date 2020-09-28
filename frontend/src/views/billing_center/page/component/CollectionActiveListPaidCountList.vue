<template>
    <span>
        <template>
        <div class="cal_member_count">
            <div class="top_column">
                <h3>Number of Member : <strong>{{ member_count }}</strong></h3>
                <h3 class="ml-4">Total Payment Amount : <strong> {{ currency_format('₱',total_payment) }}</strong></h3>
            </div>
        </div>
        </template>
        <div></div>
        <div class="cal-member-table">
            <div class="bottom-column">
                <v-data-table
                    :headers="headers" 
                    :items="item" 
                    class="elevation-1"
                >
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left"> {{ props.item.universal_id }} </td>
                        <td class="text-xs-left"> {{ props.item.carewell_id }} </td>
                        <td class="text-xs-left"> {{ props.item.name }} </td>
                        <td class="text-xs-left"><paid-count-dialog ref="paidCountDialog" :item="calData.cal_member" :paid_count="props.item.period_count" :member_id="props.item.id" :locations="locations"> {{ props.item.period_count }} </paid-count-dialog></td>
                        <td class="text-xs-left"> {{ currency_format('₱',props.item.paid_amount) }} </td>
                    </template>
                </v-data-table>
            </div>
        </div>
    </span>
</template>

<script>
import paidCountDialog from '../popup/PaidCount'
export default {
    components:{
        paidCountDialog,
    },
    data : () => ({
        member_count : 0,
        total_payment : 0,
        paid_count : 0,
        headers : [
            {text : 'UNIVERSAL ID', value:'universal_id'},
            {text : 'CAREWELL ID', value:'carewell_id'},
            {text : 'NAME', value:'name'},
            {text : 'PERIOD COUNT', value:'period_count'},
            {text : 'PAID AMOUNT', value:'paid_amount'}
        ],
        item: [],
        calData: {},
        locations : window.location.pathname == '/billing-&-collection-center/edit' ? true : false,
    }),
    methods : {
        async showAPI(){
            const {data : apiData} = await axios.get(`/api/cal/${this.$route.query.id}`)
            this.calData = apiData;
            this.getData();
        },
        getData(){
            this.member_count = this.calData ? this.calData.cal_member_count : 0;
            this.total_payment = this.calData ? this.calData.total_amount_paid : 0;

            let arrayData = [];
            let memberRecord = this.calData.cal_member;
            console.log(memberRecord)
            memberRecord.forEach((data,ind) => {
                
                let dividend = Math.trunc(data.paid_amount / data.monthly_premium);
                this.paid_count = dividend;
                arrayData.push({
                    id: data.member_id,
                    universal_id : data.member.company.universal_id,
                    carewell_id : data.member.company.carewell_id,
                    name : data.member.fullname,
                    period_count : dividend,
                    paid_amount : data.paid_amount
                });
            });
            
            this.item = arrayData;
        },
        perModePayment(monthlyPremium, modePayment){
            if(modePayment == 1)
            {
                monthlyPremium = monthlyPremium / 2;
            }
            else if(modePayment == 2)
            {
                monthlyPremium = monthlyPremium;
            }
            else if(modePayment == 3)
            {
                monthlyPremium = monthlyPremium * 3;
            }
            else if(modePayment == 4)
            {
                monthlyPremium = monthlyPremium * 6;
            }
            else if(modePayment == 5)
            {
                monthlyPremium = monthlyPremium * 12;
            }
            return monthlyPremium;
        }
    },
    mounted(){
        this.showAPI();
    }
}
</script>


<style lang="scss">
    @import "../../../../scss/app.scss";

    .cal_member_count {
        .top_column {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            justify-content: center;
            align-self: center;
            padding : 1em;
            margin-bottom : 25px;
        }
    }
</style>
