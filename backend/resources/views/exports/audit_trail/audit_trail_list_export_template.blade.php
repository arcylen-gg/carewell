<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Date</th>
            <th>Remark</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($query as $audit_trail)
            <tr>
                <td>{{$audit_trail->user->full_name}}</td>
                <td>{{$audit_trail->created_at}}</td>
                <td>{{$audit_trail->remarks}}</td>
                <td>{{$audit_trail->description}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<style>
    table, th, td {
        border: 1px solid black;
        width : 100%;
    }
    table {
        border-collapse: collapse;
        font-family: 'Montserrat', sans-serif !important;
    }
    td {
        padding : 10px;
    }
</style>
