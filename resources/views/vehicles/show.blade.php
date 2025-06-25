@extends('layouts.app')

@section('title', 'Vehicles Management')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <div class="nk-block-head-sub">
                    <a class="back-to" href="{{ route('vehicles.index') }}">
                        <em class="icon ni ni-arrow-left"></em>
                        <span>Back to Listing</span>
                    </a>
                </div>
                <h3 class="nk-block-title page-title">{{ implode(' ', [$vehicle->make, $vehicle->model]) }} Details</h3>
            </div>

            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="javascript:void(0);" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                        <em class="icon ni ni-more-v"></em>
                    </a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                <a href="{{ route('vehicles.export', $vehicle->uuid) }}" class="btn btn-primary" target="_blank">
                                    <em class="icon ni ni-download"></em>
                                    <span>Export PDF</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Vehicle Details --}}
    <div class="nk-block">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <div class="row">
                    <div class="col-12">
                        <div class="nk-block">

                            {{-- Vehicle Title --}}
                            <div class="nk-block-head">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title title">
                                            {{ implode(' ', [$vehicle->make, $vehicle->model]) }}
                                            <span style="font-size: 0.6em;">{{ $vehicle->year }}</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Vehicle Basic Details --}}
                                <div class="col-lg-6">
                                    <ul class="data-list is-compact">
                                        <li class="py-1 px-3">
                                            <div class="data-col">
                                                <div class="data-label">
                                                    <h6>Fuel Type</h6>
                                                </div>
                                                <div class="data-value">{{ ucwords($vehicle->fuel_type) }}</div>
                                            </div>
                                        </li>
                                        <li class="py-1 px-3">
                                            <div class="data-col">
                                                <div class="data-label">
                                                    <h6>Color</h6>
                                                </div>
                                                <div class="data-value">{{ $vehicle->color }}</div>
                                            </div>
                                        </li>
                                        <li class="py-1 px-3">
                                            <div class="data-col">
                                                <div class="data-label">
                                                    <h6>Milage</h6>
                                                </div>
                                                <div class="data-value">{{ $vehicle->milage }}</div>
                                            </div>
                                        </li>
                                        <li class="py-1 px-3">
                                            <div class="data-col">
                                                <div class="data-label">
                                                    <h6>Registration</h6>
                                                </div>
                                                <div class="data-value">{{ $vehicle->registration }}</div>
                                            </div>
                                        </li>
                                        <li class="py-1 px-3">
                                            <div class="data-col">
                                                <div class="data-label">
                                                    <h6>Created At</h6>
                                                </div>
                                                <div class="data-value">{{ $vehicle->createdAt() }}</div>
                                            </div>
                                        </li>
                                        <li class="py-1 px-3">
                                            <div class="data-col">
                                                <div class="data-label">
                                                    <h6>Updated At</h6>
                                                </div>
                                                <div class="data-value">{!! $vehicle->updatedAt() !!}</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Vehicle Inspection Time --}}
                                <div class="col-lg-6">
                                    <ul class="data-list is-compact">
                                        <li class="py-1 px-3">
                                            <div class="data-col">
                                                <div class="data-label">
                                                    <h6>Inspection Started At</h6>
                                                </div>
                                                <div class="data-value">{{ frontendDateTime($vehicle->inspections->first()?->started_at) }}</div>
                                            </div>
                                        </li>
                                        <li class="py-1 px-3">
                                            <div class="data-col">
                                                <div class="data-label">
                                                    <h6>Inspection Completed At</h6>
                                                </div>
                                                <div class="data-value">{{ frontendDateTime($vehicle->inspections->first()?->completed_at) }}</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inspections --}}
    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-aside-wrap">
                {{-- Checklists --}}
                <div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg toggle-screen-lg" data-content="userAside" data-toggle-screen="lg"
                    bn data-toggle-overlay="true">
                    <div class="card-inner-group" data-simplebar="init">
                        <div class="simplebar-wrapper" style="margin: 0px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: auto;">

                                        <div class="card-inner p-0">
                                            <ul class="link-list-menu">
                                                @foreach ($checklists as $key => $checklist)
                                                    <li>
                                                        <a class="nav-link checklist" href="{{ route('vehicles.checklist', [$vehicle->uuid, $checklist->uuid]) }}" async-view>
                                                            <span>{{ $checklist->title }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: auto; height: {{ $checklists->count() * 54 + 10 }}px"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                            <div class="simplebar-scrollbar simplebar-visible" style="width: 0px; display: none;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                            <div class="simplebar-scrollbar simplebar-visible" style="height: 0px; display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="card-inner card-inner-lg">
                    <div class="tab-content">
                        <div class="tab-pane active" id="async-view-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.checklist').first().click();
        });
    </script>
@endpush
