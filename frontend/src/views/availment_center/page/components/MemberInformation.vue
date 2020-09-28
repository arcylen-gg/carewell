<template>
    <span>
        <!-- <v-form ref="form" lazy-validation> -->
            <v-label><h3>Member Information</h3></v-label>
            <div class="top-holder-two">
                <div class="mx-0 px-0">
                    <v-flex xs10 class="col-six-l" style="text-align:center !important;">
                        <div class="top-holder-two">
                            <v-text-field
                                v-model="search" 
                                label="Search Name, Universal ID, Carewell ID"
                                color="accent" 
                                :disabled="for_viewing" 
                                outline
                            ></v-text-field>
                            <v-btn 
                                @click="searchData(search)"
                                :disabled="for_viewing"
                                color="accent"
                            >
                                SEARCH
                            </v-btn>
                        </div>
                        <v-autocomplete
                            v-model="form.member" 
                            :rules="requiredRules" 
                            :items="memberList"
                            :disabled="for_viewing"
                            :loading="loading"
                            color="accent" 
                            item-value="id" 
                            item-text="memberConcatIDs"
                            label="Select Member"
                            outline
                            return-object
                            @change="selectOrdering"
                        >
                        </v-autocomplete>
                        <transaction-member 
                            v-if="form.member"
                            :item_member_id="transactionData.id"
                        />
                    </v-flex>
                </div>
                <div>
                    <v-flex xs12 class="col-six-l">  
                        <v-label>Universal ID :<span>&ensp;</span>{{ form.member ? form.member.company.universal_id : '' }}</v-label><br>
                        <v-label>Carewell ID :<span>&ensp;</span>{{ form.member ? form.member.company.carewell_id : '' }}</v-label><br>
                        <v-label>Company :<span>&ensp;</span>{{ form.member ? form.member.company.company.name : '' }}</v-label><br>
                        <v-label>Deployment :<span>&ensp;</span>{{form.member ? form.member.company.company_deployment.name : '' }}</v-label><br>
                        <v-label>Birthdate :<span>&ensp;</span>{{ form.member ? form.member.birth_date : '' }}</v-label><br>
                        <v-label>Age :<span>&ensp;</span>{{ form.member ? form.member.age + " years old" : '' }}</v-label>
                    </v-flex>
                </div>
            </div>
        <!-- </v-form> -->
    </span>
</template>
<script>
import { availmentData } from '../../mixins/AvailmentStore.js';
import AvailmentAutoSaveMixins from '../../mixins/AvailmentAutoSaveMixins'; 

export default {
    mixins : [ AvailmentAutoSaveMixins ],
    data : () => ({
        form : {},
        memberList : [],
        requiredRules: [
            v => !!v || "Input is required"
        ],
        for_viewing: false,
        transactionData : {},
        loading: false,
        search: ''
    }),
    methods : {
        getMember(statusValue = 0){
            let filters = {
                is_archived : statusValue,
                showAll: 1
            }
            this.axiosMember(filters)
        },
        async axiosMember(filters){
            this.loading = true;

            const { data : { data : members } } =  await axios.get('api/member'+this.generateFilterURL(filters));
            
            this.memberList = members;

            this.memberList.map((data,key) => {
                data.disabled = false;
                if(data.is_archived)
                {
                    delete this.memberList[key];
                }
                else
                {
                    if(!data.birth_date || data.company.carewell_id === null || data.company.carewell_id === "")
                    {
                        data.disabled = true;
                    }

                    // if(!data.birth_date)
                    // {
                    //     data.disabled = true;
                    // }

                    data.memberConcatIDs = data.fullname + ' | Universal ID: ' + (data.birth_date ? data.company.universal_id : 'No Birth date') + ' |' + ' Carewell ID: ' + (data.company.carewell_id ? data.company.carewell_id : 'No Carewell ID');
                }
            });
            
            this.loading = await false;
        },
        async memberSelect(){
            const { company: { coverage_plan_id : id } } = this.form.member;

            const  { data : { coverage_plan_benefits : benefit_type } } = await axios.get(`/api/select/availment/member/coverage-plan/${id}/benefit-type`);

            this.form.member.benefit_type = benefit_type; 
        
            availmentData.dispatch('getSelectedMember',this.form.member);

            this.transactionData = this.form.member;
        },
        async memberAvailment(){
            
            let id = this.form.member.id;

            let member_coverage_plan_id = this.form.member.company.coverage_plan_id; 

            const { data : { availment } } = await axios.get(`/api/show/availment/member/${id}/availment`);
            
            let availment_data = availment;

            let arrayData = [];

            availment_data.forEach((arr,ind) => {
                if(arr.coverage_plan_id == member_coverage_plan_id)
                {
                    arrayData.push(arr)
                }
            });

            availmentData.dispatch('getSelectedMemberAvailment',arrayData);
        },
        selectOrdering(){
            console.log(this.form.member)
            this.memberSelect();
            this.memberAvailment();
            this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.form,'memberInformation');
        },
        async searchData(search){
            this.loading = true;
            let filters = {
                is_archived : 0,
                name : search
            }
            const {data : members} = await axios.get('/api/select/availment/member'+this.generateFilterURL(filters));
            console.log(members)
            await this.setMemberSelectOptions(members);

            this.loading = await false;
        },
        async setMemberSelectOptions(members){
            this.memberList = members;

            this.memberList.map((data,key) => {
                data.disabled = false;
                if(data.is_archived)
                {
                    delete this.memberList[key];
                }
                else
                {
                    if(!data.birth_date || data.company.carewell_id === null || data.company.carewell_id === "")
                    {
                        data.disabled = true;
                    }

                    // if(!data.birth_date)
                    // {
                    //     data.disabled = true;
                    // }

                    data.memberConcatIDs = data.fullname + ' | Universal ID: ' + (data.birth_date ? data.company.universal_id : 'No Birth date') + ' |' + ' Carewell ID: ' + (data.company.carewell_id ? data.company.carewell_id : 'No Carewell ID');
                }
            });
        }
    },
    mounted() {
        // this.getMember();
        availmentData.dispatch('viewingAvailment');
        this.for_viewing = availmentData.state.form.viewingAvailment;
    },
    updated() {
        //commented because everytime I input on the search member bar this always run
        this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.form,'memberInformation');
    }
}
</script>
