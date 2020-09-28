<table width = "100%" style="border-collapse: collapse; border: 0px;">
    <thead>
        <tr>
            <td style="border: 1px solid; padding:10px;"><strong>ACCOUNT TYPE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>PLAN NAME</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>POLICY NUMBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>POLICY EFFECTIVE DATE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>POLICY EXPIRY DATE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MEMBER NUMBER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MEMBER TYPE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>GENDER</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>BIRTHDATE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>PAYMENT MODE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MEMBER EFFECTIVE DATE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>MEMBER EXPIRY DATE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>BILL FROM</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>BILL TO</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>GROSS MEMBERSHIP FEE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>NET MEMBERSHIP FEE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>COLLECTION FEE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>CURRENCY OF MEMBER PAID</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>CURRENCY OF PLAN</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>UNEARNED MEMBERSHIP FEE</strong></td>
            <td style="border: 1px solid; padding:10px;"><strong>TOTAL RESERVES</strong></td>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $row)
        <tr>
                <td style="border: 1px solid; padding:10px;">{{ $row['account_type'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['plan_name'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['policy_number'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['policy_effective_date'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['policy_expiry_date'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['member_number'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['member_type'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['gender'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['birth_date'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['payment_mode'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['member_effective_date'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['member_expiry_date'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['bill_from'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ $row['bill_to'] }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['gross_membership_fee'], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;">{{ number_format($row['net_membership_fee'], 2, '.', ',') }}</td>
                <td style="border: 1px solid; padding:10px;"></td>
                <td style="border: 1px solid; padding:10px;">PHP</td>
                <td style="border: 1px solid; padding:10px;">PHP</td>
                <td style="border: 1px solid; padding:10px;"></td>
                <td style="border: 1px solid; padding:10px;"></td>
        </tr>
    @endforeach
    </tbody>
</table>