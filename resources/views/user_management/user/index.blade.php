@extends('layouts.layoutMaster')

{{-- page title --}}
@section('title', 'User')

{{-- vendor styles --}}
@section('vendor-styles')

@endsection

@php
    $page_action = Helper::pageAction(config('pages.form_type.user'));
@endphp

{{-- page styles --}}
@section('page-styles')

@endsection

@section('content')
    <section>
      <h5 class="py-3 breadcrumb-wrapper mb-2">
        <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / Setup / User Management /</span> User
      </h5>
        <div class="card">
            <div class="card-content">

                    <div class="card-header pt-75 pb-75 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ trans('pages.list_with_attr', ['attribute' => 'User']) }}</h4>

                        @if (@$page_action->is_create == 1)
                            <div class="d-flex justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                <a class="btn btn-primary  md-1 float-right" href="{{route('setup.user_management.user.create')}}"><i class="bx bx-plus"></i>Add</a>
                            </div>
                        @endif
                    </div>

                <div class="card-body">


                    <!-- datatable start -->
                    <div class="table-responsive">
                        <table class="table add-rows" id="user_list_tbl">
                            <thead>
                                <tr>
                                    <th style="display:none;"></th>
                                    <th>#</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    {{-- <th>User Type</th> --}}
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
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
    @include('scripts.user_management.user.index_js')
@endsection
