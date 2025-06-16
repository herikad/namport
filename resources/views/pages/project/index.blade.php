@extends('layouts.layoutMaster')

{{-- page title --}}
@section('title', 'Project')

{{-- vendor styles --}}
@section('vendor-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/forms/validation/form-validation.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/pickers/pickadate/pickadate.css') }}">
@endsection

<!-- @php
    $page_action = Helper::pageAction(config('pages.form_type.user'));
@endphp -->

{{-- page styles --}}
@section('page-styles')
@endsection
<style>
    .upload-section {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .upload-box {
      border: 2px dashed #ccc;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
      transition: border-color 0.3s;
    }

    .upload-box:hover {
      border-color: #999;
    }

    .logo-box {
      width: 160px;
      height: 160px;
    }

    .banner-box {
      width: 500px;
      height: 160px;
    }

    .upload-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .upload-placeholder {
      position: absolute;
      text-align: center;
      color: #999;
      font-size: 14px;
    }

    input[type="file"] {
      display: none;
    }

    .nav-tabs .nav-link.active {
      font-weight: 600;
      color: #0d3b66 !important;
      border-bottom: 2px solid #0d3b66;
    }
  </style>


@section('content')
    <section>
        <div class="card">
            <div class="card-content">

                    <div class="card-header pt-75 pb-75 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ trans('pages.list_with_attr', ['attribute' => 'Project']) }}</h4>

                        {{-- @if (@$page_action->is_create == 1) --}}
                            <div class="d-flex justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                <a class="btn btn-primary  md-1 float-right" href="#" id="open_project_model"><i class="bx bx-plus" ></i>Add</a>
                            </div>

                            
                        {{-- @endif --}}
                    </div>

                <div class="card-body">
                    <!-- datatable start -->
                    <div class="table-responsive">
                        <table class="table add-rows" id="project_list_tbl">
                            <thead>
                                <tr>
                                    <th style="display:none;"></th>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Project Name</th>
                                    <th>MIA</th>
                                    <th>Our Team</th>
                                    <th>Client Team</th>
                                    <th>Status</th>
                                    <th>Completion</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Pending Tasks</th>
                                    <th>Document</th>
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

<!-- Include modal -->
@include('pages.project.includes.create')

@endsection



{{-- vendor scripts --}}
@section('vendor-scripts')
<script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('vendors/js/pickers/daterange/daterangepicker.js') }}"></script>
@endsection

{{-- page scripts --}}

@section('page-script')
   <script src="{{ asset('extensions/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
   @include('scripts.project.index_js')
@endsection
