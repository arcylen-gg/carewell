<table width = "100%" style="border-collapse: collapse; border: 0px;">
    <thead>
        <tr>
            <td style="border: 1px solid; padding:10px;"><strong>COMPANY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JANUARY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>FEBRUARY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MARCH</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>APRIL</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MAY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JUNE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JULY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>AUGUST</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>SEPTEMBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>OCTOBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NOVEMBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>DECEMBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>TOTAL NO. OF PATIENTS</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>GRAND TOTAL AMOUNT</strong></td>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $row)
        <tr>
                <td style="border: 1px solid; padding:10px;">{{ $row['name'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][0], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][0], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][1], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][1], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][2], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][2], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][3], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][3], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][4], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][4], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][5], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][5], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][6], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][6], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][7], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][7], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][8], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][8], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][9], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][9], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][10], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][10], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_patient'][11], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_month'][11], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_patient'], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_amount'], 2, '.', ',') }}</td>
        </tr>
    @endforeach
        <tr>
            <td style="border: 1px solid; padding:10px;"><strong>TOTAL</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[0], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[0], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[1], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[1], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[2], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[2], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[3], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[3], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[4], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[4], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[5], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[5], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[6], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[6], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[7], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[7], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[8], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[8], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[9], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[9], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[10], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[10], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_patient_per_month[11], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_availment_amount[11], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_patient_count, 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_availment_amount, 2, '.', ',') }}</strong></td>
        </tr>
    </tbody>
</table>