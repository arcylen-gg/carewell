<template>
    <input 
        type="text" 
        v-model="amount" 
        @input="emitData('input')" 
        @change="emitData('change')"
        v-bind="$attrs"
    />
</template>
 
<script>
// Note:
// CurrencyFormatV2 (<currecy-format-v2></currecy-format-v2>) is used for text field that use currency
// as input. It differ from CurrencyFormat (<currency-format></currency-format>) because CurrencyFormatV2 
// accepts @change event.
//
// You can add class, style or other textbox properties to the component because 
// $attrs are responsible for that properties
//
// Use default_amount props to set default amount to the component

export default {
    data : () => ({
        amount : 0,
    }),
    props:{
        default_amount:{
            type:[String, Number],
            default: 0
        }
    },
    methods : {
        emitData(method){
            let amount = parseFloat(this.amount);

            if(method === 'change'){
                this.amount = this.currencyFormat(amount);
            }

            this.$emit(method,amount)
        },
        currencyFormat(value){
            return parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")
        }
    },
    created(){
        this.amount = this.currencyFormat(this.default_amount);
    }
}
</script>