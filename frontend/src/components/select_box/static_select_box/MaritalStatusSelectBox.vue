<template>
    <span>
        <v-select 
            v-bind="$attrs"
            label="Marital Status"  
            v-model="selectData"
            :items="['Single', 'Married', 'Divorced', 'Separated', 'Widowed']" 
            @input="$emit('input',$event)"
        />
    </span>
</template>
<script>
export default {
    data: () => ({
        selectData: null
    }),
    watch:{
        '$attrs.default':{
            handler:function(newValue,oldValue){
                this.selectData = newValue
            },
            immediate: true
        }
    }
}
</script>
<style lang="scss">

</style>