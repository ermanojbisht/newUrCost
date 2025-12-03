<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Unit</th>
            <th>Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resources as $resource)
            <tr>
                <td>{{ $resource['resCode'] }}</td>
                <td>{{ $resource['name'] }}</td>
                <td>{{ $resource['unit'] }}</td>
                <td>{{ $resource['rate'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
