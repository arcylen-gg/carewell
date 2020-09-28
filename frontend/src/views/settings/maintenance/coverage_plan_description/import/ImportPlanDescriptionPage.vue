<template>
    <div class="import-coverage-plan-description">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="import-coverage-description__menu">
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <v-btn color="success" @click="exportData">Download Template</v-btn>
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
        <!-- <div class="import-coverage-description__menu">
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <v-btn color="success">Download Template</v-btn>
        </div> -->
        <!-- <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                <v-flex xs12 md12 lg12> -->
                    <!-- <v-flex xs6 class="import-drag-drop">
                        <v-icon large>mdi-cloud-upload</v-icon><br>
                        Drag & Drop Files
                    </v-flex> -->
    <!--                 <v-flex xs6 md6 lg6 class="import-generate">
                        <v-btn color="success" @click="downloadTemplate">Download Provider Template</v-btn>
                        <v-btn color="success" @click="exportData">Download Template</v-btn>
                        <input type="file" id="file" ref="file" @change="handleFileUpload"/>
                        <v-progress-circular :rotate="-90" :size="70" :width="15" :value="loading_value" color="success"> {{loading_value}}%</v-progress-circular><br>
                        <v-btn color="primary" @click="importData">Generate Import</v-btn>
                    </v-flex>
                </v-flex >
            </v-layout>
        </v-container> -->
        <v-flex >
          
        </v-flex>
        <v-data-table :headers="headers" :items="import_item">
            <template slot="items" slot-scope="props">
                <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.color != 'red' ? 'SUCCESS' : 'FAIL' }}</strong></td>
                <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.remarks }}</strong></td>
                <td class="text-xs-center">{{ props.item.procedure_type}}</td>
                <td class="text-xs-center">{{ props.item.name}}</td>
            </template>
            <template slot="no-data">
                <v-alert :value="true" color="error" icon="warning">
                    Sorry, nothing to display here :(
                </v-alert>
            </template>
            <template slot="footer" class="hidden">
                <td :colspan="headers.length">
                    <!-- <v-btn block dark large @click="exportFailData"><strong>EXPORT FAIL DATA</strong></v-btn> -->
                </td>
            </template>
        </v-data-table>
        </div>
</template>

<script>
    export default 
    {
        data: () => ({
            breadcrumbs: [{
                text: 'Settings',
                disabled: false,
            }, {
                text: 'Maintenance',
                disabled: false,
            },
            {
                text: 'Import Coverage Plan Description',
                disabled: true,
            }],
            interval : {},
            loading_value : 0,
            headers: [
                { text: 'STATUS',          value: 'status'},
                { text: 'REMARKS',         value: 'remarks'}, 
                { text: 'PROCEDURES TYPE', value: 'procedures_type_id'}, 
                { text: 'NAME',            value: 'name'}
            ],
            import_item :[],

        }),
        methods : {
            exportFailData(){
                if(this.import_item.length != 0)
                {
                    var failData = this.import_item.filter(failData => failData.color === 'red')
                    if(failData){
                         window.open("http://carewellv2.test/api/upload/fail/procedure?data="+JSON.stringify(failData),"_blank");
                        // axios.post('api/upload/fail/procedure',failData)
                        //     .then(response=>{
                        //     this.loading_value = this.uploadPercentage
                        //     this.import_item = response.data[0];
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
                    // var list = [];
                    // //failData = JSON.stringify(failData);
                    // failData.forEach((data,key)=>{
                    //     list.push(Object.values(data))
                    // })
                    //  failData = JSON.stringify(failData);
                    // console.log(list)
                    // let formData = new FormData();
                    // formData.append('fail_data', JSON.stringify(list));
                    // axios.post('api/upload/fail/procedure',
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
            importData(){
                if(this.file){
                    let formData = new FormData();
                    formData.append('file', this.file);
                    axios.post('api/import/procedure',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            },
                            onUploadProgress: function( progressEvent ) {
                                this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
                            }.bind(this)
                        }
                        )
                    .then(response=>{
                        this.loading_value = this.uploadPercentage
                        this.import_item = response.data[0];
                        console.log(this.import_item);
                    })
                    .catch(error => {
                        console.log(error.response)
                        // this.toaster(error.response.message, error.response.status)
                    })
                    .finally(()=>{
                        this.get_procedure_types();
                    })
                }
                else
                {
                    alert('No File To Upload.');
                }
                
            },
            exportData(){
                window.open(this.$axios.defaults.baseURL+"/api/export_file/procedure","_blank");
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
    @import "../../../../../scss/app.scss";


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
