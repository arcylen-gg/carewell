<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payable</title>
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
    For Doctors:
    <table class="main-table"> 
        <thead>
            <tr>
                <td class="main-td td-field">Provider:</td>
                <td class="main-td td-value">{{ $data->provider->name }}</td>
                <td class="main-td td-field">SOA Number:</td>
                <td class="main-td td-value">{{ $data->soa_number }}</td>
            </tr>
            <tr>
                <td class="main-td td-field">Receive Date:</td>
                <td class="main-td td-value">{{ date("m/d/Y", strtotime($data->received_date)) }}</td>
                <td class="main-td td-field">Due Date:</td>
                <td class="main-td td-value">{{ date("m/d/Y", strtotime($data->due_date)) }}</td>
            </tr>
            <tr>
                <td colspan=2></td>
                <td class="main-td td-field">Payment Term:</td>
                <td class="main-td td-value">{{ $data->payment_term->name }}</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="main-td td-value" colspan="4">AVAILMENTS:</td>
            </tr>
        </tbody>
    </table>
    <table class="availment-table">
        <thead>
            <tr>
                <td class="availment-td">Approval ID</td>
                <!-- <td class="availment-td">Benefit Type</td> -->
                <td class="availment-td">Doctors</td>
                <td class="availment-td">Procedures</td>
                <!-- <td class="availment-td">Diagnosis</td> -->
                <!-- <td class="availment-td">Chief Complaint</td> -->
                <!-- <td class="availment-td">Universal ID</td> -->
                <td class="availment-td">Carewell ID</td>
                <td class="availment-td">Member Name</td>
                <td class="availment-td">Birthdate</td>
                <td class="availment-td">Age</td>
                <td class="availment-td">Gender</td>
                <!-- <td class="availment-td">Company</td> -->
                <td class="availment-td">Provider</td>
                <td class="availment-td">Date Avail</td>
                <!-- <td class="availment-td">Hospital Fee</td> -->
                <td class="availment-td">Professional Fee</td>
                <td class="availment-td">Amount</td>
                <td class="availment-td">Payment Amount</td>
                <td class="availment-td">Balance</td>
                <td class="availment-td">Remarks</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($data->payable_availment as $availment)
                    <td class="availment-td"> {{ $availment->availment->approval_id }} </td>
                    <!-- <td class="availment-td"> {{ $availment->availment->benefit_type->name }} </td> -->
                    <td class="availment-td">
                        @foreach($availment->availment->availment_doctor as $doctor)
                            {{$doctor->doctor->name}},<br>
                        @endforeach
                    </td>
                    <td class="availment-td"> 
                        @foreach($availment->availment->availment_procedure as $procedure)
                            {{$procedure->procedure->name}},<br>
                        @endforeach
                    </td>
                    <!-- <td class="availment-td"> {{ $availment->availment->diagnosis->name }} </td> -->
                    <!-- <td class="availment-td"> {{ $availment->availment->chief_complaint }} </td> -->
                    <!-- <td class="availment-td"> {{ $availment->availment->member->company->universal_id }} </td> -->
                    <td class="availment-td"> {{ $availment->availment->member->company->carewell_id }} </td>
                    <td class="availment-td"> {{ $availment->availment->member->fullname }} </td>
                    <td class="availment-td"> {{ date("m/d/Y", strtotime($availment->availment->member->birth_date)) }} </td>
                    <td class="availment-td"> {{ $availment->availment->member->age }} </td>
                    <td class="availment-td"> {{ $availment->availment->member->gender }} </td>
                    <!-- <td class="availment-td"> {{ $availment->availment->member->company->company->name }} </td> -->
                    <td class="availment-td"> {{ $availment->availment->provider->name }} </td>
                    <td class="availment-td"> {{ date("m/d/Y", strtotime($availment->availment->date_avail)) }} </td>
                    <!-- <td class="availment-td"> {{ $availment->availment->provider_payee_fee }} </td> -->
                    <td class="availment-td"> {{ number_format($availment->availment->doctor_fee, 2, '.', '') }} </td>
                    <td class="availment-td"> {{ number_format($availment->availment->grand_total, 2, '.', '') }} </td>
                    <td class="availment-td"> {{ number_format($availment->payable_amount, 2, '.', '') }} </td>
                    <td class="availment-td"> {{ number_format($availment->availment->grand_total - $availment->payable_amount, 2, '.', '') }} </td>
                    <td class="availment-td"> {{ $availment->remarks }} </td>
                @endforeach
            </td>
        </tbody>
    </table>
</body>
</html>


<style>
    .main-table {
        border-collapse: collapse;
    }

    .main-td {
        padding : 5px;
        font-size : 12pt;
    }

    .main-table, .main-td {
        border: 1px solid black;
        width : 100%;
    }

    .td-field {
        text-align : left;
    }

    .td-value {
        text-align : center;
    }

    .availment-td {
        font-size : 8pt;
        text-align: center;
    }

    .availment-table {
        border-collapse: collapse;
    }
    
    .availment-table, .availment-td {
        border: 1px solid black;
        width: 100%;
    }
</style>
