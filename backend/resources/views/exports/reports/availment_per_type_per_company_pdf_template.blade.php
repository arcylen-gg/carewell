<table width = "100%" style="border-collapse: collapse; border: 0px;">
    <thead>
        <tr>
            <td style="border: 1px solid; padding:10px;"><strong>COMPANY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>ANNUAL PHYSICAL EXAMINATION (APE)</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>OUTPATIENT CONSULTATION</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>OUTPATIENT LABORATORY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MINOR OPERATION</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>EMERGENCY CASES</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>INSURANCE BENEFIT RIDER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>CONFINEMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>DENTAL</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>TOTAL NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>GRAND TOTAL AMOUNT</strong></td>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $row)
        <tr>
                <td style="border: 1px solid; padding:10px;">{{ $row['name'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['patient_count'][0], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_per_benefit'][0], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['patient_count'][1], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_per_benefit'][1], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['patient_count'][2], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_per_benefit'][2], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['patient_count'][3], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_per_benefit'][3], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['patient_count'][4], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_per_benefit'][4], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['patient_count'][5], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_per_benefit'][5], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['patient_count'][6], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_per_benefit'][6], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['patient_count'][7], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_per_benefit'][7], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['total_patients'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['total_availments_amount'] }}</td>
        </tr>
    @endforeach
        <tr>
            <td style="border: 1px solid; padding:10px;">TOTAL</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_patient_count[0], 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_availment_amount[0], 2, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_patient_count[1], 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_availment_amount[1], 2, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_patient_count[2], 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_availment_amount[2], 2, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_patient_count[3], 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_availment_amount[3], 2, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_patient_count[4], 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_availment_amount[4], 2, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_patient_count[5], 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_availment_amount[5], 2, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_patient_count[6], 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_availment_amount[6], 2, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_patient_count[7], 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($total_availment_amount[7], 2, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($overall_patient_count, 0, '.', ',') }}</td>
            <td style="border: 1px solid; padding:10px;">{{ number_format($overall_availment_amount, 2, '.', ',') }}</td>
        </tr>
    </tbody>
</table>