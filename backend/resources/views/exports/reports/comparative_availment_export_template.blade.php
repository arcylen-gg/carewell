<table class="main-table">
    <thead>
    </thead>
    <tbody>
        <tr>
            <th class="main-td">Company</th>
            <th class="main-td">January {{$request['year_from']}}</th>
            <th class="main-td">January {{$request['year_to']}}</th>
            <th class="main-td">February {{$request['year_from']}}</th>
            <th class="main-td">February {{$request['year_to']}}</th>
            <th class="main-td">March {{$request['year_from']}}</th>
            <th class="main-td">March {{$request['year_to']}}</th>
            <th class="main-td">April {{$request['year_from']}}</th>
            <th class="main-td">April {{$request['year_to']}}</th>
            <th class="main-td">May {{$request['year_from']}}</th>
            <th class="main-td">May {{$request['year_to']}}</th>
            <th class="main-td">June {{$request['year_from']}}</th>
            <th class="main-td">June {{$request['year_to']}}</th>
            <th class="main-td">July {{$request['year_from']}}</th>
            <th class="main-td">July {{$request['year_to']}}</th>
            <th class="main-td">August {{$request['year_from']}}</th>
            <th class="main-td">August {{$request['year_to']}}</th>
            <th class="main-td">September {{$request['year_from']}}</th>
            <th class="main-td">September {{$request['year_to']}}</th>
            <th class="main-td">October {{$request['year_from']}}</th>
            <th class="main-td">October {{$request['year_to']}}</th>
            <th class="main-td">November {{$request['year_from']}}</th>
            <th class="main-td">November {{$request['year_to']}}</th>
            <th class="main-td">December {{$request['year_from']}}</th>
            <th class="main-td">December {{$request['year_to']}}</th>
            <th class="main-td">Grand Total {{$request['year_from']}}</th>
            <th class="main-td">Grand Total {{$request['year_to']}}</th>
        </tr>
        @foreach($data['item'] as $key => $row)
        <tr>
            <td class="main-td">
                {{$row->name}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[0]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[0]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[1]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[1]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[2]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[2]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[3]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[3]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[4]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[4]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[5]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[5]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[6]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[6]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[7]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[7]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[8]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[8]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[9]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[9]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[10]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[10]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_from[11]}}
            </td>
            <td class="main-td">
                {{$row->count_monthly_to[11]}}
            </td>
            <td class="main-td">
                {{$row->count_year_from}}
            </td>
            <td class="main-td">
                {{$row->count_year_to}}
            </td>
        </tr>
        @endforeach
        <tr>
            <td class="main-td">
                Total
            </td>
            <td class="main-td">
                {{$data['from'][0]}}
            </td>
            <td class="main-td">
                {{$data['to'][0]}}
            </td>
            <td class="main-td">
                {{$data['from'][1]}}
            </td>
            <td class="main-td">
                {{$data['to'][1]}}
            </td>
            <td class="main-td">
                {{$data['from'][2]}}
            </td>
            <td class="main-td">
                {{$data['to'][2]}}
            </td>
            <td class="main-td">
                {{$data['from'][3]}}
            </td>
            <td class="main-td">
                {{$data['to'][3]}}
            </td>
            <td class="main-td">
                {{$data['from'][4]}}
            </td>
            <td class="main-td">
                {{$data['to'][4]}}
            </td>
            <td class="main-td">
                {{$data['from'][5]}}
            </td>
            <td class="main-td">
                {{$data['to'][5]}}
            </td>
            <td class="main-td">
                {{$data['from'][6]}}
            </td>
            <td class="main-td">
                {{$data['to'][6]}}
            </td>
            <td class="main-td">
                {{$data['from'][7]}}
            </td>
            <td class="main-td">
                {{$data['to'][7]}}
            </td>
            <td class="main-td">
                {{$data['from'][8]}}
            </td>
            <td class="main-td">
                {{$data['to'][8]}}
            </td>
            <td class="main-td">
                {{$data['from'][9]}}
            </td>
            <td class="main-td">
                {{$data['to'][9]}}
            </td>
            <td class="main-td">
                {{$data['from'][10]}}
            </td>
            <td class="main-td">
                {{$data['to'][10]}}
            </td>
            <td class="main-td">
                {{$data['from'][11]}}
            </td>
            <td class="main-td">
                {{$data['to'][11]}}
            </td>
            <td class="main-td">
                {{$data['total_from']}}
            </td>
            <td class="main-td">
                {{$data['total_to']}}
            </td>
        </tr>
    </tbody>
</table>
<style>
    .main-table {
        border-collapse: collapse;
        table-layout: fixed;
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
