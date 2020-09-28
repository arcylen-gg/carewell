<template>
    <div class="import-collection__plan">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <v-form ref="form_submit" @submit.prevent="save" lazy-validation>
            <v-dialog
                v-model="loading_save"
                persistent
                width="300"
            >
                <v-card
                    color="primary"
                    dark
                >
                    <v-card-text>
                    {{loadingText}}
                    <v-progress-linear
                        indeterminate
                        color="white"
                        class="mb-0"
                    ></v-progress-linear>
                    </v-card-text>
                </v-card>
            </v-dialog>
        <div class="top-holder-one">
             <div class="top-holder-two">
                <v-text-field slot="activator" v-model="items.reference_number" label="Reference Number" append-icon="reorder" readonly outline></v-text-field>
                <div class="pull-right">
                    <v-btn color="accent" depressed type="submit" ref="submit_button"  class="mb-2 pull-right float-right">Save</v-btn>
                </div>
            </div>
            <v-autocomplete readonly :items="company" v-model="items.company_id" color="accent" item-value="id" item-text="name" label="Select Company" outline></v-autocomplete>
            <div class="top-holder-two">
                <v-autocomplete readonly :items="payment_mode" v-model="items.payment_mode_id" color="accent"  item-value="id" item-text="name" label="Select Payment Mode" outline></v-autocomplete>
                <v-autocomplete 
                :items="payment_term" 
                v-model="items.payment_term_id" 
                color="accent" 
                item-value="id" 
                item-text="name" 
                label="Terms of Payment" 
                readonly 
                outline>
                </v-autocomplete>
                <date-format readonly v-model="items.payment_cal_date" color="accent" label="Cal Date" outline></date-format>
                <date-format readonly v-model="items.payment_due_date" color="accent" label="Due Date" outline></date-format>
                <date-format readonly v-model="items.payment_start_date" color="accent" label="Payment Start" outline></date-format>
                <date-format readonly v-model="items.payment_end_date" color="accent" label="Payment End" outline></date-format>
            </div>
        </div>

        <div class="top-holder-one">            
            <v-container grid-list-md text-xs-center>
                <v-layout row wrap>
                    <v-flex xs12 >
                        <v-flex xs6 class="import-drag-drop">
                            <v-icon large>mdi-cloud-upload</v-icon><br>
                           <input type="file" id="fileUpload" ref="fileUpload" @change="handleFileUpload"/>
                        </v-flex>
                        <v-flex xs6 class="import-generate">
                            <v-progress-circular :rotate="-90" :size="70" :width="15" :value="loading_value" color="success"> {{loading_value}}% </v-progress-circular><br>
                        <!-- <v-btn color="primary" @click="importData" :loading="loading">Generate Import</v-btn> -->
                        <v-btn color="primary" @click="upload" :loading="loading">Generate Import</v-btn>
                        </v-flex>
                    </v-flex >
                </v-layout>
            </v-container>
        </div> 
            <template>
              <v-data-table
                :headers="headers"
                :items="import_item"
                :pagination.sync="pagination"
                class="elevation-1"
                align='center'
              >
                <template slot="headerCell" slot-scope="props">
                  <v-tooltip bottom>
                    <span slot="activator">
                      {{ props.header.text }}
                    </span>
                    <span>
                      {{ props.header.text }}
                    </span>
                  </v-tooltip>
                </template>
                <template slot="items" slot-scope="props">
                    <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.color != 'red' ? 'SUCCESS' : 'FAIL' }}</strong></td>
                    <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.saving_status }}</strong></td>
                    <td class="text-xs-center">{{ props.item.universal_id }}</td>
                    <td class="text-xs-center">{{ props.item.last_name }}</td>
                    <td class="text-xs-center">{{ props.item.first_name }}</td>
                    <td class="text-xs-center">{{ props.item.middle_name }}</td>
                    <td class="text-xs-center">{{ props.item.birth_date | mixin_change_date_format }}</td>
                    <td class="text-xs-center">{{ props.item.coverage_plan }}</td>
                    <td class="text-xs-center">{{ props.item.deployment }}</td>
                    <td class="text-xs-center">{{ props.item.payment_mode }}</td>
                    <td class="text-xs-center">{{ props.item.payment_amount }}</td>
                    <td class="text-xs-center">{{ props.item.status }}</td>
                </template>
              </v-data-table>
            </template>
        </v-form>
        </div>
</template>

