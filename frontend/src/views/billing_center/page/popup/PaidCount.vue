<template>
    <span>
        <v-btn color="accent" slot="activator" @click="loadDialog" style="cursor: pointer">
            <slot></slot>
        </v-btn>
        <v-dialog v-model="dialog" scrollable persistent max-width="850px">
            <v-card>
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;">Period Count - {{formTitle}}</v-card-title>
                <v-form ref="form_submit" lazy-validation>
                    <!-- <div class="date_column" v-for="(paidData,key) in form" :key="key"> -->
                    <v-data-table
                        :headers="headers"
                        :items="form"
                        hide-actions
                    >
                        <template slot="items" slot-scope="props">
                            <td class="mx-3 mt-4">
                                <v-menu 
                                    v-model="paid_start_menu[props.index]"
                                    :close-on-content-click="false" 
                                    :nudge-right="120" 
                                    :disabled="!locations"
                                    lazy transition="scale-transition" 
                                    offset-y 
                                    full-width 
                                    min-width="290px"
                                >
                                    <date-format 
                                        slot="activator"  
                                        :rules="requiredRules" 
                                        v-model="form[props.index].start_paid"
                                        :default="form[props.index].start_paid"
                                        color="accent" 
                                        label="Start" 
                                        append-icon="event" 
                                        readonly 
                                        outline
                                    ></date-format>
                                    <v-date-picker 
                                        ref="paid_count_picker" 
                                        min="1950-01-01"
                                        v-model="form[props.index].start_paid"
                                        @input="paid_start_val = false"
                                    />
                                </v-menu>
                            </td>
                            <td class="mx-3 mt-4">
                                <v-menu 
                                    v-model="paid_end_menu[props.index]"
                                    :close-on-content-click="false"
                                    :nudge-right="120" 
                                    :disabled="!locations"
                                    lazy transition="scale-transition" 
                                    offset-y 
                                    full-width 
                                    min-width="290px"
                                >
                                    <date-format 
                                        slot="activator"  
                                        :rules="requiredRules" 
                                        v-model="form[props.index].end_paid"
                                        :default="form[props.index].end_paid"
                                        color="accent" 
                                        label="End" 
                                        append-icon="event" 
                                        readonly 
                                        outline
                                    ></date-format>
                                    <v-date-picker 
                                        ref="paid_count_picker" 
                                        min="1950-01-01"
                                        v-model="form[props.index].end_paid"
                                        @input="paid_end_val = false"
                                    />
                                </v-menu>
                            </td>
                            <td class="mx-3 mt-4">
                                <currency-format 
                                    v-model="form[props.index].amount"
                                    outline
                                    label="Amount"
                                    reverse
                                    readonly
                                    :rules="requiredRules"
                                />
                            </td>
                        </template>
                    </v-data-table>
                    <!-- </div> -->
                </v-form>
                <span></span>
                <v-card-actions class="form-bgcolor">
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close" v-if="locations">CANCEL</v-btn>
                    <v-btn color="accent" depressed @click="save" v-if="locations">UPDATE</v-btn>
                    <v-btn color="accent" depressed @click="close" v-if="!locations">CLOSE</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
export default {
    data : () => ({
        formTitle: '',
        member_data : [],
        dialog : false,
        paid_start_val : false,
        paid_start_menu : [],
        paid_end_val :false,
        paid_end_menu : [],
        requiredRules: [
            v => !!v || "Input is required"
        ],
        form : [{
            start_paid : '',
            end_paid : '',
            amount : 0
        }],
        headers: [
            { text: 'Start Date', sortable: false , align: 'center' } ,
            { text: 'End Date', sortable: false , align: 'center' } ,
            { text: 'Amount', sortable: false , align: 'center' },
        ],
        is_data_exist : true,
    }),
    props : {
        item : {
            Type : Object,
        },
        paid_count : {
            Type : Number,
        },
        member_id : {
            Type : Number,
        },
        locations : {
            Type : Boolean,
            default : true,
        }
    },
    methods : {
        async loadDialog(){
            this.dialog = true
            await this.setMemberData(this.member_id);
            await this.setPaidCount();
        },
        setMemberData(id){
            let array = [];
            this.item.forEach((arr,ind) => {
                if(arr.member.id == id)
                {
                    array.push(arr)
                }
            });
            this.member_data = array;
            this.formTitle = this.member_data[0].member.fullname;
        },
        async setPaidCount(){
            let arrays = [];
            
            const { data : dataPaidDate } = await axios.get(`/api/cal_paid_count/${this.member_data[0].cal_id}`);
            let info = dataPaidDate;
            console.log(this.member_data)
            info.forEach((data,ind) => {
                if(data.cal_member_id === this.member_data[0].id)
                {
                    arrays.push({
                        start_paid : data.start_date,
                        end_paid : data.end_date,
                        amount : data.amount
                    });
                }
            });

            if(arrays.length === 0)
            {
                this.is_data_exist = false;
                for(let i = 0; i < this.paid_count; i++)
                {
                    arrays.push({
                        start_paid : '',
                        end_paid : '',
                        amount : this.member_data[0].monthly_premium
                    });
                }
            }
            this.form = arrays;
        },
        save(){
            if(this.$refs.form_submit.validate())
            {
                this.member_data[0].period_count_date = this.form

                if(this.is_data_exist)
                {
                    axios.put(`/api/cal_paid_count/${this.member_data[0].cal_id}`,this.member_data[0])
                    .then(response =>{
                        this.toaster(response.data,response.status)
                        EventBus.$emit('updatePaidCount', response)
                    })
                    .catch(error => {
                        this.toaster(error.response.data, error.response.status)
                    })

                    this.close();
                }
                else
                {
                    axios.post('/api/cal_paid_count', this.member_data[0])
                    .then(response => {
                        this.toaster(response.data,response.status)
                        EventBus.$emit('createPaidCount', response)
                    })
                    .catch(error => {
                        this.toaster(error.response.data, error.response.status)
                    })
                    
                    this.close();
                }
            }
        },
        close(){
            this.dialog = false
        },
        mounted(){
            
        }
    }
}
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";
    .date_column{
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        justify-content: center;
        align-self: center;
    }
    .form-bgcolor{
        background-color: #fff;
    }
</style>