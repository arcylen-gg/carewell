export default {
    methods : {
        async REPORT_MIXINS_GET_DATA(reportType, exportType='index'){
            // add this.filters.limit if you want to limit result

            // see web.php for this route

            // reportType must be same as the class name that is going to use
            // if class name is consist of 2 or more words separate it using '-'

            // exportType show how data is presented (index, excel, pdf, php)
            console.log(exportType,reportType)
            if((this.filters.date_from && this.filters.date_to) && (!this.filters.filter_by || this.filters.filter_by != 'Custom'))
            {
                this.filters.filter_by = "Custom";
            }

            if(exportType == "index")
            {
                const data = await axios.get(`report/${reportType}/${exportType}`+this.generateFilterURL(this.filters));
                this.companies = data.data;
                this.policy_data = data.data;
            }
            else
            {
                window.open(this.$axios.defaults.baseURL+`/report/${reportType}/${exportType}`+this.generateFilterURL(this.filters),"_blank");
            }
        },

        async loadDialog(){
            const data = await axios.get('report/company');
            this.companies = data.data
            console.log(data)
        },

        async clearDatePicker(){
            if(this.filters.filter_by != 'Custom')
            {
                this.filters.date_from = null;
                this.filters.date_to = null;
            }
        },
        async axiosGet(filters, reportType, exportType='index'){
            this.filters = filters;
            this.REPORT_MIXINS_GET_DATA(reportType, exportType)
        },
    }
}