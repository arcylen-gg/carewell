<table>
    <thead>
		<tr>
		  	<td>id</td>
		   	<td>name</td>
		    <td>code</td>
		    <td>address</td>
		    <td>email</td>
		    <td>contact_number</td>
		    <td>status</td>
	    </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
    	<tr>
    	    <td>{{ $row['id'] }}</td>
    	    <td>{{ $row['name'] }}</td>
    	    <td>{{ $row['code'] }}</td>
    	    <td>{{ $row['address'] }}</td>
    	    <td>{{ $row['email'] }}</td>
    	    <td>{{ $row['contact_number'] }}</td>
    	    <td>{{ $row['is_archived'] == 0 ? 'active':'inactive' }}</td>
		</tr>
    @endforeach
    </tbody>
</table>