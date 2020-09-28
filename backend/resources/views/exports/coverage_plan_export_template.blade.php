<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Coverage Plan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
    <script src="main.js"></script>
</head>
<body>
    <?php
        $colspan = 2
    ?>
    <table>
        <tr>
            <td style="border-right : none">
                <!-- <img src= {{ public_path().'\images\carewell-logo.jpg' }} height=100 width=200> -->
                <img src= "http://carewellv2-api.digimahouse.com/images/carewell-logo.jpg" height=100 width=200>
                
            </td>
            <td colspan=3 style="border-left : none">
                CAREWELL HEALTH SYSTEMS, INC.
            </td>
        </tr>
        <tr>
            <td colspan=2>Coverage Plan</td>
            <td colspan=2 align="right">{{ $data->name }}</td>
        </tr>

        <!-- COVERAGE PLAN HEADER -->
        <tr>
            <td class="label-description">Monthly Premium:</td>
            <td class="label-value label-description">PHP {{ number_format($data->monthly_premium,2) }}</td>
            <td class="label-description">Age:</td>
            <td class="label-value label-description">{{ $data->age_bracket_from}} - {{ $data->age_bracket_to}}</td>
        </tr>
        <tr>
            <td class="label-description">Handling Fee:</td>
            <td class="label-value label-description">PHP {{ number_format($data->handling_fee,2) }}</td>
            <td class="label-description">Processing Fee:</td>
            <td class="label-value label-description">PHP {{ number_format($data->processing_fee,2) }}</td>
        </tr>
        <tr>
            <td class="label-description">Card Fee:</td>
            <td class="label-value label-description">PHP {{ number_format($data->card_fee,2) }}</td>
            <td class="label-description">Hospital Income Benefits:</td>
            <td class="label-value label-description">PHP {{ number_format($data->hospital_income_benefit,2) }}</td>
        </tr>
        <tr>
            <td class="label-description">Pre-existing:</td>
            <td class="label-value label-description">{{ $data->pre_existing->name }}</td>
            <td class="label-description">Annual Benefit Limit:</td>
            <td class="label-value label-description">PHP {{ number_format($data->annual_benefit_limit,2)}}</td>
        </tr>
        <tr>
            <td class="label-description">Maximum Benefit Limit:</td>
            <td class="label-value label-description">PHP {{ number_format($data->maximum_benefit_limit,2) }}</td>
            <td colspan=2 class="label-value label-description">{{ $data->max_limit_per == 1 ? "Per Year" : "Per Illness/Disease"}}</td>
        </tr>
        <!-- COVERAGE PLAN HEADER -->

        <tr>
            <td colspan=4>
                <br>
            </td>
        </tr>
        @foreach($data->coverage_plan_benefits as $key => $coverage_plan_benefits)
            <tr>
                <td colspan={{$colspan}}><p class="background-benefits">{{ $key+1 }} {{ $coverage_plan_benefits->benefit_type->name }}</p></td>
                @if( !empty($coverage_plan_benefits->limit_per_month) )
                    @if( $coverage_plan_benefits->limit_per_month > $coverage_plan_benefits->limit_per_year )
                        <td class="label-value" colspan={{$colspan}}><p class="background-benefits">PHP {{ number_format($coverage_plan_benefits->covered_amount,2) }} / {{ number_format($coverage_plan_benefits->limit_per_month) }} time(s) per Month</p></td>
                    @else
                        <td class="label-value" colspan={{$colspan}}><p class="background-benefits">PHP {{ number_format($coverage_plan_benefits->covered_amount,2) }} / {{ number_format($coverage_plan_benefits->limit_per_year) }} time(s) per Year / {{ number_format($coverage_plan_benefits->limit_per_month) }} time(s) per Month</p></td>
                    @endif
                @else
                    <td class="label-value" colspan={{$colspan}}><p class="background-benefits">PHP {{ number_format($coverage_plan_benefits->covered_amount,2) }} / Year</p></td>
                @endif
               
            </tr>
            @foreach($coverage_plan_benefits->coverage_plan_procedures as $benefitKey => $coverage_plan_procedures)
                <tr>
                    <td class="procedure-indention" colspan={{$colspan}}>{{ $key+1 }}.{{ $benefitKey+1 }} {{ ucwords(strtolower($coverage_plan_procedures->procedures->name)) }}</td>
                    @if( empty($coverage_plan_procedures->amount) )
                        <td class="label-value" colspan={{$colspan}}>
                            {{ $coverage_plan_benefits->charges }}  
                        </td>
                    @else
                        <td class="label-value" colspan={{$colspan}}>
                            PHP {{ number_format($coverage_plan_procedures->amount,2) }}
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    </table>
    <footer>
        <span>Printed by: {{$user}} {{date('m/d/Y h:i:s A')}}</span>
    </footer>
    
</body>
</html>

<style>
    body{
        font-family: 'Montserrat', sans-serif !important;
    }
    table, th, td {
        border: 1px solid black;
        width : 100%;
    }
    table {
        border-collapse: collapse;
    }
    td {
        padding : 10px;
    }
    .label-description {
        white-space: nowrap
    }
    .label-value {
        text-align : center;
    }
    .procedure-indention {
        text-indent : 30px;
    }
    p.background-benefits {
        background-color : yellow;
        font-weight : bold;
    }
    footer {
        position: fixed; 
        bottom: 0cm; 
        left: 0cm; 
        right: 0cm;
        text-align: right;
    }
</style>

