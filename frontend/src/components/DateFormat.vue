<template>
    <v-text-field
        v-bind="$attrs"
        v-model="formatedDate"
    ></v-text-field>
</template>

<script>
export default {
    data: () => ({
        date:''
    }),
    computed:{
        formatedDate:{
            get: function(){
                return this.dateFormat();
            },
            set: function(){
                return this.dateFormat();
            }
        }
    },
    props:{
        value:{
            type:String,
            default:''
        }
    },
    watch:{
        value:{
            handler:function(newValue,oldValue) {
                this.date = newValue;
            },
            immediate:true
        },
        '$attrs.default':{
            handler: function(newValue,oldValue){
                this.date = newValue
            },
            immediate: true
        }
    },
    methods:{
        dateFormat(){
            let date = this.date;

            if(!date) return '';

            let splitDate = date.split('-');

            if(splitDate.length == 1)
            {
                return splitDate[0];
            }

            return `${splitDate[1]}/${splitDate[2]}/${splitDate[0]}`;
        }
    }
}
</script>
