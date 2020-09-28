<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
    <script src="main.js"></script>
</head>
<body style="font-size: 11-otherpt;">
    <table class="main-table">
        <thead>
            <tr>
                <th class="main-td td-value" colspan=2>Request For Payment</th>
            </tr>
            <tr>
                <th class="main-td td-value" colspan=2>Carewell Health Systems Inc.</th>
            </tr>
            <tr>
                <th class="main-td td-value" colspan=2>Outpatient</th>
            </tr>
            <tr>
                <th class="main-td td-value" colspan=2>Adjudication Form</th>
            </tr>
            <tr>
                <td colspan=2 height=20></td>
            </tr>
        </thead>
    </thead>
    <table class="main-table">
        <thead>
            <tr>
                <td class="main-td" style="width: 20%"><strong>Provider:</strong></td>
                <td class="main-td" style="width: 80%"><strong>{{ $data->provider->name }}</strong></td>
            </tr>
            <tr>
                <td class="main-td" style="width: 20%"><strong>SOA No.:</strong></td>
                <td class="main-td" style="width: 80%"><strong>{{ $data->soa_number }}</strong></td>
            </tr>
            <tr>
                <td class="main-td" style="width: 20%"><strong>Received:</strong></td>
                <td class="main-td" style="width: 80%"><strong>{{ $data->received_date }}</strong></td>
            </tr>
            <tr>
                <td class="main-td" style="width: 20%"><strong>Due:</strong></td>
                <td class="main-td" style="width: 80%"><strong>{{ $data->due_date }}</strong></td>
            </tr>
            <tr>
                <td class="main-td" style="width: 20%"><strong>Prep. By:</strong></td>
                <td class="main-td" style="width: 80%"><strong>{{ $data->user->full_name }}</strong></td>
            </tr>
            <tr>
                <td class="main-td"><strong>Prep. Date:</strong></td>
                <td class="main-td"><strong>{{ $data->created_at }}</strong></td>
            </tr>
            <tr>
                <td colspan=2 height=20></td>
            </tr>
        </thead>
    </table>
    <table class="main-table">
        <thead>
            <tr>
                <th class="main-td">No.</th>
                <th class="main-td">Px</th>
                <th class="main-td">Ap No.</th>
                <th class="main-td">Card No.</th>
                <th class="main-td">Date</th>
                <th class="main-td">Lab</th>
                <th class="main-td">Amt</th>
                <th class="main-td">D/A</th>
                <th class="main-td">c/o CW</th>
                <th class="main-td">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->payable_availment as $key => $availment)
            <tr>
                <td class="main-td">{{ $key + 1 }}</td>
                <td class="main-td">{{ $availment->availment->member->fullname }}</td>
                <td class="main-td">{{ $availment->availment->approval_id }}</td>
                <td class="main-td">{{ $availment->availment->carewell_id }}</td>
                <td class="main-td">{{ $availment->availment->date_avail }}</td>
                <td class="main-td">
                    @foreach($availment->availment->availment_procedure as $procedure)
                        {{ $procedure->procedure->name }}
                    @endforeach
                </td>
                <td class="main-td"  style="text-align: right !important">
                    <?php
                        $total_availment_procedure_gross_amount[$key] = $availment->availment->procedures_gross_amount_total ? $availment->availment->procedures_gross_amount_total : 0;
                        echo $total_availment_procedure_gross_amount[$key];
                    ?>
                </td>
                <td class="main-td"  style="text-align: right !important">
                    <?php
                        $total_amount_doctor_pf = array();
                        $amount_doctor_pf = 0;
                        foreach ($availment->availment->availment_doctor as $doctor) 
                        {
                            $fee = $doctor->doctors_fee ? $doctor->doctors_fee : 0;
                            $amount_doctor_pf += $fee;
                            $total_amount_doctor_pf[$key] = $fee;
                        }

                        $total_d_a[$key] = 0;
                        echo $total_d_a[$key] ? $total_d_a[$key] : '';
                    ?>
                </td>
                <td class="main-td"  style="text-align: right !important">
                    <?php
                        $total_amount_charged_to_carewell[$key] = ($availment->availment->procedures_carewell_charged_total + $availment->availment->doctors_carewell_charged_total) - $amount_doctor_pf;
                        echo number_format($total_amount_charged_to_carewell[$key], 2);
                    ?>
                </td>
                <td class="main-td">{{ $availment->remarks }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan=6 class="main-td" style="text-align: right;"><strong> Grand Total</strong></td>
                <td class="main-td"  style="text-align: right !important"><strong>{{ number_format(array_sum($total_availment_procedure_gross_amount), 2) }}</strong></td>
                <td class="main-td"  style="text-align: right !important"><strong>{{ number_format(array_sum($total_d_a), 2) }}</strong></td>
                <td class="main-td"  style="text-align: right !important"><strong>{{ number_format(array_sum($total_amount_charged_to_carewell), 2) }}</strong></td>
                <td class="main-td"></td>
            </tr>
            <tr>
                <td colspan=14 height=20></td>
            </tr>
            <?php $total_amount_doctor_pf = array(); ?>
            <tr>
                <td colspan=6 class="main-td align-right"><strong>PLEASE MAKE CHECK PAYABLE TO:</strong></td>
                <td class="main-td td-value"  style="text-align: right !important">
                    <strong>
                        <?php
                            $grand_total = ( array_sum($total_amount_charged_to_carewell)  - array_sum($total_d_a) );
                            echo number_format($grand_total,2);
                        ?>
                    </strong>
                </td>
                <td colspan=7 class="main-td td-value"><strong>{{ $data->provider->name }}</strong></td>
            </tr>
            <?php $total_doctor_fee = array(); ?>
            <tr>
                <td class="main-td align-right" colspan=6><strong>TOTAL:</strong></td>
                <td class="main-td td-value" style="text-align: right !important"><strong>{{ number_format( (array_sum($total_doctor_fee) + $grand_total ), 2) }}</strong></td>
                <td class="main-td" colspan=8></td>
            </tr>
        </tbody>
    </table>
    <table class="main-table">
        <thead>
            <tr>
                <td class="main-td" colspan=2>Checked & Verified by:</td>
                <td class="main-td" colspan=2>Validated by:</td>
                <td class="main-td" colspan=2>Noted by:</td>
                <td class="main-td">Audited by:</td>
                <td class="main-td">Noted by:</td>
            </tr>
            <tr>
                <td class="main-td" height=20 colspan=2></td>
                <td class="main-td" height=20 colspan=2></td>
                <td class="main-td" height=20 colspan=2></td>
                <td class="main-td" height=20 colspan=2></td>
                <td class="main-td" height=20 colspan=2></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th class="main-td-other" colspan=10>Carewell Contact No</th>
            </tr>
            <tr>
                <td class="main-td-other align-right" colspan=5>02-0935-1237 / 02-871-5586 / 02-435-6015</td>
                <td class="main-td-other" colspan=5><strong>Sun</strong> - 0932-8625697</td>
            </tr>
            <tr>
                <td class="main-td-other align-right" colspan=5><strong>Globe</strong> - 0917-5387499</td>
                <td class="main-td-other" colspan=5><strong>Smart</strong> - 0998-9884692</td>
            </tr>
            <tr>
                <td colspan=10 height=20></td>
            </tr>
        </tbody>
    </table>
</body>
</html>


<style>
    .main-table {
        border-collapse: collapse;
        table-layout: fixed;
    }

    .main-td {
        padding : 2px;
        font-size : 11pt;
    }
    .main-td-other {
        padding : 2px;
        font-size : 8pt;
    }

    .main-table, .main-td {
        border: 1px solid black;
        width : 100%;
    }
    .main-table, .main-td-other {
        border: 1px solid black;
        width : 100%;
    }

    .td-field {
        text-align : left;
    }

    .align-right {
        text-align : right;
    }

    .td-value {
        text-align : center;
    }

    .availment-td {
        font-size : 8pt;
        text-align: center;
    }

    .availment-table {
        border-collapse: collapse;
    }
    
    .availment-table, .availment-td {
        border: 1px solid black;
        width: 100%;
    }
</style>
