<template>
    <span>
         <v-autocomplete
            v-bind="$attrs"
            v-model="selectData"
            :items="selectOptions"
            @change="$emit('input',$event)"
        />
    </span>
</template>
<script>
export default {
    data: () => ({
        selectData: null,
        selectOptions:[],
        returnData: null,
    }),
    props:{
        apiLink:{
            type: String,
            // default: '/api/bank'
            required:true
        },
        filters:{
            //use this filters object to search from database
            //use table column name as object key
            type: Object,
            default: () => {
                return {showAll:1}
            }
        },
        addAll:{
            //add "All" option in select box
            type: Boolean,
            default: false,
        },
        isLoad:{
            //isLoad is set to false to avoid multiple request at once
            //one request will be done if set to true
            type: Boolean,
            default: false
        }
    },
    watch:{
        isLoad:{
            handler: function(newValue,oldValue) {
                if(newValue)
                {
                    this.getData();
                }
                // this.$nextTick(()=>{
                //     this.selectData = this.$attrs.default 
                // })
                // console.log(this.$attrs.default,'$attrs')
                // this.selectData = this.$attrs.default 
            },
            immediate: true
        },
        '$attrs.default':{
            handler: function(newValue,oldValue){
                // console.log({newValue,oldValue})
                this.selectData = newValue
            },
            immediate: true
        }
    },
    methods:{
        axiosData(){
            this.filters.showAll = 1;
            return axios.get(this.apiLink+this.generateFilterURL(this.filters));
        },
        async getData(){
            let dataAPI = await this.axiosData();

            await this.getSelectData(dataAPI); //this function return value for this.returnData
            let response =  await this.returnData

            if(this.addAll)
            {
                let addAllResponse = [{[this.$attrs['item-value']]:'all',[this.$attrs['item-text']]:'All'},...response];
                this.selectOptions = await addAllResponse;
                return;
            }
            return this.selectOptions = await response;
        },
        getSelectData(value = []){
            //search value if have key == "data"
            if(value.hasOwnProperty('data'))
            {
                //continue searching on the object for the key == "data"
                const {data} = value;
                this.getSelectData(data)
                return;
            }
            return this.returnData = value; //return data if key data cannot be found
        }
    },
}
</script>
<style lang="scss">

</style>
