<template>
    <div class="import-coverage-plan-description">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="import-coverage-description__menu">
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <download-template-member>Download Template</download-template-member>
        </div>
        <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                <v-flex xs12 >
                    <v-flex xs6 class="import-drag-drop">

                        <v-icon large>mdi-cloud-upload</v-icon><br>
                        <!-- <input type="file" id="file" ref="file" @change="handleFileUpload"/> -->
                        <!-- Drag & Drop Files -->
                        <input type="file" id="fileUpload" ref="fileUpload" @change="handleFileUpload" :disabled="loading"/>
                        <!-- <input type="button" id="upload" value="Upload" @click="upload()" /> -->

                    </v-flex>
                    <v-flex xs6 class="import-generate">
                        <v-progress-circular :rotate="-90" :size="70" :width="15" :value="loading_value" color="success"> {{loading_value}}% </v-progress-circular><br>
                        <!-- <v-btn color="primary" @click="importData">Generate Import</v-btn> -->
                        <v-btn color="primary" @click="upload" :loading="loading">Generate Import</v-btn>
                    </v-flex>
                </v-flex >
            </v-layout>
        </v-container>
        <v-flex >
          
        </v-flex>
        <v-data-table 
            :headers="headers" 
            :items="import_item" 
            must-sort 
            :pagination.sync="sorting">
            <template slot="items" slot-scope="props">
                <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.color != 'red' ? 'SUCCESS' : 'FAIL' }}</strong></td>
                <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.remarks }}</strong></td>
                <td class="text-xs-center">{{ props.item.universal_id }}</td>
                <td class="text-xs-center">{{ props.item.carewell_id }}</td>
                <td class="text-xs-center">{{ props.item.first_name }}</td>
                <td class="text-xs-center">{{ props.item.middle_name }}</td>
                <td class="text-xs-center">{{ props.item.last_name }}</td>
                <td class="text-xs-center">{{ props.item.mothers_maiden_name }}</td>
                <td class="text-xs-center">{{ props.item.date | mixin_change_date_format }}</td>
                <td class="text-xs-center">{{ props.item.gender }}</td>
                <td class="text-xs-center">{{ props.item.marital_status }}</td>
                <td class="text-xs-center">{{ props.item.contact_number }}</td>
                <td class="text-xs-center">{{ props.item.email }}</td>
                <td class="text-xs-center">{{ props.item.permanent_address }}</td>
                <td class="text-xs-center">{{ props.item.present_address }}</td>
                <td class="text-xs-center">{{ props.item.company }}</td>
                <td class="text-xs-center">{{ props.item.company_deployment }}</td>
                <td class="text-xs-center">{{ props.item.payment_mode }}</td>
                <td class="text-xs-center">{{ props.item.coverage_plan }}</td>
                <td class="text-xs-center">{{ props.item.sss_number }}</td>
                <td class="text-xs-center">{{ props.item.tin }}</td>
                <td class="text-xs-center">{{ props.item.philhealth_number }}</td>
                <td class="text-xs-center">{{ props.item.pagibig_number }}</td>
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
        data: () => ({
            files: [],
            file: '',
            fileUpload : null,
            breadcrumbs: [
                {text: 'Member Center',disabled: false,}, 
                {text: 'Import Member',disabled: true,}
            ],
            uploadPercentage: 0,
            loading_value : 0,
            loading : false,
            headers: [
                {text: 'STATUS',value: 'status',sortable: false}, 
                {text: 'REMARKS',value: 'remarks'}, 
                {text: 'UNIVERSAL ID',value: 'universal_id'}, 
                {text: 'CAREWELL ID',value: 'carewell_id'}, 
                {text: 'FIRST NAME',value:  'first_name'}, 
                {text: 'MIDDLE NAME',value: 'middle_name'}, 
                {text: 'LAST NAME',value: 'last_name'}, 
                {text: "MOTHER'S MAIDEN NAME",value:  'mothers_maiden_name'}, 
                {text: 'BIRTHDATE',value: 'birth_date'}, 
                {text: 'GENDER',value:'gender'}, 
                {text: 'MARITAL STATUS',value: 'marital_status'}, 
                {text: 'CONTACT NUMBER',value: 'contact_number'},
                {text: 'EMAIL',value: 'email'}, 
                {text: 'PERMANENT ADDRESS',value: 'permanent_address'}, 
                {text: 'PRESENT ADDRESS',value: 'present_address'}, 
                {text: 'COMPANY',value: 'company_id'}, 
                {text: 'DEPLOYMENT',value: 'company_deployment_id'}, 
                {text: 'PAYMENT MODE',value: 'payment_mode_id'}, 
                {text: 'COVERAGE PLAN',value: 'coverage_plan_id'}, 
                {text: 'SSS',value: 'sss_number'}, 
                {text: 'TIN',value: 'tin'}, 
                {text: 'PHILHEALTH',value: 'philhealth_number'}, 
                {text: 'PAGIBIG',value: 'pagibig_number'}, 
            ],
            sorting: {
                descending: true,
                rowsPerPage: -1,
                sortBy: 'remarks',
            },
            import_item:[],
            excelData:{}
        }),
        methods : {
            async upload(){
                if(this.file)
                {
                    // 07/19/2019
                    // See this cdn on index.html
                    // src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.5/xlsx.full.min.js
                    // src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.5/jszip.js

                    //see this blog
                    //https://blog.teamtreehouse.com/reading-files-using-the-html5-filereader-api
                    let file = this.$refs.fileUpload;

                    const check_correct_file_format_xlsx = file.files[0].name.toLowerCase().includes('.xlsx');
                    const check_correct_file_format_csv = file.files[0].name.toLowerCase().includes('.csv');

                    if(check_correct_file_format_xlsx || check_correct_file_format_csv)
                    {
                        //see this blog
                        //https://stackoverflow.com/questions/11829537/html5-filereader-how-to-return-result
                        this.readFile(file,(e)=>{
                            this.getImportRows(e.target.result);
                        });   
                    }
                    else
                    {
                        alert('Wrong File Format.');
                    }
                }
                else 
                {
                    alert('No File To Upload.');
                }
               
            },
            readFile(file, onCallBack){
                let reader = new FileReader();
                reader.onload = onCallBack;
                reader.readAsBinaryString(file.files[0]);
            },
            async importAPI(rows){
                this.loading = true;

                let ctr = 0;
                for await (let row of rows)
                {
                    await axios.post('api/import/member', row)
                    // await axios.post('api/import/member-list/test', row)
                    .then(response => {
                        ctr += 1;

                        this.loading_value = parseInt((ctr / rows.length) * 100);
                        
                        this.import_item.push(response.data);
                    })
                    .catch(error => {
                        console.log({error})
                    })
                }

                this.loading = await false;
            },
            getImportRows(data){
                //Read the Excel File data.
                let workbook = XLSX.read(data, {
                    type: 'binary'
                });

                //Fetch the name of First Sheet.
                let firstSheet = workbook.SheetNames[0];

                //Read all rows from First Sheet into an JSON array.
                let excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[firstSheet]);

                console.log({excelRows});

                this.importAPI(excelRows);
            },
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
                // this.file = this.$refs.file.files[0];
                this.file = this.$refs.fileUpload.files[0];
            },
            async importData(){
                if(this.file){
                    let formData = new FormData();
                    let ctr = 0;
                    formData.append('file', this.file);

                    let array = [];
                    await axios.post('api/import/member_list', formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    )
                    .then(response=>{
                        array = response.data;
                    })

                    console.log(array)

                    for(const arr of array)
                    {
                        arr.cal_data = this.items;
                        await axios.post('api/import/member', arr)
                        .then(response=>{
                            ctr += 1;
                            this.loading_value = parseInt((ctr / array.length) * 100);
                            this.loading = false;
                            this.import_item.push(response.data);
                        })
                    }
                    // this.get_count();
                    
                    // axios.post('api/import/member',
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
                window.open(this.$axios.defaults.baseURL+"/api/export/member","_blank");
            },

            downloadTemplate(){
                alert("No function");
                // window.open("http://carewellv2.test/api/export_file/fail","_blank");
            }
        },
        beforeDestroy () 
        {
            clearInterval(this.interval)
        },
        mounted () 
        {
            
        },
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";


    .import-coverage-plan-description
    {
        .v-breadcrumbs li a {
        font-size: 16px;
        }
        .import-coverage-description__menu
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
