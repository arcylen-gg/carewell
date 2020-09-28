<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CAL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
    <script src="main.js"></script>
</head>
<body>
    <table class="main-table">
        <thead>
            <tr>
                <td class="main-td td-field">Reference Number:</td>
                <td class="main-td td-value">{{ $data['reference_number'] }}</td>
                <td colspan="2" style="text-align:center">
                    <img src= "http://carewellv2-api.digimahouse.com/images/carewell-logo.jpg" height=100 width=200>
                </td>
            </tr>
            <tr>
                <td class="main-td td-field">Company:</td>
                <td class="main-td td-value">{{ $data['company']['name'] }}</td>
                <td colspan="2" style="text-align:center">CAREWELL HEALTH SYSTEM, INC.</td>
            </tr>
            <tr>
                <td class="main-td td-field">Payment Mode:</td>
                <td class="main-td td-value">{{ $data['payment_mode']['name'] }}</td>
                <td class="main-td td-field">Payment Term:</td>
                <td class="main-td td-value">{{ $data['payment_term']['name'] }}</td>
            </tr>
            <tr>
                <td class="main-td td-field">CAL Date:</td>
                <td class="main-td td-value">{{ date("m/d/Y", strtotime($data['payment_cal_date'])) }}</td>
                <td class="main-td td-field">Due Date:</td>
                <td class="main-td td-value">{{ date("m/d/Y", strtotime($data['payment_due_date'])) }}</td>
            </tr>
            <tr>
                <td class="main-td td-field">Payment Start:</td>
                <td class="main-td td-value">{{ date("m/d/Y", strtotime($data['payment_start_date'])) }}</td>
                <td class="main-td td-field">Payment End:</td>
                <td class="main-td td-value">{{ date("m/d/Y", strtotime($data['payment_end_date'])) }}</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" class="main-td td-value">CAL Members:</td>
            </tr>
        </tbody>
    </table>
        <table class="sub-table">
            <thead>
                <tr>
                    <td class="sub-td" nowrap>Universal ID</td>
                    <td class="sub-td" nowrap>Last Name</td>
                    <td class="sub-td" nowrap>First Name</td>
                    <td class="sub-td" nowrap>Middle Name</td>
                    <td class="sub-td" nowrap>Birth Date</td>
                    <td class="sub-td" nowrap>Coverage Plan</td>
                    <td class="sub-td" nowrap>Deployment</td>
                    <td class="sub-td" nowrap>Payment Mode</td>
                    <td class="sub-td" nowrap>Monthly Premium</td>
                    <td class="sub-td" nowrap>Payment Amount</td>
                </tr>
            </thead>
            <tbody>
                @if(isset($data['cal_member']))
                    @foreach($data['cal_member'] as $key => $cal_member)
                        <tr>
                            <td class="sub-td" nowrap>{{ $cal_member['member']['company']['universal_id'] }}</td>
                            <td class="sub-td">{{ $cal_member['member']['last_name'] }}</td>
                            <td class="sub-td">{{ $cal_member['member']['first_name'] }}</td>
                            <td class="sub-td">{{ $cal_member['member']['middle_name'] }}</td>
                            <td class="sub-td">{{ date("m/d/Y", strtotime($cal_member['member']['birth_date'])) }}</td>
                            <td class="sub-td">{{ $cal_member['member']['company']['coverage_plan']['name'] }}</td>
                            <td class="sub-td">{{ $cal_member['member']['company']['company_deployment']['name'] }}</td>
                            <td class="sub-td">{{ $data['payment_mode']['name'] }}</td>
                            <td class="sub-td">{{ number_format($cal_member['monthly_premium'], 2) }}</td>
                            <td class="sub-td">{{ number_format($cal_member['paid_amount'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="font-size:18pt;text-align:right;" colspan=8><strong>TOTAL:</strong></td>
                        <td class="sub-td">{{ number_format($data['total_monthly_premium'], 2) }}</td>
                        <td class="sub-td">{{ number_format($data['total_paid_amount'], 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan=10 style="text-align:center;font-size:20pt"><strong>No CAL Members</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>
 
</body>

<style>
    .main-table {
        border-collapse: collapse;
    }

    .main-td {
        padding : 5px;
        font-size : 12pt;
    }

    .main-table, .main-td {
        border: 1px solid black;
        width : 100%;
    }

    .td-field {
        text-align : left;
    }

    .td-value {
        text-align : center;
    }

    .sub-td {
        font-size : 12pt;
        text-align: center;
    }

    .sub-table {
        border-collapse: collapse;
        width : 100%;
    }
    
    .sub-table, .sub-td {
        border: 1px solid black;
        /* width: 100%; */
    }

    .break-page { page-break-before: always; }

}
</style>