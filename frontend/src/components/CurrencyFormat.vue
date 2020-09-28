<template>
    <span>
        <v-text-field 
            v-bind="$attrs" 
            v-model="currencyComputed" 
            @change="getValue(currency.real)"
        />
    </span>
</template>
<script>
export default {
    data: () => ({
        currency : {
            real:'', //currency.real are the one to put in database
            edit:'', //currency.edit are just use for showing
        },
    }),
    props:{
        value:{
            //accept either string or number props
            type: [String,Number],
            default: '0'
        },
    },
    computed:{
        currencyComputed:{
            get:function(){
                return this.currency.edit
            },
            set:function(newValue){
                let decimalPoint = parseFloat(newValue.replace(/,/g,'')).toFixed(2)
                this.currency.real = decimalPoint
                return decimalPoint;
            }
        },
    },
    watch: {
        value : {
            handler:function(newValue,oldValue){
                this.value = newValue;
                this.checkValue(this.value);
            },
            immediate: true
        },
    },
    methods:{
        getValue(num){
            this.$emit('input', num)
            let convert = parseFloat(num);
            this.currency.edit = convert.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,");
        },
        checkValue(value){
            if(isNaN(value))
            {
                value = '0';
            }
            this.currency.real = value;
            this.getValue(this.currency.real);
        },
    },
}
</script>
