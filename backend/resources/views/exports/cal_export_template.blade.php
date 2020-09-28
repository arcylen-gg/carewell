<table>
    <thead>
		<tr>
            <td>universal_id</td>
		   	<td>last_name</td>
		    <td>first_name</td>
		    <td>middle_name</td>
		    <td>birth_date</td>
		    <td>coverage_plan</td>
		    <td>payment_mode</td>
		    <td>monthly_premium</td>
			<td>deployment</td>
		    <td>payment_amount</td>
		    <td>status</td>
		  	<td>member_code</td>
	    </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $row)
    	<tr>
    	    <td>{{ $row->universal_id }}</td>
    	    <td>{{ $row->last_name }}</td>
    	    <td>{{ $row->first_name }}</td>
    	    <td>{{ $row->middle_name }}</td>
    	    <td>{{ $row->birth_date }}</td>
    	    <td>{{ $row->coverage_plan_name }}</td>
    	    <td>{{ $row->payment_mode }}</td>
    	    <td>{{ $row->monthly_premium }}</td>
			<td>{{ $row->deployment_name }}</td>
    	    <td></td>
    	    <td>{{ $row->is_archived == 0 ? 'Active':'Inactive' }}</td>
            <td>{{ $row->member_id }}</td>
		</tr>
    @endforeach
    </tbody>
</table>