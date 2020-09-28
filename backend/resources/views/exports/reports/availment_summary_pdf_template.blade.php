<table width = "100%" style="border-collapse: collapse; border: 0px;">
    <thead>
        <tr>
            <td style="border: 1px solid; padding:10px;"><strong>COMPANY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JANUARY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>FEBRUARY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MARCH</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>APRIL</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MAY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JUNE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JULY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>AUGUST</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>SEPTEMBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>OCTOBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NOVEMBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>DECEMBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>TOTAL</strong></td>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $row)
        <tr>
                <td style="border: 1px solid; padding:10px;">{{ $row['name'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][0], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][1], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][2], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][3], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][4], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][5], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][6], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][7], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][8], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][9], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][10], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['no_of_availments'][11], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['total_availments'], 0, '.', ',') }}</td>
        </tr>
    @endforeach
        <tr>
            <td style="border: 1px solid; padding:10px;"><strong>TOTAL</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[0], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[1], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[2], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[3], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[4], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[5], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[6], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[7], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[8], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[9], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[10], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($total_no_of_availments[11], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_total_of_availments, 0, '.', ',') }}</strong></td>
        </tr>
    </tbody>
</table>