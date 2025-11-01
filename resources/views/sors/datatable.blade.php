@extends('layouts.layout001.app')

@section('title', $sor->name . ' - SOR Data Table')

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $sor->name }} - Data Table View</h1>

    <div class="card">
        <table id="sor-items-datatable" class="display min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Code</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script>
        $(function () {
            $('#sor-items-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('api.sors.items-datatable', $sor) }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'item_code', name: 'item_code' },
                    { data: 'name', name: 'name' },
                    { data: 'item_type', name: 'item_type' },
                    { data: 'unit_name', name: 'unit_name' },
                ]
            });
        });
    </script>
@endpush
