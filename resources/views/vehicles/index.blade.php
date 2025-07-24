@extends('layouts.app')

@section('title', 'Vehicles Management')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Vehicles Management</h3>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered card-preview">
            <div class="card-inner overflow-x-scroll">
                <table id="vehicles-dt" class="table nowrap nk-tb-list nk-tb-ulist dataTable no-footer" width="100%">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th><span class="sub-text">#</span></th>
                            @if (auth()->user()->isSuperAdmin())
                                <th><span class="sub-text">Manager</span></th>
                            @endif
                            <th><span class="sub-text">Vehicle</span></th>
                            <th><span class="sub-text">Registration</span></th>
                            <th><span class="sub-text">Inspection Started At</span></th>
                            <th><span class="sub-text">Inspection Completed At</span></th>
                            <th><span class="sub-text">Vehicle Created</span></th>
                            <th><span class="sub-text">Vehicle Updated</span></th>
                            <th class="text-right"><span class="sub-text">Actions</span></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let columns = [{
                data: 'DT_RowIndex',
                name: 'uuid',
                orderable: false,
                searchable: false
            },
            {
                data: 'vehicle',
                name: 'vehicle'
            },
            {
                data: 'registration',
                name: 'registration'
            },
            {
                data: 'started_at',
                name: 'started_at',
                searchable: false
            },
            {
                data: 'completed_at',
                name: 'completed_at',
                searchable: false
            },
            {
                data: 'created',
                name: 'created',
                searchable: false
            },
            {
                data: 'updated',
                name: 'updated',
                searchable: false
            },
            {
                data: 'actions',
                name: 'actions',
                searchable: false
            },
        ];

        @if (auth()->user()->isSuperAdmin())
            columns.splice(1, 0, {
                data: 'manager',
                name: 'manager',
                orderable: false,
                searchable: false
            });
        @endif

        let dt = $('#vehicles-dt').DataTable({
            processing: true,
            serverSide: true,
            scrollX: false,
            ordering: false,
            autoWidth: true,
            ajax: {
                url: "{{ route('vehicles.datatable') }}",
            },
            columns: columns,
        });
    </script>
@endpush