<script>
    export default {
        data: () => ({
            form : {
            },
            loading_value : 0,
            loading: false,
            loading_save : false,
            breadcrumbs: [
                { text: 'Dashboard', disabled: false }, 
                { text: 'Billing Center', disabled: false }, 
                { text: 'Create CAL', disabled: true } 
            ],
            items : {},
            pagination : {
                rowsPerPage : -1,
            },
            headers: [
                {text: 'Status', value: 'status'},
                {text: 'Remarks', value: 'remarks'}, 
                {text: 'Universal ID', value: 'universal_id'},
                {text: 'Last Name',value: 'last_name'}, 
                {text: 'First Name',value: 'first_name'}, 
                {text: 'Middle Name',value: 'middle_name'}, 
                {text: 'Birth Date',value: 'birth_date'}, 
                {text: 'Coverage Plan',value: 'coverage_plan'}, 
                {text: 'Deployment',value: 'deployment'}, 
                {text: 'Payment Mode',value: 'payment_mode'},
                {text: 'Payment Amount',value: 'payment_amount'}, 
                {text: 'Member Status',value: 'member_status'},
            ],
            imported_file: [],
            import_item: [],
            payment_mode: [],
            company:[],
            filters:{},
            payment_term: [],
            loadingText: 'Loading Data...'
        }),
        methods : {
            upload(){
                if(this.file)
                {
                    let file = this.$refs.fileUpload;

                    const check_correct_file_format_xlsx = file.files[0].name.toLowerCase().includes('.xlsx');
                    const check_correct_file_format_csv = file.files[0].name.toLowerCase().includes('.csv');

                    if(check_correct_file_format_xlsx || check_correct_file_format_csv)
                    {
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

                this.importData(excelRows);
            },
            handleFileUpload(){
                this.loading_value = 0;
                this.import_item = [];
                this.file = this.$refs.fileUpload.files[0];
            },
            async importData(rows){
                this.loading = true;
                
                let ctr = 0;
                for await (let row of rows)
                {
                    row.cal_data = this.items;
                    console.log(row,'data'+ ctr)
                    await axios.post('api/import/cal_member', row)
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
            exportData(){
                //this.$axios.defaults.baseURL+"/api/export_file/diagnosis_list"
                window.open(this.$axios.defaults.baseURL+"/api/export_file/cal_member","_blank");
            },
            showData() {
                this.loading = true
                //this.clearData()
                axios.get(`/api/cal/${this.$route.query.id}`)
                .then(response=> {
                    this.items = response.data
                    this.loading = false
                })
                .catch(error => {
                    console.log(error)
                })
            },
            axiosGet(){
                this.filters.is_archived = 0
                this.getPaymentMode(this.filters)
                this.getCompany(this.filters)
                this.getPaymentTerm()
            },
            getPaymentMode(filters) {
                axios.get('api/payment_mode'+this.generateFilterURL(filters))
                .then(response => {
                    this.payment_mode = response.data
                })
                .catch(error => {
                })
                .finally(() => {
                })
            },
            getCompany(filters) {
                axios.get('api/company'+this.generateFilterURL(filters))
                .then(response => {
                    this.company = response.data.data.data
                })
                .catch(error => {
                })
                .finally(() => {
                })
            },
            getPaymentTerm() {
                // for ( let i = 0; i <= 31; i++)
                // {
                //     if(i != 0){
                //         this.payment_term.push({days: i, text: i + (i == 1 ? ' day' : ' days')});
                //     }
                // }
                let filters = {
                    is_archived : 0,
                    showAll: 1
                }
                axios.get('/api/payment-term'+this.generateFilterURL(filters))
                .then(response => {
                    // console.log(response,'response')
                    this.payment_term = response.data.data
                    // console.log(this.payment_term)
                })
                .catch(error => {
                    console.log(error.response,'afas')
                })
            },
            async save(){
                var a = false;
                if(this.import_item.length == 0)
                {
                    this.toaster('Please import member to proceed.', 300)
                }
                else
                {
                    for( var i = 0; i < this.import_item.length; i++){ 
                        if ( this.import_item[i].color == 'green')
                        {
                            a = true;
                        }
                    }

                    if(a == false)
                    {
                        this.toaster('Imported member is invalid!', 300)
                    }
                    else
                    {
                        const {id : cal_id} = this.items;
                        this.items.import_item = this.import_item
                        let response_data = "";
                        let response_status = "";
                        this.loading = true;
                        this.loading_save = true;
                        this.loadingText = "Saving Data...";
                        //delete existing member in cal
                        const deleteData = await axios.delete(`/api/cal_member/${cal_id}`);
                        
                        let imports = this.items.import_item;
                        console.log(this.items)
                        for(const arr of imports)
                        {
                            if(arr.color == "green")
                            {
                                arr.cal_id = cal_id;
                                await axios.post('/api/cal_member', arr)
                                .then(response=>{
                                    response_data = response.data;
                                    response_status = response.status;
                                })
                            }
                        }
                        this.toaster(response_data, response_status)
                        this.$refs.form_submit.reset()
                        this.redirectIfNotExisting()
                        // this.get_count();
                    }
                }
          
             },
             redirectIfNotExisting()
             {
                 this.$router.push('/billing-&-collection-center')
             },
        },
        beforeDestroy () 
        {
            clearInterval(this.interval)
        },
        mounted () 
        {
            this.showData()
            this.axiosGet()
        }
    }
</script>

<style lang="scss">
    @import "../../../scss/app.scss";

    .import-collection__plan {
        .title {
            font-family: 'Montserrat', sans-serif !important;
        }


        .top-holder-one 
        {
            border: 1px solid #ededed;
            padding: 15px;
            display: grid;
            background-color: #ffffff;
            grid-template-columns: repeat(1, 1fr);
            grid-column-gap: 50px;
            margin-bottom: 10px;
            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .top-holder-two {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-column-gap: 50px;
            margin-bottom: 10px;

            .v-text-field {
                font-size: 15px;

                .v-input__slot {
                    border: 1px solid #919191 !important;
                }
            }

            .v-text-field__details {
                margin-bottom: 0px !important;
            }

            .v-input--radio-group {
                margin-top: 0px !important;

                .v-input__slot {
                    margin-bottom: 0px !important;
                }

            }
        }

        }

        .bottom-holder {
            padding: 15px;
            border: 1px solid #ededed;

            .benefits-holder {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-column-gap: 50px;
                grid-row-gap: 15px;
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
    }

    
</style>