<table>
<thead>
    <tr>
        <td><strong>AVAILMENT SUMMARY REPORT</strong></td>
    </tr>
    <tr>
        <td><strong>{{$year}}</strong></td>
    </tr>
    <tr>
        <td><strong>COMPANY NAME</strong></td>
        <td><strong>JANUARY</strong></td>
        <td><strong>FEBRUARY</strong></td>
        <td><strong>MARCH</strong></td>
        <td><strong>APRIL</strong></td>
        <td><strong>MAY</strong></td>
        <td><strong>JUNE</strong></td>
        <td><strong>JULY</strong></td>
        <td><strong>AUGUST</strong></td>
        <td><strong>SEPTEMBER</strong></td>
        <td><strong>OCTOBER</strong></td>
        <td><strong>NOVEMBER</strong></td>
        <td><strong>DECEMBER</strong></td>
        <td><strong>TOTAL AVAILMENT</strong></td>
    </tr>
</thead>
<tbody>
    @foreach($data as $key => $row)
    <tr>
        <td>{{ $row['company_name'] }}</td>
        <td>{{ $row['no_of_availments'][0] }}</td>
        <td>{{ $row['no_of_availments'][1] }}</td>
        <td>{{ $row['no_of_availments'][2] }}</td>
        <td>{{ $row['no_of_availments'][3] }}</td>
        <td>{{ $row['no_of_availments'][4] }}</td>
        <td>{{ $row['no_of_availments'][5] }}</td>
        <td>{{ $row['no_of_availments'][6] }}</td>
        <td>{{ $row['no_of_availments'][7] }}</td>
        <td>{{ $row['no_of_availments'][8] }}</td>
        <td>{{ $row['no_of_availments'][9] }}</td>
        <td>{{ $row['no_of_availments'][10] }}</td>
        <td>{{ $row['no_of_availments'][11] }}</td>
        <td>{{ $row['total_availments'] }}</td>
    </tr>
    @endforeach
    <tr>
        <td><strong>TOTAL</strong></td>
    </tr>
</tbody>
</table>