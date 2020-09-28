<table>
    <thead>
		<tr>
		  	<td>company</td>
		   	<td>approval_id</td>
		    <td>member_name</td>
		    <td>coverage_plan</td>
		    <td>final_diagnosis</td>
		    <td>charge_to</td>
		    <td>grand_total</td>
		    <td>total_approved</td>
	    </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $row)
    	<tr>
    	    <td>{{ $row['company'] }}</td>
    	    <td>{{ $row['approval_id'] }}</td>
    	    <td>{{ $row['member_name'] }}</td>
    	    <td>{{ $row['coverage_plan'] }}</td>
    	    <td>{{ $row['final_diagnosis'] }}</td>
    	    <td>{{ $row['charged_to'] }}</td>
    	    <td>{{ $row['grand_total'] }}</td>
    	    <td>{{ $row['total_approved'] }}</td>
		</tr>
    @endforeach
    </tbody>
</table>