@extends('layouts.app')

@section('title', 'Assign Vehicle Management')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Assign Vehicle Management</h3>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="javascript:void(0);" class="btn btn-icon btn-trigger toggle-expand" data-target="pageMenu">
                        <em class="icon ni ni-more-v"></em>
                    </a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                <div class="form-group">
                                    <a href="{{ route('vehicles-assign.create') }}" class="form-control btn btn-primary" async-modal>
                                        <em class="icon ni ni-plus"></em>
                                        <span>Assign Vehicle</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-block">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table id="admins-dt" class="table nowrap nk-tb-list nk-tb-ulist dataTable no-footer" width="100%">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th><span class="sub-text">#</span></th>
                            @if (auth()->user()->isSuperAdmin())
                                <th><span class="sub-text">Manager</span></th>
                            @endif
                            <th><span class="sub-text">Prepration Managers</span></th>
                            <th><span class="sub-text">Vehicle</span></th>
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
                data: 'prepration_manager',
                name: 'prepration_manager',
            },
            {
                data: 'vehicle',
                name: 'vehicle',
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

        let dt = $('#admins-dt').DataTable({
            processing: true,
            serverSide: true,
            scrollX: false,
            ordering: false,
            autoWidth: true,
            ajax: {
                url: "{{ route('vehicles-assign.datatable') }}",
            },
            columns: columns,
        });
    </script>
@endpush
