<table width = "100%" style="border-collapse: collapse; border: 0px;">
    <thead>
        <tr>
            <td style="border: 1px solid; padding:10px;"><strong>COMPANY</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JANUARY COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JANUARY AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>FEBRUARY COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>FEBRUARY AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MARCH COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MARCH AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>APRIL COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>APRIL AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MAY COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MAY AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JUNE COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JUNE AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JULY COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>JULY AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>AUGUST COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>AUGUST AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>SEPTEMBER COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>SEPTEMBER AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>OCTOBER COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>OCTOBER AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NOVEMBER COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NOVEMBER AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>DECEMBER COUNT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>DECEMBER AVAILMENT</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>YTD</strong></td>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $row)
        <tr>
                <td style="border: 1px solid; padding:10px;">{{ $row['name'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][0], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][0], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][1], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][1], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][2], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][2], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][3], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][3], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][4], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][4], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][5], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][5], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][6], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][6], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][7], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][7], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][8], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][8], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][9], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][9], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][10], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][10], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_count'][11], 0, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['monthly_total'][11], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['year_total'], 2, '.', ',') }}</td>
        </tr>
    @endforeach
        <tr>
            <td style="border: 1px solid; padding:10px;"><strong>TOTAL</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[0], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[0], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[1], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[1], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[2], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[2], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[3], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[3], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[4], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[4], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[5], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[5], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[6], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[6], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[7], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[7], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[8], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[8], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[9], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[9], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[10], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[10], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_count[11], 0, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_monthly_total[11], 2, '.', ',') }}</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>{{ number_format($overall_grand_total, 2, '.', ',') }}</strong></td>
        </tr>
    </tbody>
</table>