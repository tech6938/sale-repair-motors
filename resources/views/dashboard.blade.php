@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="nk-block">
        <div class="row g-gs">
            {{-- Latest Admins --}}
            <div class="col-xxl-8">
                <div class="card card-full">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Latest Admins</h6>
                            </div>
                        </div>
                    </div>
                    <div class="nk-tb-list mt-n2">
                        <div class="nk-tb-item nk-tb-head">
                            @if (auth()->user()->isSuperAdmin())
                                <div class="nk-tb-col"><span>Owner</span></div>
                            @endif
                            <div class="nk-tb-col"><span>Name</span></div>
                            <div class="nk-tb-col tb-col-md"><span>Phone</span></div>
                            <div class="nk-tb-col"><span>Status</span></div>
                            <div class="nk-tb-col"><span class="d-none d-sm-inline">Comments</span></div>
                            <div class="nk-tb-col"><span class="d-none d-sm-inline">Created</span></div>
                            <div class="nk-tb-col"><span class="d-none d-sm-inline">Updated</span></div>
                        </div>
                        @forelse ($latestAdmins as $latestAdmin)
                            <div class="nk-tb-item">
                                @if (auth()->user()->isSuperAdmin())
                                    <div class="nk-tb-col tb-col-sm">
                                        <div class="user-card">
                                            <div class="user-avatar {{ getRandomColorClass() }}">
                                                {!! getAvatarHtml($latestAdmin->owner) !!}
                                            </div>
                                            <div class="user-info">
                                                @if (auth()->user()->id === $latestAdmin->owner->id)
                                                    <span class="tb-lead">{{ $latestAdmin->owner->name }}</span>
                                                @else
                                                    <a href="{{ route('admins.show', $latestAdmin->owner->uuid) }}" async-modal async-modal-size="lg">
                                                        <span class="tb-lead text-danger">{{ $latestAdmin->owner->name }}</span>
                                                    </a>
                                                @endif
                                                <span>{{ $latestAdmin->owner->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="nk-tb-col tb-col-sm">
                                    <div class="user-card">
                                        <div class="user-avatar {{ getRandomColorClass() }}">
                                            {!! getAvatarHtml($latestAdmin) !!}
                                        </div>
                                        <div class="user-info">
                                            <a href="{{ route('admins.show', $latestAdmin->uuid) }}" async-modal async-modal-size="lg">
                                                <span class="tb-lead text-danger">{{ $latestAdmin->name }}</span>
                                            </a>
                                            <span>{{ $latestAdmin->email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">{!! canEmpty($latestAdmin->phone) !!}</span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub">
                                        {!! $latestAdmin->status_badge !!}
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub">
                                        @if (empty($latestAdmin->admin_comments))
                                            {!! canEmpty($latestAdmin->admin_comments) !!}
                                        @else
                                            <a href="{{ route('admins.comments', $latestAdmin->uuid) }}" class="btn btn-icon btn-sm btn-light" async-modal data-bs-toggle="tooltip"
                                                title="View Comments" data-method="post">
                                                <em class="icon ni ni-comments"></em>
                                            </a>
                                        @endif
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub">
                                        {!! $latestAdmin->createdAt() !!}
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub">
                                        {!! $latestAdmin->updatedAt() !!}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="m-4">No record found</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="col-xxl-4 col-md-8 col-lg-6">
                <div class="card h-100">
                    <div class="card-inner">
                        <div class="card-title-group mb-2">
                            <div class="card-title">
                                <h6 class="title">Statistics</h6>
                            </div>
                        </div>
                        <ul class="nk-store-statistics">
                            <li class="item">
                                <div class="info">
                                    <div class="title">Total Admins</div>
                                    <div class="count">{{ $adminsCount }}</div>
                                </div>
                                <em class="icon bg-pink-dim ni ni-users"></em>
                            </li>
                            <li class="item">
                                <div class="info">
                                    <div class="title">Total Staffs</div>
                                    <div class="count">{{ $staffsCount }}</div>
                                </div>
                                <em class="icon bg-info-dim ni ni-users"></em>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Latest Staffs --}}
            <div class="col-xxl-8">
                <div class="card card-full">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Latest Staffs</h6>
                            </div>
                        </div>
                    </div>
                    <div class="nk-tb-list mt-n2">
                        <div class="nk-tb-item nk-tb-head">
                            @if (auth()->user()->isSuperAdmin())
                                <div class="nk-tb-col"><span>Owner</span></div>
                            @endif
                            <div class="nk-tb-col"><span>Name</span></div>
                            <div class="nk-tb-col tb-col-md"><span>Phone</span></div>
                            <div class="nk-tb-col"><span>Status</span></div>
                            <div class="nk-tb-col"><span class="d-none d-sm-inline">Comments</span></div>
                            <div class="nk-tb-col"><span class="d-none d-sm-inline">Created</span></div>
                            <div class="nk-tb-col"><span class="d-none d-sm-inline">Updated</span></div>
                        </div>
                        @forelse ($latestStaffs as $latestStaff)
                            <div class="nk-tb-item">
                                @if (auth()->user()->isSuperAdmin())
                                    <div class="nk-tb-col tb-col-sm">
                                        <div class="user-card">
                                            <div class="user-avatar {{ getRandomColorClass() }}">
                                                {!! getAvatarHtml($latestStaff->owner) !!}
                                            </div>
                                            <div class="user-info">
                                                @if (auth()->user()->id === $latestStaff->owner->id)
                                                    <span class="tb-lead">{{ $latestStaff->owner->name }}</span>
                                                @else
                                                    <a href="{{ route('admins.show', $latestStaff->owner->uuid) }}" async-modal async-modal-size="lg">
                                                        <span class="tb-lead text-danger">{{ $latestStaff->owner->name }}</span>
                                                    </a>
                                                @endif
                                                <span>{{ $latestStaff->owner->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="nk-tb-col tb-col-sm">
                                    <div class="user-card">
                                        <div class="user-avatar {{ getRandomColorClass() }}">
                                            {!! getAvatarHtml($latestStaff) !!}
                                        </div>
                                        <div class="user-info">
                                            <a href="{{ route('staffs.show', $latestStaff->uuid) }}" async-modal async-modal-size="lg">
                                                <span class="tb-lead text-danger">{{ $latestStaff->name }}</span>
                                            </a>
                                            <span>{{ $latestStaff->email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">{!! canEmpty($latestStaff->phone) !!}</span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub">
                                        {!! $latestStaff->status_badge !!}
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub">
                                        @if (empty($latestStaff->staff_comments))
                                            {!! canEmpty($latestStaff->staff_comments) !!}
                                        @else
                                            <a href="{{ route('staffs.comments', $latestStaff->uuid) }}" class="btn btn-icon btn-sm btn-light" async-modal
                                                data-bs-toggle="tooltip" title="View Comments" data-method="post">
                                                <em class="icon ni ni-comments"></em>
                                            </a>
                                        @endif
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub">
                                        {!! $latestStaff->createdAt() !!}
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub">
                                        {!! $latestStaff->updatedAt() !!}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="m-4">No record found</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush
