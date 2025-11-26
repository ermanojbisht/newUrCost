<table>
    <thead>
        <tr>
            <th>Resource ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Unit</th>
            <th>Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resources as $resource)
        <tr>
            <td>{{ $resource->id }}</td>
            <td>{{ $resource->secondary_code ?: 'RES_' . $resource->id }}</td>
            <td>{{ $resource->name }}</td>
            <td>{{ $resource->unit->name ?? '' }}</td>
            <td>{{ $resource->current_rate }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
