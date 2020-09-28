<template>
    <div class="top-holder">
        <v-autocomplete 
            v-model="form.charges" 
            color="accent" 
            :items="charges" 
            label="Charges" 
            outline 
        ></v-autocomplete>
         <div></div>
        <currency-format 
            v-model="form.covered_amount" 
            color="accent" 
            label="Amount Covered"
            outline 
            reverse
        >
        </currency-format>
        <v-text-field color="accent" v-model="form.limit_per_year" label="Limit per year" outline></v-text-field>
        <div class="checkbox-holder">
            <v-checkbox v-model="form.per_confinement"></v-checkbox>
            <currency-format 
                color="accent" 
                label="Per Confinement" 
                v-model="form.per_confinement_amount" 
                :disabled="!form.per_confinement" 
                outline
                reverse
            >
            </currency-format>
            <!-- <v-text-field color="accent" label="Per Confinement" v-model="form.per_confinement_amount" :disabled="!form.per_confinement" outline></v-text-field> -->
        </div>
        <div class="checkbox-holder">
            <v-checkbox v-model="form.per_month"></v-checkbox>
            <v-text-field color="accent" label="Limit per Month" v-model="form.limit_per_month" :disabled="!form.per_month" outline></v-text-field>
        </div>
    </div>
</template>

<script>
import { coveragePlanData } from '../../js/CoveragePlanStore';

export default {
    data: () => ({
        form:{},
        charges : [
            'Covered',
            'Charged to MBL'
        ], 
    }),
    props:{
        for_MBL:{
            type:String,
            default: 'Covered'
        }
    },
    watch : {
        chargeMBL: function(newValue,oldValue){
            this.chargeMBL = newValue;
        }
    },
    computed:{
        chargeMBL:{
            get:function(){
                return this.form.charges = this.for_MBL
            },
            set:function(newValue){
                return newValue;
            }
        }
    },
}
</script>

<style lang="scss">
    .top-holder {
        border: 1px solid #ededed;
        padding: 15px;
        display: grid;
        background-color: #ffffff;
        grid-template-columns: repeat(2, 1fr);
        grid-column-gap: 50px;
        margin-bottom: 10px;

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
</style>
