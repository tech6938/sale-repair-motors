@extends('layouts.app')

@section('title', 'Admins Management')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Admins Management</h3>
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
                                    <a href="{{ route('admins.create') }}" class="form-control btn btn-primary" async-modal>
                                        <em class="icon ni ni-plus"></em>
                                        <span>Invite New Admin</span>
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
                            <th><span class="sub-text">Name</span></th>
                            <th><span class="sub-text">Phone</span></th>
                            <th><span class="sub-text">Address</span></th>
                            <th><span class="sub-text">Status</span></th>
                            <th><span class="sub-text">Comments</span></th>
                            <th><span class="sub-text">Created</span></th>
                            <th><span class="sub-text">Updated</span></th>
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
        let dt = $('#admins-dt').DataTable({
            processing: true,
            serverSide: true,
            scrollX: false,
            ordering: false,
            autoWidth: true,
            ajax: {
                url: "{{ route('admins.datatable') }}",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'uuid',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'comments',
                    name: 'comments',
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
            ],
        });
    </script>
@endpush
