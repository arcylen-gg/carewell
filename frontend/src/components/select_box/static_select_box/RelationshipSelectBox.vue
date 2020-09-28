<template>
    <span>
        <v-select 
            v-bind="$attrs"
            label="Relationship"  
            v-model="selectData"
            :items="relationship" 
            @input="$emit('input',$event)"
        />
    </span>
</template>
<script>
export default {
    data: () => ({
        selectData: null,
        relationship:['Mother', 'Father', 'Child', 'Spouse', 'Uncle', 'Aunt', 'Brother', 'Sister',
            'Grandfather', 'Grandmother', 'Nephew', 'Niece', 'Cousin'
        ],
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
