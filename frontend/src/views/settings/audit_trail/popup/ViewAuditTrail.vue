<template>
    <span>
        <span @click="loadDialog">
            <slot></slot>
        </span>
        <v-dialog v-model="dialog" persistent scrollable max-width="700px">
            <v-card>
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">View Audit Trail</v-card-title>
                <v-card-text>
                    <v-data-table
                        :items="get_all"
                        hide-actions
                        hide-headers
                    >
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left" nowrap><h6>{{removeSpecialChar[props.index]}}  : </h6></td>
                        <td class="text-xs-left" v-if="props.item.new !== props.item.old" nowrap>
                            <strong>
                                <span v-if="Array.isArray(props.item.old)">
                                    <span v-for="(arrData,key) in props.item.old" :key="key">
                                        {{arrData[arrayVal].name}}<br>
                                    </span>
                                </span>
                                <span v-else>
                                    {{props.item.old}}
                                    <span style="color:red"><sup> Old</sup></span>
                                </span>
                            </strong>
                        </td>
                        <td class="text-xs-left" nowrap>
                            <strong>
                                <span v-if="Array.isArray(props.item.new)">
                                    <span v-for="(arrData,key) in props.item.new" :key="key">
                                        {{arrData[arrayVal].name}}<br>
                                    </span>
                                </span>
                                <span v-else>
                                    <span v-if="props.item.new === props.item.old">
                                        {{props.item.new}}
                                        <span style="color:blue"><sup> Original</sup></span>
                                    </span>
                                    <span v-else>
                                        {{props.item.new}}
                                        <span style="color:green"><sup> New</sup></span>
                                    </span>
                                </span>
                             </strong>
                        </td>
                        </template>
                    </v-data-table>
                    
                </v-card-text>
                 <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>
<script>
export default {
    data: () => ({
        dialog:false,
        get_data:[],
        get_new_data: [],
        get_old_data: [],
        get_all: [],
        arrayVal: 'provider',
    }),
    props:{
        form:{
            type:Object
        }
    },
    computed:{
        removeSpecialChar:function(){
            return this.get_data.map(arr => arr.replace(/_/g," ").toUpperCase());
        }
    },
    methods:{
        loadDialog(){
            let data = JSON.parse(this.form.new_data);
            data = this.deleteObject(data);
            console.log(this.form)

            this.get_data = Object.getOwnPropertyNames(data);
            this.get_new_data = Object.values(data);
            console.log(this.get_data)
            console.log(this.get_new_data)
            
            this.getOldData(); 
            this.getAll();
            console.log(this.get_all)
            this.dialog = true;
        },
        getOldData(){
            if(this.form.old_data == null)
            {
                let data = this.get_data.forEach((data,key)=>{
                    this.get_old_data.push('');
                })
            }
            else
            {
                let old_data = JSON.parse(this.form.old_data);
                old_data = this.deleteObject(old_data);
                this.get_old_data = Object.values(old_data)
            }
        },
        getAll(){
            let data = this.get_data.forEach((data,key)=>{
                if(Array.isArray(this.get_new_data[key]) || typeof this.get_new_data[key] === 'object')
                {
                    let oldName = [];
                    let remarked = this.form.remarks;
                    if(remarked.match(/Added.*/) || remarked.match(/Create.*/))
                    {
                        if(this.get_old_data[key] != "")
                        {
                            this.get_old_data[key].forEach((arr,ind)=>{
                                oldName.push(arr.name)
                            })
                        }
                    }
                    else
                    {
                        oldName = this.getArrayName(this.get_old_data[key],oldName,this.form.remarks);
                    }

                    let newName = [];
                    newName = this.getArrayName(this.get_new_data[key],newName,this.form.remarks);

                    this.get_all.push({
                        new:newName.toString(),
                        old:oldName.toString()
                    })
                }
                else
                {
                    this.get_all.push({
                        new:this.get_new_data[key],
                        old:this.get_old_data[key]
                    })
                }
            })
        },
        getInnerName(arrayName,condition){
            let innerName = '';
            if(condition === "Added Member" || condition === "Update Member" || condition === "Archive Member" || condition === "Restore Member")
            {
                innerName = arrayName.company.name;
            }
            return innerName
        },
        getArrayName(arrayName,arrayPush,condition){
            if(typeof arrayName !== 'object' || Array.isArray(arrayName))
            {
                arrayName.forEach((arr,ind)=>{
                    if(arr.name != null)
                    {arrayPush.push(arr.name)}
                    else
                    {arrayPush.push("N/A")}
                })
            }
            else
            {
                if(arrayName !== null)
                {
                    if(typeof arrayName.name === 'undefined')
                    {
                        arrayPush.push(this.getInnerName(arrayName,condition))
                    }
                    else
                    {
                        arrayPush.push(arrayName.name)
                    }
                }
                else
                {
                    arrayPush.push("N/A")
                }
            }
            return arrayPush
        },
        close(){
            this.get_data = [],
            this.get_new_data = [],
            this.get_old_data = [],
            this.get_all = [],
            this.dialog = false;
        },
        deleteObject(data){
            let deletedData = data;
            delete deletedData.id;
            delete deletedData.is_archived;
            delete deletedData.created_at;
            delete deletedData.updated_at;
            delete deletedData.crypt;
            delete deletedData.user_position_id;
            delete deletedData.is_default;
            delete deletedData.availment;
            delete deletedData.cal_member;
            delete deletedData.current_balance;
            delete deletedData.member_company;
            delete deletedData.doctor_id;
            delete deletedData.member_id;
            delete deletedData.company_id;
            delete deletedData.provider_id;
            delete deletedData.diagnosis_id;
            delete deletedData.benefit_type_id;
            delete deletedData.provider_payee_id;
            delete deletedData.files;
            delete deletedData.count;
            delete deletedData.procedure_type_id;
            delete deletedData.age_bracket;
            delete deletedData.is_used_in_company;
            delete deletedData.doctor_providers;
            
            return deletedData;
        },
    },
    mounted(){

    }
}
</script>

