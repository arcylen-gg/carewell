<table>
<thead>
    <tr>
        <td><strong>CAREWELL HEALTH SYSTEM, INC.</strong></td>
    </tr>
    <tr>
        <td><strong>AVAILMENT MONITORING REPORT</strong></td>
    </tr>
    <tr>
        <td><strong>CY {{$year}}</strong></td>
    </tr>
    <tr>
        <td><strong>CONSOLIDATED</strong></td>
    </tr>
    <tr>
        <td><strong>AVAILMENT TYPE</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>JANUARY</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>FEBRUARY</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>MARCH</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>APRIL</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>MAY</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>JUNE</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>JULY</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>AUGUST</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>SEPTEMBER</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>OCTOBER</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>NOVEMBER</strong></td>
        <td><strong>NO. OF PATIENT</strong></td>
        <td><strong>DECEMBER</strong></td>
        <td><strong>TOTAL NO. OF PATIENT</strong></td>
        <td><strong>TOTAL AMOUNT</strong></td>
    </tr>
</thead>
<tbody>
    @foreach($data as $key => $row)
    <tr>
        <td>{{ $row['benefit_name'] }}</td>
        <td>{{ $row['no_of_patient'][0] }}</td>
        <td>{{ $row['total_month'][0] }}</td>
        <td>{{ $row['no_of_patient'][1] }}</td>
        <td>{{ $row['total_month'][1] }}</td>
        <td>{{ $row['no_of_patient'][2] }}</td>
        <td>{{ $row['total_month'][2] }}</td>
        <td>{{ $row['no_of_patient'][3] }}</td>
        <td>{{ $row['total_month'][3] }}</td>
        <td>{{ $row['no_of_patient'][4] }}</td>
        <td>{{ $row['total_month'][4] }}</td>
        <td>{{ $row['no_of_patient'][5] }}</td>
        <td>{{ $row['total_month'][5] }}</td>
        <td>{{ $row['no_of_patient'][6] }}</td>
        <td>{{ $row['total_month'][6] }}</td>
        <td>{{ $row['no_of_patient'][7] }}</td>
        <td>{{ $row['total_month'][7] }}</td>
        <td>{{ $row['no_of_patient'][8] }}</td>
        <td>{{ $row['total_month'][8] }}</td>
        <td>{{ $row['no_of_patient'][9] }}</td>
        <td>{{ $row['total_month'][9] }}</td>
        <td>{{ $row['no_of_patient'][10] }}</td>
        <td>{{ $row['total_month'][10] }}</td>
        <td>{{ $row['no_of_patient'][11] }}</td>
        <td>{{ $row['total_month'][11] }}</td>
        <td>{{ $row['total_patient'] }}</td>
        <td>{{ $row['total_amount'] }}</td>
    </tr>
    @endforeach
    <tr>
        <td><strong>TOTAL</strong></td>
    </tr>
</tbody>
</table>