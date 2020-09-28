export default {
    methods : {
        // getCompany(status = 0){
        //     this.filters.is_archived = status
        //      axios.get('api/company'+this.generateFilterURL(this.filters))
        //     .then(response=>{
        //         this.select_company = response.data.data.data
        //     })
        //     .catch(error=>{

        //     })
        // },
    //     getMode(status = 0){
    //         axios.get('api/payment_mode')
    //        .then(response=>{
    //            this.select_payment = response.data
    //        })
    //        .catch(error=>{

    //        })
    //    },
       getDeployment(event){
            let deployment = [];
            deployment = [...this.form.company_ref_id.company_deployment]
            //  deployment = this.form.company_ref_id.company_deployment
            this.select_deployment =  deployment

            let coveragePlanArray = [];
            let coverage_plan = this.form.company_ref_id.company_coverage_plan.forEach((data,key)=>{
                coveragePlanArray.push(data.coverage_plan);
            })
            this.select_coverage_plan = coveragePlanArray
        },
        addRow(){
            var data = { full_name : null,
                        birth_date : null,
                        relationship : null, }
            this.list.push(data)
        },
        listIndex(index){
            let limit = 1
            if(this.list.length > limit)
                {
                this.list.splice(index, 1);
                }
                else
                {
                this.toaster(`Dependents cannot be less than ${limit}.`, 300);
                }
        },
        close() {
            // EventBus.$emit(this.eventName)
            this.dialog = false
        },
        presentAddress(event){
            this.form.present_address = event == true ? this.form.permanent_address : ''
        },
    }
}