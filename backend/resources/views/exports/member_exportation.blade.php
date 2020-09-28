<table>
    <thead>
		<tr>
		  	<td>id</td>
		   	<td>universal_id</td>
		    <td>carewell_id</td>
		    <td>first_name</td>
		    <td>middle_name</td>
		    <td>last_name</td>
		    <td>mothers_maiden_name</td>
            <td>birth_date</td>
            <td>gender</td>
            <td>marital_status</td>
            <td>contact_number</td>
            <td>email</td>
            <td>permanent_address</td>
            <td>present_address</td>
            <td>dependents_name</td>
            <td>dependents_birth_date</td>
            <td>dependents_relationship</td>
            <td>company</td>
            <td>deployment</td>
            <td>payment_mode</td>
            <td>coverage_plan</td>
            <td>sss_number</td>
            <td>tin</td>
            <td>philhealth_number</td>
            <td>pagibig_number</td>
            <td>status</td>
	    </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
        @if(count($row['dependents']) > 0)
            @foreach ($row['dependents'] as $dependents)
                <tr>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row['company']['universal_id'] }}</td>
                    <td>{{ $row['company']['carewell_id'] }}</td>
                    <td>{{ $row['first_name'] }}</td>
                    <td>{{ $row['middle_name'] }}</td>
                    <td>{{ $row['last_name'] }}</td>
                    <td>{{ $row['mothers_maiden_name'] }}</td>
                    <td>{{ $row['birth_date'] }}</td>
                    <td>{{ $row['gender'] }}</td>
                    <td>{{ $row['marital_status'] }}</td>
                    <td>{{ $row['contact_number'] }}</td>
                    <td>{{ $row['email'] }}</td>
                    <td>{{ $row['permanent_address'] }}</td>
                    <td>{{ $row['present_address'] }}</td>
                    <td>{{ $dependents['full_name'] }}</td>
                    <td>{{ $dependents['birth_date'] }}</td>
                    <td>{{ $dependents['relationship'] }}</td>
                    <td>{{ $row['company']['company']['name'] }}</td>
                    <td>{{ $row['company']['company_deployment']['name'] }}</td>
                    <td>{{ $row['company']['payment_mode']['name'] }}</td>
                    <td>{{ $row['company']['coverage_plan']['name'] }}</td>
                    <td>{{ $row['sss_number'] }}</td>
                    <td>{{ $row['tin'] }}</td>
                    <td>{{ $row['philhealth_number'] }}</td>
                    <td>{{ $row['pagibig_number'] }}</td>
                    <td>{{ $row['is_archived'] == 0 ? 'Active' : 'Inactive'}}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>{{ $row['id'] }}</td>
                <td>{{ $row['company']['universal_id'] }}</td>
                <td>{{ $row['company']['carewell_id'] }}</td>
                <td>{{ $row['first_name'] }}</td>
                <td>{{ $row['middle_name'] }}</td>
                <td>{{ $row['last_name'] }}</td>
                <td>{{ $row['mothers_maiden_name'] }}</td>
                <td>{{ $row['birth_date'] }}</td>
                <td>{{ $row['gender'] }}</td>
                <td>{{ $row['marital_status'] }}</td>
                <td>{{ $row['contact_number'] }}</td>
                <td>{{ $row['email'] }}</td>
                <td>{{ $row['permanent_address'] }}</td>
                <td>{{ $row['present_address'] }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $row['company']['company']['name'] }}</td>
                <td>{{ $row['company']['company_deployment']['name'] }}</td>
                <td>{{ $row['company']['payment_mode']['name'] }}</td>
                <td>{{ $row['company']['coverage_plan']['name'] }}</td>
                <td>{{ $row['sss_number'] }}</td>
                <td>{{ $row['tin'] }}</td>
                <td>{{ $row['philhealth_number'] }}</td>
                <td>{{ $row['pagibig_number'] }}</td>
                <td>{{ $row['is_archived'] == 0 ? 'Active' : 'Inactive'}}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>