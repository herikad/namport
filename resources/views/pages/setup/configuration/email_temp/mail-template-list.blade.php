@extends('layouts.layoutMaster')

{{-- page title --}}
@section('title', 'Mail Tempalte Setting')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

@php
    $page_action = Helper::pageAction(config('pages.form_type.email_template'));
@endphp


{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
<section>
    <h5 class="py-3 breadcrumb-wrapper mb-2">
        <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / {{ trans('pages.attribute_list.setup') }} / {{ trans('pages.attribute_list.configuration') }} / </span> {{ trans('pages.mail_template_list.mail') }}
    </h5>
    <div class="card">
        <div class="card-content">

            
                <div class="card-header pt-75 pb-75 d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ trans('pages.list_with_attr', ['attribute' => 'Email Template']) }}</h4>
                    @if (@$page_action->is_create == 1)
                        <a class="btn theme-btn-primary mr-1" href="{{ route('setup.config.email_temp.create') }}"><i class="bx bx-plus"></i>Add</a>
                    @endif
                </div>

            <div class="card-body">
                <!-- datatable start -->
                <div class="table-responsive">
                    <table class="table add-rows" id="mail_list_tbl">
                        <thead>
                            <tr>
                                <th style="display:none;"></th>
                                <th>#</th>
                                <th>{{ trans('pages.mail_template_list.mail_type') }}</th>
                                <th>{{ trans('pages.mail_template_list.mail_username') }}</th>
                                <th>{{ trans('pages.mail_template_list.title') }}</th>
                                <th>{{ trans('pages.status') }}</th>
                                <th>{{ trans('pages.action') }}</th>
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
    @include('scripts.setup.configuration.email_temp.mail_template_index_js')
@endsection
