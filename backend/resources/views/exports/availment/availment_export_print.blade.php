<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AVAILMENT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
    <script src="main.js"></script>
</head>
<body>
    <span>
        <img src= "http://carewellv2-api.digimahouse.com/images/carewell-logo.jpg" height=100 width=200>
    </span>
    <br>
    <span>
        CAREWELL HEALTH SYSTEM, INC.
    </span>
    <br>
    <br>
    <table class="main-table">
        <tr>
            <td colspan=8 class="main-td td-weight">Caller Information:</td>
        </tr>
        <tr>
            <td class="main-td td-field td-weight">
                Full Name:
            </td>
            <td class="main-td td-value">
                {{ $data->caller_name }}
            </td>
            <td class="main-td td-field td-weight">
                Position:
            </td>
            <td class="main-td td-value">
                {{ $data->caller_position }}
            </td>
            <td class="main-td td-field td-weight">
                Contact Number:
            </td>
            <td class="main-td td-value">
                {{ $data->caller_contact_number }}
            </td>
            <td class="main-td td-field td-weight">
                Date:
            </td>
            <td class="main-td td-value">
                {{ date("m/d/Y", strtotime($data->caller_date)) }}
            </td>
        </tr>
    </table>
    <br>
    <table class="main-table">
        <thead>
            <tr>
                <td colspan=10 class="main-td td-weight">Member Information:</td>
            </tr>
        </thead>
        <tbody>
            <tr rowspan=5>
                <td class="main-td td-field td-weight" >
                    Full Name:
                </td>
                <td class="main-td td-value">
                    {{ $data->member->fullname }}
                </td>
                <td class="main-td td-field td-weight">
                    Universal ID:
                </td>
                <td class="main-td td-value">
                    {{ $data->member->company->universal_id }}
                </td>
            </tr>
            <tr>
                <td colspan=2></td>
                <td class="main-td td-field td-weight">
                    Carewell ID:
                </td>
                <td class="main-td td-value">
                    {{ $data->member->company->carewell_id }}
                </td>
            </tr>
            <tr>
                <td colspan=2></td>
                <td class="main-td td-field td-weight">
                    Company:
                </td>
                <td class="main-td td-value">
                    {{ $data->member->company->company->name }}
                </td>
            </tr>
            <tr>
                <td colspan=2></td>
                <td class="main-td td-field td-weight">
                    Birthdate:
                </td>
                <td class="main-td td-value">
                    {{ date("m/d/Y", strtotime($data->member->birth_date)) }}
                </td>
            </tr>
            <tr>
                <td colspan=2></td>
                <td class="main-td td-field td-weight">
                    Age:
                </td>
                <td class="main-td td-value">
                    {{ $data->member->age }}
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table class="main-table">
        <thead>
            <tr>
                <td colspan=6 class="main-td td-weight">
                    Availment Information:
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="main-td td-field td-weight">
                    Provider:
                </td>
                <td class="main-td td-value">
                    {{ $data->provider->name }}
                </td>
                <td class="main-td td-field td-weight">
                    Benefit Type:
                </td>
                <td class="main-td td-value">
                    {{ $data->benefit_type->name }}
                </td>
                <td class="main-td td-field td-weight">
                    Date:
                </td>
                <td class="main-td td-value">
                    {{ date("m/d/Y", strtotime($data->date_avail)) }}
                </td>
            </tr>
            <tr>
                <td class="main-td td-field td-weight" colspan=2>
                    Chief Complaint:
                </td>
                <td class="main-td td-field td-weight" colspan=2>
                    Initial Diagnosis:
                </td>
                <td class="main-td td-field td-weight" colspan=2>
                    Final Diagnosis:
                </td>
            </tr>
            <tr>
                <td class="main-td td-value" colspan=2>
                    {{ $data->chief_complaint }}
                </td>
                <td class="main-td td-value" colspan=2>
                    {{ $data->initial_diagnosis}}
                </td>
                <td class="main-td td-value" colspan=2>
                    {{ $data->final_diagnosis }}
                </td>
            </tr>
            <tr>
                <td class="main-td td-field td-weight">
                    Diagnosis Charged:
                </td>
                <td class="main-td td-value">
                    {{ $data->diagnosis ? $data->diagnosis->name : '' }}
                </td>
                <td colspan=4></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table class="main-table">
        <thead>
			<tr>
				<td class="main-td td-weight" colspan=8>
					Procedures
				</td>
			</tr>
            <tr>
				<td class="main-td td-value td-weight">
					Procedures:
				</td>
				<td class="main-td td-value td-weight">
					Gross Amount:
				</td>
				<td class="main-td td-value td-weight">
					PHIC Charity/ SWA:
				</td>
				<td class="main-td td-value td-weight">
					Charged to Patient:
				</td>
				<td class="main-td td-value td-weight">
					Charged to Carewell:
				</td>
				<td class="main-td td-value td-weight">
					Charged to Other:
				</td>
				<td class="main-td td-value td-weight">
					Remarks:
				</td>
				<td class="main-td td-value td-weight">
					Disapproved:
				</td>
            </tr>
        </thead>
        <tbody>
			@foreach($data->availment_procedure as $key => $availment_procedure)
			<tr>
				<td class="main-td td-value">
					{{ $availment_procedure->procedure->name }}
				</td>
				<td class="main-td td-value">
					{{ number_format($availment_procedure->gross_amount, 2, '.', '') }}
				</td>
				<td class="main-td td-value">
					{{ number_format($availment_procedure->phic_charity_swa, 2, '.', '') }}
				</td>
				<td class="main-td td-value">
					{{ number_format($availment_procedure->patient_charged, 2, '.', '') }}
				</td>
				<td class="main-td td-value">
					{{ number_format($availment_procedure->carewell_charged, 2, '.', '') }}
				</td>
				<td class="main-td td-value">
					{{ number_format($availment_procedure->charge_other, 2, '.', '') }}
				</td>
				<td class="main-td td-value">
					{{ $availment_procedure->remarks }}
				</td>
				<td class="main-td td-value">
					{{ $availment_procedure->is_disapproved == 1 ? 'YES' : 'NO' }}
				</td>
			</tr>
			@endforeach
		</tbody>
    </table>
	<br>
	<table class="main-table">
		<tr>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL GROSS AMOUNT :</span> <br>
				PHP {{ number_format($data->procedures_gross_amount_total, 2, '.', '') }}
			</td>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL PHIC CHARITY/SWA :</span> <br>
				PHP {{ number_format($data->procedures_phic_amount_total, 2, '.', '') }}
			</td>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL CHARGED TO PATIENT :</span> <br>
				PHP {{ number_format($data->procedures_patient_charged_total, 2, '.', '') }}
			</td>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL CHARGED TO CAREWELL :</span> <br>
				PHP {{ number_format($data->procedures_carewell_charged_total, 2, '.', '') }}
			</td>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL CHARGED TO OTHERS :</span> <br>
				PHP {{ number_format($data->procedures_charge_other_total, 2, '.', '') }}
			</td>
		</tr>
	</table>
	<br>
    <table class="main-table">
        <thead>
			<tr>
				<td class="main-td td-weight" colspan=9>
					Physician Information
				</td>
			</tr>
            <tr>
				<td class="main-td td-value td-weight">
					Physician:
				</td>
				<td class="main-td td-value td-weight">
					Specialization:
				</td>
				<td class="main-td td-value td-weight">
					Rate/ RVS:
				</td>
				<td class="main-td td-value td-weight">
					Procedure:
				</td>
				<td class="main-td td-value td-weight">
					Actual PF Charges:
				</td>
				<td class="main-td td-value td-weight">
					PHIC Charity/ SWA:
				</td>
				<td class="main-td td-value td-weight">
					Charged to Patient:
				</td>
				<td class="main-td td-value td-weight">
					Charged to Carewell:
				</td>
				<td class="main-td td-value td-weight">
					Charged to Other:
				</td>
            </tr>
        </thead>
        <tbody>
			@foreach($data->availment_doctor as $key => $availment_doctor)
				<tr>
					<td class="main-td td-value">
						{{ $availment_doctor->doctor->name }}
					</td>
					<td class="main-td td-value">
						{{ $availment_doctor->specialization ? $availment_doctor->specialization->name : '' }}
					</td>
					<td class="main-td td-value">
						{{ $availment_doctor->rate_rvs }}
					</td>
					<td class="main-td td-value">
						{{ $availment_doctor->procedure ? $availment_doctor->procedure->name : '' }}
					</td>
					<td class="main-td td-value">
						{{ number_format($availment_doctor->doctors_fee, 2, '.', '') }}
					</td>
					<td class="main-td td-value">
						{{ number_format($availment_doctor->phic_charity_swa, 2, '.', '') }}
					</td>
					<td class="main-td td-value">
						{{ number_format($availment_doctor->patient_charged, 2, '.', '') }}
					</td>
					<td class="main-td td-value">
						{{ number_format($availment_doctor->carewell_charged, 2, '.', '') }}
					</td>
					<td class="main-td td-value">
						{{ number_format($availment_doctor->charge_other, 2, '.', '') }}
					</td>
				</tr>
			@endforeach
		</tbody>
    </table>
	<br>
	<table class="main-table">
		<tr>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL ACTUAL PF CHARGE :</span> <br>
				PHP {{ number_format($data->doctors_gross_amount_total, 2, '.', '') }}
			</td>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL PHIC CHARITY/SWA :</span> <br>
				PHP {{ number_format($data->doctors_phic_amount_total, 2, '.', '') }}
			</td>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL CHARGED TO PATIENT :</span> <br>
				PHP {{ number_format($data->doctors_patient_charged_total, 2, '.', '') }}
			</td>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL CHARGED TO CAREWELL :</span> <br>
				PHP {{ number_format($data->doctors_carewell_charged_total, 2, '.', '') }}
			</td>
			<td class="main-td td-value td-total">
				<span class="td-weight">TOTAL CHARGED TO OTHERS :</span> <br>
				PHP {{ number_format($data->doctors_charge_other_total, 2, '.', '') }}
			</td>
		</tr>
	</table>
	<br>
	<table class="main-table">
		<tr>
			<td colspan=3 class="main-td td-weight">
				Payee Information:
			</td>
		</tr>
		<tr>
			<td class="main-td td-value td-weight">
			</td>
			<td class="main-td td-value td-weight">
				PAYEE
			</td>
			<td class="main-td td-value td-weight">
				AMOUNT
			</td>
		</tr>
		<tr>
			<td class="main-td td-value td-weight">
				Hospital Fee:
			</td>
			<td class="main-td td-value">
				{{ $data->provider_payee ? $data->provider_payee->name : '' }}
			</td>
			<td class="main-td td-value">
				{{ number_format($data->provider_payee_fee, 2, '.', '') }}
			</td>
		</tr>
		<tr>
			<td class="main-td td-value td-weight">
				Professional Fee:
			</td>
			<td class="main-td td-value">
				{{ $data->doctor ? $data->doctor->name : '' }}
			</td>
			<td class="main-td td-value">
				{{ number_format($data->doctor_fee, 2, '.', '') }}
			</td>
		</tr>
		<tr>
			<td class="main-td td-value td-weight">
				Grand Total
			</td>
			<td class="main-td td-value"></td>
			<td class="main-td td-value">
				PHP {{ number_format($data->grand_total, 2, '.', '') }}
			</td>
		</tr>
	</table>
</body>

<style>
    .main-table {
        border-collapse: collapse;
        width : 100%;
    }

    .main-td {
        padding : 5px;
        font-size : 12pt;
    }

    .main-table, .main-td {
        border: 1px solid black;
       
    }

	.td-total {
		white-space: nowrap;
	}

    .td-field {
        text-align : left;
    }

	.td-weight {
		font-weight : bolder;
	}

    .td-value {
        text-align : center;
    }

    .sub-td {
        font-size : 12pt;
        text-align: center;
    }

    .sub-table {
        border-collapse: collapse;
        width : 100%;
    }
    
    .sub-table, .sub-td {
        border: 1px solid black;
        /* width: 100%; */
    }

    .break-page { page-break-before: always; }

}
</style>