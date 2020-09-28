<template>
    <span class="top-holder">
        <v-text-field v-model="form.name" color="accent" label="Plan Name" outline></v-text-field>
        <currency-format 
            v-model="form.monthly_premium" 
            color="accent" 
            label="Monthly Premium"
            outline 
            reverse
        >
        </currency-format>
        <v-layout class="my-0 py-0">
            <v-flex md-5 py-0>
                <v-text-field type="number" v-model="form.age_bracket_from" :value="form.age_bracket_from" color="accent" label="Age Bracket - From" outline></v-text-field>
            </v-flex>
            <v-flex md-2 py-0 class="align-center justify-center layout">
                <div class="title pb-3">-</div>
            </v-flex>
            <v-flex md-5 py-0>
                <v-text-field type="number" v-model="form.age_bracket_to" color="accent" label="Age Bracket - To" outline></v-text-field>
            </v-flex>
        </v-layout>
        <currency-format 
                v-model="form.handling_fee" 
                color="accent" 
                label="Handling Fee"
                outline 
                reverse
            >
            </currency-format>

            <currency-format 
                v-model="form.processing_fee" 
                color="accent" 
                label="Processing Fee"
                outline 
                reverse
            >
            </currency-format>

            <currency-format 
                v-model="form.card_fee" 
                color="accent" 
                label="Card Fee"
                outline 
                reverse
            >
            </currency-format>

            <currency-format 
                v-model="form.hospital_income_benefit" 
                color="accent" 
                label="Hospital Income Benefits (HIB)"
                outline 
                reverse
            >
            </currency-format>

            <global-select-box
                v-model="form.pre_existing_id"
                api-link="/api/pre-existing"
                item-value="id" 
                item-text="name" 
                label="Pre-Existing" 
                outline
                is-load
                :filters="{is_archived:0}"
                :default="form.pre_existing_id"
            >
            </global-select-box>

            <currency-format 
                v-model="form.annual_benefit_limit" 
                color="accent" 
                label="Annual Benefit Limit (ABL)"
                outline 
                reverse
            >
            </currency-format>

            <currency-format 
                v-model="form.maximum_benefit_limit" 
                color="accent" 
                label="Maximum Benefit Limit (MBL)"
                outline 
                reverse
            >
            </currency-format>

            <v-text-field 
                color="accent"
                v-model="form.member_type"
                label="Member Type"
                outline
            >
            </v-text-field>

            <v-radio-group v-model="form.max_limit_per" row @change="getMaxLimitPer">
                <v-radio label="Year" value="1"></v-radio>
                <v-radio label="Illness / Disease" value="2"></v-radio>
            </v-radio-group>
    </span>
</template>

<script>
import { coveragePlanData } from '../js/CoveragePlanStore';

export default {
    data: () => ({
        form:{
            max_limit_per: "1",
            name: '',
            age_bracket_from: 0,
            age_bracket_to: 1,
        },
    }),
    methods:{
        getMaxLimitPer(){
            //max_limit_per_illness_or_year
            let max_limit_per = this.form.max_limit_per - 1; //subtracted by 1 because of this will be the array key
            coveragePlanData.commit('getMaxLimitPerIllnessOrYear',max_limit_per);
        }
    }
}
</script>

<style lang="scss" scoped>
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

