@extends('layouts.layoutMaster')

{{-- page title --}}
@section('title', 'General Settings')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

@php
    $page_action = Helper::pageAction(config('pages.form_type.general_settings'));
@endphp

{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
    <section>
        <h5 class="py-3 breadcrumb-wrapper mb-2">
            <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> /
                {{ trans('pages.attribute_list.setup') }} / Configuration / </span> General
        </h5>
        <div class="card w-98">
            <div class="card-content">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"> General </h4>
                </div>
                <hr class="m-0">

                <div class="card-body">
                    <div class="row pills-stacked">
                        <div class="col-md-3 col-sm-12  box-scroll p-2" style=" height:500px;">
                            <ul class="nav nav-pills flex-column text-center text-md-left term_menu">

                                @if (count($term_categories) > 0)

                                    @foreach ($term_categories as $i => $cat)
                                        <li class="nav-item mr-px-5">
                                            <a class="nav-link cursor-pointer {{ $i == 0 ? 'active' : '' }}"
                                                id="stacked-pill-1" data-bs-toggle="pill"
                                                data-bs-target="#term-cat-{{ $i }}"
                                                aria-controls="true">{{ $cat->category_details }}</a>
                                        </li>
                                    @endforeach

                                @endif

                            </ul>
                        </div>
                        <div class="col-md-9 col-sm-12 row">
                            <div class="tab-content p-3 col-12">

                                @if (count($term_categories) > 0)

                                    @foreach ($term_categories as $i => $cat)
                                        <div class="tab-pane {{ $i == 0 ? 'active' : '' }}"
                                            id="term-cat-{{ $i }}" role="tabpanel"
                                            aria-labelledby="stacked-pill-2" aria-expanded="false">
                                            <div class="col-12">

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h4 class="card-title mb-0">
                                                        {{ trans('pages.list_with_attr', ['attribute' => $cat->category_details]) }}
                                                    </h4>
                                                    <div class="d-flex">
                                                        @if (@$page_action->is_create == 1)
                                                            <a class=" btn theme-btn-primary btn-sm add-term" href="#"
                                                                data-title="{{ $cat->category_details }}"
                                                                data-term-category-id="{{ $cat->term_category_id }}"><i
                                                                    class="bx bx-plus"></i></a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="table">
                                                    <table id="role-cat-tbl-{{ $i }}"
                                                        class="table general-stg-table"
                                                        data-term-category-id="{{ $cat->term_category_id }}">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th> Code</th>
                                                                <th> Name</th>
                                                                <th> Details</th>
                                                                <th>{{ trans('pages.action') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if (count($cat->data))
                                                                @foreach ($cat->data as $ti => $td)
                                                                    <tr>
                                                                        <td>{{ $ti + 1 }}</td>
                                                                        <td>{{ $td->term_code }}</td>
                                                                        <td>{{ $td->term_name }}</td>
                                                                        <td>{{ !empty($td->term_details) ? $td->term_details : '' }}
                                                                        </td>
                                                                        <td>
                                                                            @if ($td->is_default != 1)
                                                                                @if (@$page_action->is_update == 1)
                                                                                    <a href="#"
                                                                                        data-term-id="{{ $td->term_id }}"
                                                                                        data-term-category-id="{{ $td->term_category_id }}"
                                                                                        data-title="{{ $cat->category_details }}"
                                                                                        class="float-top edit-term"><i
                                                                                            class="bx bx-edit theme-text-secondary bx-sm"></i></a>
                                                                                @endif
                                                                                @if (@$page_action->is_update == 1)
                                                                                    <a href="#"
                                                                                        data-term-id="{{ $td->term_id }}"
                                                                                        data-term-category-id="{{ $td->term_category_id }}"
                                                                                        data-title="{{ $cat->category_details }}"
                                                                                        class="float-top remove-term"><i
                                                                                            class="bx bx-trash theme-text-secondary bx-sm"></i></a>
                                                                                @endif
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade animate__animated animate__jackInTheBox" id="termAddUpdateModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <form action="{{ route('setup.config.general-settings.store') }}" method="POST" id="termFrm">
                    @csrf
                    <div class="modal-header pl-1 pr-1">
                        <h5 class="modal-title" id="term-modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body pl-1 pr-1">
                        @csrf
                        <div class="row">

                            <div class="col-md-12 col-12">
                                <div class="form-group mb-3">
                                    <label id="term_code_title"></label>
                                    <input class="form-control" maxlength="50" name="term_code" type="text" Required>
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group mb-3">
                                    <label id="term_title"></label>
                                    <input class="form-control" name="term_name" maxlength="50" type="text" Required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="controls mb-3">
                                    <label id="term_detail_title"></label>
                                    <textarea class="form-control" name="term_details" placeholder="Enter detail..."></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                              <label id="term_icon"></label>
                                <div class="card border shadow-none mb-1 app-file-info">
                                    <div class="card-body p-1 text-center">
                                        <div id="profile_pic_1_preview"
                                            class="image-fixed flex-shrink-0  mx-sm-0 mx-auto pt-1">
                                            <img src="" alt="" class="object-fit-md-contain term_icon" height="100"
                                                width="120" style="object-fit: cover;" onerror="this.src='{{ asset('/no_image.jpg') }}'">
                                        </div>
                                    </div>
                                    <div class="card-footer p-1 justify-content-center d-flex">
                                        <div class="form-group add-new-file text-center">
                                            <label for="profile_pic_1" class="btn btn-sm btn-primary">Select</label>
                                            <input type="file" name="term_icon" class="d-none" id="profile_pic_1"
                                                accept="image/x-png, image/jpeg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input name="term_id" type="hidden">
                            <input name="term_category_id" type="hidden">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button id="termFrmBtn" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- vendor scripts --}}
@section('vendor-scripts')

@endsection

{{-- page scripts --}}
@section('page-script')
    @include('scripts.setup.configuration.general_setting.index_js')
@endsection
