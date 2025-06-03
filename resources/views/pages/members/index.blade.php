@extends('layouts.layoutMaster')

{{-- page title --}}
@section('title', 'Members')

{{-- vendor styles --}}
@section('vendor-styles')

@endsection
@section('page-styles')

@endsection

@section('content')
    <section>
        <h5 class="py-3 breadcrumb-wrapper mb-2">
            <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> /
                {{ trans('pages.attribute_list.Members') }}
        </h5>
        <div class="card">
            <div class="card-content">

                <div class="card-header pt-75 pb-75 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ trans('pages.list_with_attr', ['attribute' => 'Members']) }}</h4>

                </div>

                <div class="card-body">

                    <!-- datatable start -->
                    <div class="table-responsive">
                        <table class="table" id="member_list_tbl">
                            <thead>
                                <tr>
                                    <th style="display:none;"></th>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Member Name</th>
                                    <th>Email</th>
                                    <th>Phone No</th>
                                    <th>Gender</th>
                                    <th>Type</th>
                                    <th>Level</th>
                                    <th>Ratings</th>
                                    <th>Deleted On</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- datatable ends -->
                </div>

            </div>
        </div>
    </section>
@endsection

{{-- vendor scripts --}}
@section('vendor-scripts')

@endsection

{{-- page scripts --}}
@section('page-script')
@include('scripts.members.index_js')
@endsection
