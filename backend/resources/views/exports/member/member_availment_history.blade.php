<table>
    <thead>
		<tr>
		  	<td>APPROVAL NO.</td>
		   	<td>APPROVAL DATE</td>
		    <td>TYPE OF BENEFITS</td>
			<td>DIAGNOSIS</td>
			<td>FINAL DIAGNOSIS</td>
		    <td>TOTAL AMOUNT</td>
		    <td>CHARGED TO PATIENT</td>
            <td>CHARGED TO CAREWELL</td>
            <td>PROVIDER</td>
            <td>COMPANY</td>
	    </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
    	<tr>
    	    <td>{{ $row['approval_id'] }}</td>
    	    <td>{{ $row['date_avail'] }}</td>
    	    <td>{{ $row['benefit_type']['name'] }}</td>
			<td>{{ $row['diagnosis']['name'] }}</td>
			<td>{{ $row['final_diagnosis'] }}</td>
    	    <td>{{ $row['grand_total'] }}</td>
            <td>{{ $row['procedures_patient_charged_total'] }}</td>
    	    <td>{{ $row['procedures_carewell_charged_total'] }}</td>
            <td>{{ $row['provider']['name'] }}</td>
    	    <td>{{ $row['company']['name'] }}</td>
		</tr>
    @endforeach
    </tbody>
</table>