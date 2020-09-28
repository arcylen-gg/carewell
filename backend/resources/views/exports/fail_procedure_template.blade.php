

<table>
    <thead>
    
    </thead>
    <tbody>
    @foreach($data as $row)
    <tr>
            {{ $row->name }}
    </tr>
    @endforeach
    </tbody>
</table>