@extends('layouts.layoutMaster')

{{-- title --}}
@section('title', 'Dashboard')

{{-- vendor styles --}}
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css') }}" />
@endsection

{{-- page styles --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dashboard-analytics.css') }}">
    <style>
        #member_list_wrapper .dataTables_scrollHeadInner {
            width: 100% !important;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-gap: 10px;
            margin-bottom: 20px;
        }

        .grid .card-body {
            padding: 20px 10px;
        }

        #order_list_tbl_wrapper {
            max-height: 500px;
        }
    </style>
@endsection
@php
    $page_action = Helper::pageAction(config('pages.form_type.dashboard'));
@endphp

@section('content')
    <section>

        <div class="pt-4">
            <div class="grid">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <a href="#">
                                        <span class="avatar-initial bg-label-danger rounded-circle">
                                            <i class="fas fa-user-tie fs-4"></i></span>
                                    </a>
                                </div>
                                <div class="card-info">
                                    <a href="#">
                                        <h5 class="card-title mb-0 me-2">10</h5>
                                    </a>
                                    <a class="primary-text-color h6" href="#">Clients</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <a href="#">
                                        <span class="avatar-initial bg-label-dark rounded-circle"><i
                                                class="bx bx-user fs-4"></i></span>
                                    </a>
                                </div>
                                <div class="card-info">
                                    <a href="#">
                                        <h5 class="card-title mb-0 me-2">20</h5>
                                    </a>
                                    <a class="primary-text-color h6" href="#">Projects</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <a href="#">
                                        <span class="avatar-initial bg-label-danger rounded-circle">
                                            <i class="bx bx-tennis-ball fs-4"></i></span>
                                    </a>
                                </div>
                                <div class="card-info">
                                    <a href="#">
                                        <h5 class="card-title mb-0 me-2">25</h5>
                                    </a>
                                    <a class="primary-text-color h6" href="#">Sessions</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        {{-- Member list - graph --}}
        <div class="row mt-4">
            <div class="col-md-12 col-sm-12">
                <div class="card  p-3 mt-2 shadow-none">
                    <div class="row">
                        <div class="col-sm-4">
                            <h4>Members</h4>
                        </div>
                        <div class="col-sm-4 chart_year_block">
                            <div class="controls">
                                <select name="chart-year" class="form-control chart_year select2" id="chart-year-select"
                                    data-element-ref="select2" required>
                                    @foreach (range(strftime('%Y', time()) - 15, strftime('%Y', time())) as $year)
                                        <option selected={{ $year == strftime('%Y', time()) ? true : false }}
                                            value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <select name="member_block_select" class="form-select member_block_select">
                                <option value="chart" selected>Graph View</option>
                                <option value="list">List</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 member_list_block d-none shadow-none">
                        <div class="table-responsive">
                            <div class="card shadow-none">
                                <div class="table-responsive">
                                    <table class="table" id="member_list">
                                        <thead>
                                            <tr>
                                                <th style="display:none;"></th>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Type</th>
                                                <th>Join Date</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Scatter Chart -->
                    <div class="col-12 member_chart_block">
                        <div class="card shadow-none">
                            <div id="dashboard-analytics-chart" class="dashboard-analytics-chart card-body pb-1">

                            </div>
                        </div>
                    </div>
                    <!-- Member Scatter Chart -->
                </div>
            </div>
        </div>


    </section>

@endsection

{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js') }}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
    @include('scripts.dashboard.admin.index_js')
@endsection
