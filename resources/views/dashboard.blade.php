@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="nk-block">
        <div class="row g-gs">
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
                            <li class="item">
                                <div class="info">
                                    <div class="title">Total Inspections</div>
                                    <div class="count">{{ $inspectionsCount }}</div>
                                </div>
                                <em class="icon bg-info-dim ni ni-users"></em>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush
