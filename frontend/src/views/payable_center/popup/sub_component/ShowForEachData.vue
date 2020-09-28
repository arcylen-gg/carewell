<template>
    <span>
        <div v-for="(value,index) in obj" :key="index">
            {{value}}
        </div>
    </span>
</template>

<script>
export default {
    data : () => ({
        obj:{}
    }),
    props : {
        forEachData:{
            type: Object,
            default: {}
        }
    },
    methods: {
        getData(){
            let data = this.forEachData.obj;
            let affected = this.forEachData.propertyAffected;

            let mapData = data.map(item => {
                if(item.hasOwnProperty(affected))
                {
                    let dataValue = item[affected];
                    if(dataValue.hasOwnProperty('name'))
                    {
                        return dataValue.name;
                    }
                    else if(dataValue.hasOwnProperty('first_name') && dataValue.hasOwnProperty('last_name'))
                    {
                        return `${dataValue.first_name} ${dataValue.last_name}`;
                    }
                    else
                    {
                        return '';
                    }
                }
                else
                {
                    return '';
                }
            })

            this.obj = mapData;
    
        }
    },
    mounted(){
        this.getData();
    }
}
</script>
