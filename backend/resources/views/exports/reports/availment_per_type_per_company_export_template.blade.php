<table>
<thead>
    <tr>
        <td><strong>AVAILMENT PER TYPE PER COMPANY REPORT {{$year}}</strong></td>
    </tr>
    <tr>
        <td><strong>{{$month}} {{$year}}</strong></td>
    </tr>
    <tr>
        <td><strong>COMPANY NAME</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>ANNUAL PHYSICAL EXAMINATION (APE)</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>OUTPATIENT CONSULTATION</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>OUTPATIENT LABORATORY</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>MINOR OPERATION</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>EMERGENCY CASES</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>INSURANCE BENEFIT RIDER</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>CONFINEMENT</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>DENTAL</strong></td>
        <td><strong>TOTAL NO. OF PATIENTS</strong></td>
        <td><strong>GRAND TOTAL AMOUNT</strong></td>

    </tr>
</thead>
<tbody>
    @foreach($data as $key => $row)
    <tr>
        <td>{{ $row['company_name'] }}</td>
        <td>{{ $row['patient_count'][0] }}</td>
        <td>{{ $row['total_per_benefit'][0] }}</td>
        <td>{{ $row['patient_count'][1] }}</td>
        <td>{{ $row['total_per_benefit'][1] }}</td>
        <td>{{ $row['patient_count'][2] }}</td>
        <td>{{ $row['total_per_benefit'][2] }}</td>
        <td>{{ $row['patient_count'][3] }}</td>
        <td>{{ $row['total_per_benefit'][3] }}</td>
        <td>{{ $row['patient_count'][4] }}</td>
        <td>{{ $row['total_per_benefit'][4] }}</td>
        <td>{{ $row['patient_count'][5] }}</td>
        <td>{{ $row['total_per_benefit'][5] }}</td>
        <td>{{ $row['patient_count'][6] }}</td>
        <td>{{ $row['total_per_benefit'][6] }}</td>
        <td>{{ $row['patient_count'][7] }}</td>
        <td>{{ $row['total_per_benefit'][7] }}</td>
        <td>{{ $row['total_patients'] }}</td>
        <td>{{ $row['total_availments_amount'] }}</td>
    </tr>
    @endforeach
    <tr>
        <td><strong>TOTAL</strong></td>
    </tr>
</tbody>
</table>