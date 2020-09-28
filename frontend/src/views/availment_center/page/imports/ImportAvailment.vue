<template>
    <div class="import-availment">
        <v-breadcrumbs :items="breadcrumbs" class="px-0">
            <v-icon slot="divider">chevron_right</v-icon>
        </v-breadcrumbs>
        <div class="import-availment__menu">
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <v-label>&nbsp;</v-label>
            <download-availment-template>Download Template</download-availment-template>
        </div>
        <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                <v-flex xs12 >
                    <v-flex xs6 class="import-drag-drop">

                        <v-icon large>mdi-cloud-upload</v-icon><br>
                        <input type="file" id="fileUpload" ref="fileUpload" @change="handleFileUpload"/>
                        <!-- Drag & Drop Files -->
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
            :pagination.sync="pagination"
            must-sort 
        >
            <template slot="items" slot-scope="props">
                <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.color != 'red' ? 'SUCCESS' : 'FAIL' }}</strong></td>
                <td class="text-xs-center" nowrap :style="{color: props.item.color}"><strong>{{ props.item.remarks }}</strong></td>
                <td class="text-xs-center">{{ props.item.approval_id }}</td>
                <td class="text-xs-center">{{ props.item.caller_name }}</td>
                <td class="text-xs-center">{{ props.item.caller_date }}</td>
                <td class="text-xs-center">{{ props.item.first_name }}</td>
                <td class="text-xs-center">{{ props.item.middle_name }}</td>
                <td class="text-xs-center">{{ props.item.last_name }}</td>
                <td class="text-xs-center">{{ props.item.universal_id }}</td>
                <td class="text-xs-center">{{ props.item.carewell_id }}</td>
                <td class="text-xs-center">{{ props.item.company }}</td>
                <td class="text-xs-center">{{ props.item.deployment }}</td>
                <td class="text-xs-center">{{ props.item.provider }}</td>
                <td class="text-xs-center">{{ props.item.benefit_type }}</td>
                <td class="text-xs-center">{{ props.item.availment_date }}</td>
                <td class="text-xs-center">{{ props.item.discharged_date }}</td>
                <td class="text-xs-center">{{ props.item.chief_complaint }}</td>
                <td class="text-xs-center">{{ props.item.initial_diagnosis }}</td>
                <td class="text-xs-center">{{ props.item.final_diagnosis }}</td>
                <td class="text-xs-center">{{ props.item.charge_to }}</td>
                <td class="text-xs-center">{{ props.item.procedures }}</td>
                <!-- <td class="text-xs-center">{{ currency_format('₱',parseInt(props.item.gross_amount)) }}</td>
                <td class="text-xs-center">{{ currency_format('₱',props.item.phic_charity) }}</td>
                <td class="text-xs-center">{{ currency_format('₱',props.item.charged_to_patient) }}</td>
                <td class="text-xs-center">{{ currency_format('₱',props.item.charged_to_carewell) }}</td>
                <td class="text-xs-center">{{ currency_format('₱',props.item.charged_to_others) }}</td> -->
                <td class="text-xs-center">{{ props.item.gross_amount | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.phic_charity | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.charged_to_patient | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.charged_to_carewell | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.charged_to_others | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.procedure_remarks }}</td>
                <td class="text-xs-center">{{ props.item.disapproved }}</td>
                <td class="text-xs-center">{{ props.item.physician }}</td>
                <td class="text-xs-center">{{ props.item.specialization }}</td>
                <td class="text-xs-center">{{ props.item.rate_rvs }}</td>
                <td class="text-xs-center">{{ props.item.procedure }}</td>
                 <td class="text-xs-center">{{ props.item.actual_pf_charge | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.phys_phic_charity | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.phys_charged_to_patient | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.phys_charged_to_carewell | mixin_change_currency_format }}</td>
                <td class="text-xs-center">{{ props.item.provider_payee }}</td>
                <td class="text-xs-center">{{ props.item.hospital_fee }}</td>
                <td class="text-xs-center">{{ props.item.doctor }}</td>
                <td class="text-xs-center">{{ props.item.professional_fee }}</td>
                <!-- <td class="text-xs-center">{{ currency_format('₱',props.item.actual_pf_charge) }}</td>
                <td class="text-xs-center">{{ currency_format('₱',props.item.phys_phic_charity) }}</td>
                <td class="text-xs-center">{{ currency_format('₱',props.item.phys_charged_to_patient) }}</td>
                <td class="text-xs-center">{{ currency_format('₱',props.item.phys_charged_to_carewell) }}</td> -->
            </template>
            <template slot="no-data">
                <v-alert :value="true" color="error" icon="warning">
                    Sorry, nothing to display here :(
                </v-alert>
            </template>
        </v-data-table>
    </div>
</template>

<script>
export default {
    data: () => ({
        file: '',
        loading_value : 0,
        ctr_death : 0,
        loading: false,
        breadcrumbs: [
            { text: 'Availment Center', disabled: false }, 
            { text: 'Import Availment', disabled: true } 
        ],
        items : {},
        pagination : {
            rowsPerPage : -1,
        },
        headers: [
            {text: 'Status', value: 'status'},
            {text: 'Remarks', value: 'remarks'}, 
            {text: 'Approval ID', value: 'approval_id'},
            {text: 'Caller Name', value: 'caller_name'},
            {text: 'Caller Date', value: 'caller_date'},
            {text: 'First Name', value: 'first_name'},
            {text: 'Middle Name', value: 'middle_name'},
            {text: 'Last Name', value: 'last_name'},
            {text: 'Universal ID', value: 'universal_id'},
            {text: 'Carewell ID', value: 'carewell_id'},
            {text: 'Company', value: 'company'},
            {text: 'Deployment', value: 'deployment'},
            {text: 'Provider', value: 'provider'},
            {text: 'Benefit Type', value: 'benefit_type'},
            {text: 'Availment Date', value: 'availment_date'},
            {text: 'Discharged Date', value: 'discharged_date'},
            {text: 'Chief Complaint', value: 'chief_complaint'},
            {text: 'Initial Diagnosis', value: 'initial_diagnosis'},
            {text: 'Final Diagnosis', value: 'final_diagnosis'},
            {text: 'Diagnosis to Charge', value: 'charge_to'},
            {text: 'Procedures', value: 'procedures'},
            {text: 'Gross Amount', value: 'gross_amount'},
            {text: 'PHIC Charity/SWA Charge', value: 'phic_charity'},
            {text: 'Patient Charge', value: 'charged_to_patient'},
            {text: 'Carewell Charge', value: 'charged_to_carewell'},
            {text: 'Other Charge', value: 'charged_to_others'},
            {text: 'Procedure Remarks', value: 'remarks'},
            {text: 'Disapproved', value: 'disapproved'},
            {text: 'Physician', value: 'physician'},
            {text: 'Specialization', value: 'specialization'},
            {text: 'Rate/RVS', value: 'rate_rvs'},
            {text: 'Physician Procedures', value: 'procedure'},
            {text: 'Actual PF Charge', value: 'actual_pf_charge'},
            {text: 'PHIC Charity/SWA Charge', value: 'phys_phic_charity'},
            {text: 'Patient Charge', value: 'phys_charged_to_patient'},
            {text: 'Carewell Charge', value: 'phys_charged_to_carewell'},
            {text: 'Provider Payee', value: 'provider_payee'},
            {text: 'Hospital Fee', value: 'hospital_fee'},
            {text: 'Doctor', value: 'doctor'},
            {text: 'Professional Fee', value:'professional_fee'}
        ],
        imported_file: [],
        import_item: [],
        import_record: [],
    }),
    methods: {
        async upload(){
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
            console.log(this.import_item)
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
            // this.file = this.$refs.file.files[0];
            this.file = this.$refs.fileUpload.files[0];
        },
        async importData(rows){
            this.loading = true;
            let excelData = this.forEmptyCell(rows);
            console.log(excelData)
            let ctr = 0;
            let approvalID = "";
            let member_name = "";
            let death_row = 0;
            for await (let row of excelData)
            {
                await axios.post('api/import/availment', row)
                .then(response => {
                    console.log(response)
                    ctr += 1;
                    let death_procedure = "";
                    this.loading_value = parseInt((ctr / rows.length) * 100);
                    if(this.import_item.length != 0)
                    {
                        let availment_import = response.data;
                        this.import_item.forEach((array,ind) => {
                            array.middle_name = array.middle_name ? array.middle_name+' ' : '';
                            availment_import.middle_name = availment_import.middle_name ? availment_import.middle_name+' ' : '';
                            let avail_member = array.first_name+' '+array.middle_name+array.last_name;
                            let member_data = availment_import.first_name+' '+availment_import.middle_name+availment_import.last_name
                            if(availment_import.approval_id == array.approval_id && availment_import.color == "green" && availment_import.benefit_type == "Insurance Benefit Rider")
                            {
                                if(array.procedures == "NATURAL DEATH" || array.procedures == "ACCIDENTAL DEATH")
                                {
                                    death_row += 1;
                                }

                                if(death_row > 1)
                                {
                                    availment_import.color = 'red';
                                    availment_import.remarks = 'You can avail one death procedure.';
                                }
                            }
                            if(availment_import.approval_id != array.approval_id && availment_import.color == "green" && avail_member == member_data && array.benefit_type == "Insurance Benefit Rider")
                            {
                                 if(array.procedures == "NATURAL DEATH" || array.procedures == "ACCIDENTAL DEATH")
                                {
                                    this.ctr_death += 1;
                                }
                                death_procedure = array.procedures
                                if(this.ctr_death >= 1)
                                {
                                    availment_import.color = 'red';
                                    availment_import.remarks = avail_member +' already avail '+death_procedure+' procedure.';
                                }
                            }

                            if(avail_member != member_data)
                            {
                                this.ctr_death = 0;
                                death_row = 0;
                            }
                        });
                    }
                    console.log(response.data,'data')
                    this.import_item.push(response.data);
                })
                .catch(error => {
                    console.log({error})
                })
            }
            await this.import_item.forEach((arr,ind) => {
                if(arr.color == 'green')
                {
                    let caller_info = this.availmentArray('caller',arr);
                    let member_info = this.availmentArray('member', arr);
                    let availment_info = this.availmentArray('availment', arr);
                    let procedure_info = this.availmentArray('procedure', arr);
                    let physician_info = this.availmentArray('physician', arr);
                    let payee_info = this.availmentArray('payee', arr);
                    if(this.import_record.length != 0)
                    {
                        let search_availment = (availment) => availment.approval_id === arr.approval_id;
                        let search_procedure = (procedure) => procedure.procedures === arr.procedures;
                        let search_physician_procedures =  (doctor) => doctor.physician === arr.physician && doctor.procedure === arr.procedure; 
                        let availment_approval = this.import_record.find(search_availment);
                        if(!availment_approval)
                        {
                            this.import_record.push({
                                approval_id : arr.approval_id,
                                callerInformation : caller_info,
                                memberInformation : member_info,
                                coverage_plan : arr.coverage_plan,
                                availmentInformation : availment_info,
                                procedure_type : arr.plan_description,
                                procedureInformation : procedure_info,
                                physicianInformation : physician_info,
                                payeeInformation : payee_info
                            });
                        }
                        else
                        {
                            let availment_procedure = availment_approval.procedureInformation.find(search_procedure);
                            let availment_physician = availment_approval.physicianInformation.find(search_physician_procedures);
                            if(!availment_procedure)
                            {
                                availment_approval.procedureInformation.push({
                                    procedures : arr.procedures,
                                    gross_amount : arr.gross_amount,
                                    phic_charity : arr.phic_charity,
                                    charged_to_patient : arr.charged_to_patient,
                                    charged_to_carewell : arr.charged_to_carewell,
                                    charged_to_others : arr.charged_to_others,
                                    procedure_remarks : arr.procedure_remarks,
                                    disapproved : arr.disapproved
                                });
                            }

                            if(!availment_physician)
                            {
                                availment_approval.physicianInformation.push({
                                    physician : arr.physician,
                                    specialization : arr.specialization,
                                    rate_rvs : arr.rate_rvs,
                                    procedure : arr.procedure,
                                    actual_pf_charge : arr.actual_pf_charge,
                                    phys_phic_charity : arr.phys_phic_charity,
                                    phys_charged_to_patient : arr.phys_charged_to_patient,
                                    phys_charged_to_carewell : arr.phys_charged_to_carewell
                                });
                            }
                        }
                    }
                    else
                    {
                        this.import_record.push({
                            approval_id : arr.approval_id,
                            callerInformation : caller_info,
                            memberInformation : member_info,
                            coverage_plan : arr.coverage_plan,
                            availmentInformation : availment_info,
                            procedure_type : arr.plan_description,
                            procedureInformation : procedure_info,
                            physicianInformation : physician_info,
                            payeeInformation : payee_info
                        });
                    }
                }
            });
            // console.log(this.import_record,'end')
            for await (let availmentRecord of this.import_record)
            {
                await axios.post('api/import/availment/save', availmentRecord)
                .then(response => {
                    console.log(response.data)
                })
                .catch(error => {
                    console.log({error})
                })
            }
            this.loading = await false;
        },
        availmentArray(table_name, arr){
            let arrayData = [];
            if(table_name == "caller")
            {
                arrayData = {
                    name : arr.caller_name,
                    position : arr.caller_position,
                    contact_number : arr.caller_contact_number,
                    date : arr.caller_date
                };
                return arrayData;
            }
            else if(table_name == "member")
            {
                arrayData = {
                    first_name : arr.first_name,
                    middle_name : arr.middle_name,
                    last_name : arr.last_name,
                    carewell_id : arr.carewell_id,
                    universal_id : arr.universal_id,
                    company : arr.company
                };
                return arrayData;
            }
            else if(table_name == "availment")
            {
                arrayData = {
                    provider : arr.provider,
                    benefit_type : arr.benefit_type,
                    date_avail : arr.availment_date,
                    discharged_date : arr.discharged_date,
                    chief_complaint : arr.chief_complaint,
                    initial_diagnosis : arr.initial_diagnosis,
                    final_diagnosis : arr.final_diagnosis,
                    diagnosis: arr.charge_to
                };
                return arrayData;
            }
            else if(table_name == "procedure")
            {
                arrayData = [{
                    procedures : arr.procedures,
                    gross_amount : arr.gross_amount,
                    phic_charity : arr.phic_charity,
                    charged_to_patient : arr.charged_to_patient,
                    charged_to_carewell : arr.charged_to_carewell,
                    charged_to_others : arr.charged_to_others,
                    procedure_remarks : arr.procedure_remarks,
                    disapproved : arr.disapproved == "Yes" ? 1 : 0
                }];
                return arrayData;
            }
            else if(table_name == "physician")
            {
                arrayData = [{
                    physician : arr.physician,
                    specialization : arr.specialization,
                    rate_rvs : arr.rate_rvs,
                    procedure : arr.procedure,
                    actual_pf_charge : arr.actual_pf_charge,
                    phys_phic_charity : arr.phys_phic_charity,
                    phys_charged_to_patient : arr.phys_charged_to_patient,
                    phys_charged_to_carewell : arr.phys_charged_to_carewell
                }];
                return arrayData;
            }
            else if(table_name == "payee")
            {
                arrayData = {
                    provider_payee : arr.provider_payee,
                    hospital_fee : arr.hospital_fee,
                    doctor : arr.doctor,
                    professional_fee : arr.professional_fee
                };
                return arrayData;
            }
        },
        exportData(){
            window.open(this.$axios.defaults.baseURL+"/api/export/availment","_blank");
        },
        forEmptyCell(data){
            data.forEach((arr,ind) => {
                arr.procedures = arr.procedures ? arr.procedures : '';
                arr.gross_amount = arr.gross_amount ? arr.gross_amount : 0;
                arr.phic_charity = arr.phic_charity ? arr.phic_charity : 0;
                arr.charged_to_patient = arr.charged_to_patient ? arr.charged_to_patient : 0;
                arr.charged_to_carewell = arr.charged_to_carewell ? arr.charged_to_carewell : 0;
                arr.charged_to_others = arr.charged_to_others ? arr.charged_to_others : 0;
                arr.physician = arr.physician ? arr.physician : '';
                arr.actual_pf_charge = arr.actual_pf_charge ? arr.actual_pf_charge : 0;
                arr.phys_phic_charity = arr.phys_phic_charity ? arr.phys_phic_charity : 0;
                arr.phys_charged_to_patient = arr.phys_charged_to_patient ? arr.phys_charged_to_patient : 0;
                arr.phys_charged_to_carewell = arr.phys_charged_to_carewell ? arr.phys_charged_to_carewell : 0;
                arr.hospital_fee = arr.hospital_fee ? arr.hospital_fee : 0;
                arr.professional_fee = arr.professional_fee ? arr.professional_fee : 0;
            });
            return data;
        }
    }
}
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";


    .import-availment
    {
        .v-breadcrumbs li a {
        font-size: 16px;
        }
        .import-availment__menu
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

