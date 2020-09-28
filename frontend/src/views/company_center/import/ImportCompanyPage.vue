<template>
    <div class="import-company">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="import-company__menu"> 
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <v-btn @click="exportData" color="success">Download Template</v-btn>
        </div>
        <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                <v-flex xs12 >
                    <v-flex xs6 class="import-drag-drop">

                        <v-icon large>mdi-cloud-upload</v-icon><br>
                        <input type="file" id="file" ref="file" @change="handleFileUpload"/>
                        <!-- Drag & Drop Files -->
                    </v-flex>
                    <v-flex xs6 class="import-generate">
                        <v-progress-circular :rotate="-90" :size="70" :width="15" :value="loading_value" color="success"> {{loading_value}}% </v-progress-circular><br>
                        <v-btn color="primary" @click="importData">Generate Import</v-btn>
                    </v-flex>
                </v-flex >
            </v-layout>
        </v-container>
        <v-flex >
          
        </v-flex>
        <v-data-table :headers="headers" :items="import_item" must-sort :pagination.sync="sorting">
            <template slot="items" slot-scope="props">
                <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.color != 'red' ? 'SUCCESS' : 'FAIL' }}</strong></td>
                <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.remarks }}</strong></td>
                <td class="text-xs-center">{{ props.item.name }}</td>
                <td class="text-xs-center">{{ props.item.email }}</td>
                <td class="text-xs-center">{{ props.item.code }}</td>
                <td class="text-xs-center">{{ props.item.contact_number }}</td>
                <td class="text-xs-center">{{ props.item.account_type }}</td>
                <td class="text-xs-center">{{ props.item.address }}</td>
                <td class="text-xs-center">{{ props.item.policy_effective_date }}</td>
                <td class="text-xs-center">{{ props.item.policy_expiry_date }}</td>
                <td class="text-xs-center">{{ props.item.contact_person_firstname }}</td>
                <td class="text-xs-center">{{ props.item.contact_person_middlename }}</td>
                <td class="text-xs-center">{{ props.item.contact_person_lastname }}</td>
                <td class="text-xs-center">{{ props.item.contact_person_position }}</td>
                <td class="text-xs-center">{{ props.item.contact_person_contact_number }}</td>
                <td class="text-xs-center">{{ props.item.coverage_plan }}</td>
                <td class="text-xs-center">{{ props.item.deployment }}</td>
            </template>
            <template slot="no-data">
                <v-alert :value="true" color="error" icon="warning">
                    Sorry, nothing to display here :(
                </v-alert>
            </template>
            <template slot="footer">
                <td :colspan="headers.length">
                    <v-btn block dark large @click="exportFailData"><strong>EXPORT FAIL DATA</strong></v-btn>
                </td>
            </template>
        </v-data-table>
    </div>
</template>
<script>
export default {
    data : ()=> ({
        files: [],
            file: '',
            fileUpload : null,
            breadcrumbs: [
                {text: 'Company Center',disabled: false,}, 
                {text: 'Import Company',disabled: true,}
            ],
            uploadPercentage: 0,
            loading_value : 0,
            headers: [
                {text: 'STATUS',value: 'status',sortable: false}, 
                {text: 'REMARKS',value: 'remarks'}, 
                {text: 'NAME',value: 'name'}, 
                {text: 'EMAIL',value: 'email'}, 
                {text: 'CODE',value:  'code'}, 
                {text: 'CONTACT NUMBER',value: 'contact_number'},
                {text: 'ACCOUNT TYPE', vlaue: 'account_type'},
                {text: 'ADDRESS',value: 'address'}, 
                {text: 'POLICY EFFECTIVE DATE', value: 'policy_effective_date'},
                {text: 'POLICY EXPIRY DATE', value: 'policy_expiry_date'},
                {text: 'CONTACT PERSON FIRSTNAME',value: 'contact_person_firstname'}, 
                {text: 'CONTACT PERSON MIDDLENAME',value:  'contact_person_middlename'}, 
                {text: 'CONTACT PERSON LASTNAME',value: 'contact_person_lastname'}, 
                {text: 'CONTACT PERSON POSITION',value:'contact_person_position'}, 
                {text: 'CONTACT PERSON CONTACT NUMBER',value: 'contact_person_contact_number'},
                {text: 'COVERAGE PLAN',value: 'coverage_plan'}, 
                {text: 'DEPLOYMENT',value: 'deployment'},
            ],
            sorting: {
                descending: true,
                rowsPerPage: -1,
                sortBy: 'remarks',
            },
            import_item:[],
    }),
    methods : {
        exportFailData(){
            if(this.import_item.length != 0)
            {
                var list = [];
                var failData = this.import_item.filter(failData => failData.color === 'red')
                console.log(failData);
                // // failData = JSON.stringify(failData);
                // failData.forEach((data,key)=>{
                //     list.push(Object.values(data))
                // })
                // // failData = JSON.stringify(failData);
                // console.log(list)
                // let formData = new FormData();
                // formData.append('fail_data', JSON.stringify(list));
                // axios.post('api/export_file/fail',
                //     formData,
                //     {
                //         headers: {
                //             'Content-Type': 'multipart/form-data'
                //         },
                //     })
                // .then(response=>{
                //     console.log(response)
                // })
                // .catch(error=>{

                // })
            }
        },
        handleFileUpload(){
            this.loading_value = 0;
            this.import_item = [];
            this.file = this.$refs.file.files[0];
        },
        async importData(){
            if(this.file){
                let formData = new FormData();
                let ctr = 0;
                formData.append('file', this.file);

                let array = [];
                await axios.post('api/import/company/list', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )
                .then(response=>{
                    array = response.data;
                })

                for(const arr of array)
                {
                    arr.cal_data = this.items;
                    await axios.post('api/import/company', arr)
                    .then(response=>{
                        ctr += 1;
                        this.loading_value = parseInt((ctr / array.length) * 100);
                        this.loading = false;
                        this.import_item.push(response.data);
                    })
                }
                this.get_count();
                // axios.post('api/import/company',
                //     formData,
                //     {
                //         headers: {
                //             'Content-Type': 'multipart/form-data'
                //         },
                //         onUploadProgress: function( progressEvent ) {
                //             this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
                //         }.bind(this)
                //     })
                // .then(response=>{
                //     this.loading_value = this.uploadPercentage
                //     this.import_item = response.data[0];
                //     console.log(this.import_item, "imports")
                //     this.get_count();
                // })
                // .catch(error => {
                //     console.log(error.response)
                //     // this.toaster(error.response.message, error.response.status)
                // })
                // .finally(()=>{
        
                // })
            }
            else
            {
                alert('No File To Upload.');
            }
            
        },
        exportData(){
            window.open(this.$axios.defaults.baseURL+"/api/export/company","_blank");
        },

        downloadTemplate(){
            alert("No function");
        }
    }
}
</script>


<style lang="scss">
    @import "../../../scss/app.scss";


    .import-company
    {
        .v-breadcrumbs li a {
        font-size: 16px; 
        }
        .import-company__menu
        {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            justify-content: center;
            align-self: center;
            .v-btn
            {
                height: 40px;
            }
            :nth-child(2)
            {
                .v-input__slot
                {
                    width: 80%;
                    margin-left: auto;
                }
            }
            :nth-child(3), :nth-child(4)
            {
                justify-self: end;
            }
            .v-text-field 
            {
                font-size: 15px;
                .v-input__slot {
                    border: 1px solid #ced4da;
                }
            }
        }
        .import-drag-drop
        {
            border: dashed 3px #222C3C;
            width: 50%;
            padding : 30px !important;
            text-align : center;
            font-weight : bold;
            float : left;
            cursor : pointer;
            position : relative;
        }
        .import-generate
        {
            width : 50%;
            float : right;
            text-align : center;            
            position : relative;
        }
        
        table
        {
            thead
            {
                tr
                {
                    th
                    {
                        text-align: center !important;
                    }
                }
            }
        }
    }
</style>
