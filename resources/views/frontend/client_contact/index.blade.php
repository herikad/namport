@extends('layouts.layoutMaster')

{{-- page title --}}
@section('title', 'Client Contact')

{{-- vendor css --}}

{{-- vendor styles --}}
@section('vendor-style')
@endsection

@php
$page_action = Helper::pageAction(config('pages.form_type.client_contacts'));
@endphp

{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
<section>
    <div class="card">
        <div class="card-content">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title text-primary"> Client Contact </h4>
                {{-- @if (@$page_action->is_create == 1) --}}
                <a class="btn btn-primary mr-1 mb-1" href="{{ route('client_contacts.create') }}"><i class="bx bx-plus"></i>Add</a>
                {{-- @endif --}}
            </div>

            <div class="card-body">
                <!-- datatable start -->
                <div class="table-responsive">
                    <table class="table add-rows" id="contact_list_tbl">
                        <thead>
                            <tr>
                                <th style="display:none;"></th>
                                <th>#</th>
                                <th>Profile</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Mobile No.</th>
                                <th>Added On</th>
                                <th>Date Of Joining</th>
                                <th>Status</th>
                                {{-- <th>Action</th> --}}
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
<script>
  var page_action = @json(Helper::pageAction(config('pages.form_type.client_contacts')));
  console.log(page_action)
  var permission_denied = "{{ config('custom.permission_denied') }}";
</script>
@include('scripts.client_contact.index_js')
@endsection
