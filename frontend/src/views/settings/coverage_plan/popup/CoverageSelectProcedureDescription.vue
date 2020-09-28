<template>
    <div class="top-holder">
        <v-autocomplete color="accent" :key="for_MBL" v-model="form.charges" :items="charges" label="Charges" outline ></v-autocomplete>
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
            <v-text-field color="accent" label="Per Confinement" v-model="form.per_confinement_amount" :disabled="!form.per_confinement" outline></v-text-field>
        </div>
        <div class="checkbox-holder">
            <v-checkbox v-model="form.per_month"></v-checkbox>
            <v-text-field color="accent" label="Limit per Month" v-model="form.limit_per_month" :disabled="!form.per_month" outline></v-text-field>
        </div>
    </div>
</template>

<script>
export default {
    data : () => ({
        charges : ['Covered', 'Charged to MBL'], 
        requiredRules: [
             v => !!v || 'Input is required',
        ],
    }),
    props : {
        for_MBL:{
            type: Number,
            default: 0
        },
        form: {
            type: Object,
            default: () => {
                return {
                }
            }
        },
    },
    watch : {
        for_MBL: function (newValue, oldValue) {
            // console.log({newValue, oldValue})
            this.form.charges = newValue == 1 ? "Covered" : "Charged to MBL";
        }
    },
    methods : {
        
    },
}
</script>
