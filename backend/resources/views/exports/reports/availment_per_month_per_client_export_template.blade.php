<table>
<thead>
    <tr>
        <td><strong>AVAILMENT PER MONTH PER CLIENT REPORT</strong></td>
    </tr>
    <tr>
        <td><strong>{{$year}}</strong></td>
    </tr>
    <tr>
        <td><strong>COMPANY NAME</strong></td>
        <td><strong>JANUARY COUNT</strong></td>
        <td><strong>JANUARY AVAILMENT</strong></td>
        <td><strong>FEBRUARY COUNT</strong></td>
        <td><strong>FEBRUARY AVAILMENT</strong></td>
        <td><strong>MARCH COUNT</strong></td>
        <td><strong>MARCH AVAILMENT</strong></td>
        <td><strong>APRIL COUNT</strong></td>
        <td><strong>APRIL AVAILMENT</strong></td>
        <td><strong>MAY COUNT</strong></td>
        <td><strong>MAY AVAILMENT</strong></td>
        <td><strong>JUNE COUNT</strong></td>
        <td><strong>JUNE AVAILMENT</strong></td>
        <td><strong>JULY COUNT</strong></td>
        <td><strong>JULY AVAILMENT</strong></td>
        <td><strong>AUGUST COUNT</strong></td>
        <td><strong>AUGUST AVAILMENT</strong></td>
        <td><strong>SEPTEMBER COUNT</strong></td>
        <td><strong>SEPTEMBER AVAILMENT</strong></td>
        <td><strong>OCTOBER COUNT</strong></td>
        <td><strong>OCTOBER AVAILMENT</strong></td>
        <td><strong>NOVEMBER COUNT</strong></td>
        <td><strong>NOVEMBER AVAILMENT</strong></td>
        <td><strong>DECEMBER COUNT</strong></td>
        <td><strong>DECEMBER AVAILMENT</strong></td>
        <td><strong>YTD</strong></td>

    </tr>
</thead>
<tbody>
    @foreach($data as $key => $row)
    <tr>
        <td>{{ $row['name'] }}</td>
        <td>{{ $row['monthly_count'][0] }}</td>
        <td>{{ $row['monthly_total'][0] }}</td>
        <td>{{ $row['monthly_count'][1] }}</td>
        <td>{{ $row['monthly_total'][1] }}</td>
        <td>{{ $row['monthly_count'][2] }}</td>
        <td>{{ $row['monthly_total'][2] }}</td>
        <td>{{ $row['monthly_count'][3] }}</td>
        <td>{{ $row['monthly_total'][3] }}</td>
        <td>{{ $row['monthly_count'][4] }}</td>
        <td>{{ $row['monthly_total'][4] }}</td>
        <td>{{ $row['monthly_count'][5] }}</td>
        <td>{{ $row['monthly_total'][5] }}</td>
        <td>{{ $row['monthly_count'][6] }}</td>
        <td>{{ $row['monthly_total'][6] }}</td>
        <td>{{ $row['monthly_count'][7] }}</td>
        <td>{{ $row['monthly_total'][7] }}</td>
        <td>{{ $row['monthly_count'][8] }}</td>
        <td>{{ $row['monthly_total'][8] }}</td>
        <td>{{ $row['monthly_count'][9] }}</td>
        <td>{{ $row['monthly_total'][9] }}</td>
        <td>{{ $row['monthly_count'][10] }}</td>
        <td>{{ $row['monthly_total'][10] }}</td>
        <td>{{ $row['monthly_count'][11] }}</td>
        <td>{{ $row['monthly_total'][11] }}</td>
        <td>{{ $row['year_total'] }}</td>
    </tr>
    @endforeach
    <tr>
        <td><strong>TOTAL</strong></td>
    </tr>
</tbody>
</table>