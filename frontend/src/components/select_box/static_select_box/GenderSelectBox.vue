<template>
    <span>
        <v-select 
            v-bind="$attrs"
            label="Gender"  
            v-model="selectData"
            :items="['Male','Female']" 
            @input="$emit('input',$event)"
        />
    </span>
</template>
<script>
export default {
    data: () => ({
        selectData: null,
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
