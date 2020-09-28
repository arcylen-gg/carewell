<table>
    <thead>
		<tr>
		  	<td><strong>ACCOUNT TYPE</strong></td>
		   	<td><strong>PLAN NAME</strong></td>
		    <td><strong>POLICY NUMBER</strong></td>
		    <td><strong>POLICY EFFECTIVE DATE</strong></td>
		    <td><strong>POLICY EXPIRY DATE</strong></td>
		    <td><strong>MEMBER NUMBER</strong></td>
		    <td><strong>MEMBER TYPE</strong></td>
        <td><strong>GENDER</strong></td>
        <td><strong>BIRTHDATE</strong></td>
        <td><strong>PAYMENT MODE</strong></td>
        <td><strong>MEMBER EFFECTIVE DATE</strong></td>
        <td><strong>MEMBER EXPIRY DATE</strong></td>
        <td><strong>BILL FROM</strong></td>
        <td><strong>BILL TO</strong></td>
        <td><strong>GROSS MEMBERSHIP FEE</strong></td>
        <td><strong>NET MEMBERSHIP FEE</strong></td>
        <td><strong>COLLECTION FEE</strong></td>
        <td><strong>CURRENCY OF MEMBER PAID</strong></td>
        <td><strong>CURRENCY OF PLAN</strong></td>
        <td><strong>UNEARNED MEMBERSHIP FEE</strong></td>
        <td><strong>TOTAL RESERVES</strong></td>
	    </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $row)
    	<tr>
    	    <td>{{ $row['account_type'] }}</td>
    	    <td>{{ $row['plan_name'] }}</td>
    	    <td>{{ $row['policy_number'] }}</td>
    	    <td>{{ $row['policy_effective_date'] }}</td>
    	    <td>{{ $row['policy_expiry_date'] }}</td>
    	    <td>{{ $row['member_number'] }}</td>
    	    <td>{{ $row['member_type'] }}</td>
          <td>{{ $row['gender'] }}</td>
          <td>{{ $row['birth_date'] }}</td>
          <td>{{ $row['payment_mode'] }}</td>
          <td>{{ $row['member_effective_date'] }}</td>
          <td>{{ $row['member_expiry_date'] }}</td>
          <td>{{ $row['bill_from'] }}</td>
          <td>{{ $row['bill_to'] }}</td>
          <td>{{ $row['gross_membership_fee'] }}</td>
          <td>{{ $row['net_membership_fee'] }}</td>
          <td></td>
          <td>PHP</td>
          <td>PHP</td>
          <td></td>
          <td></td>
		  </tr>
    @endforeach
    </tbody>
</table>