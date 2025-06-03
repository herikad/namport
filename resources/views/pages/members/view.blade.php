@extends('layouts.layoutMaster')

{{-- title --}}
@section('title', 'View Members')
{{-- vendor styles --}}
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />

@endsection

{{-- page styles --}}
@section('page-style')

    <link rel="stylesheet" href="{{ asset('assets/css/member_view.css') }}" />
    <style>
        video.video_size {
            width: 100%;
            height: 300px;
        }
    </style>
@endsection

@section('content')

    <h5 class="py-3 breadcrumb-wrapper mb-2">
        <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a>
            / {{ trans('pages.attribute_list.Members') }}
            / </span>View
    </h5>
    <section>
        <div class="row gy-4">
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class=" d-flex align-items-center flex-column">
                                <div class="image-fixed flex-shrink-0 center-cropped">
                                    <input type="hidden"
                                        value="{{ isset($member_info->member_id) ? $member_info->member_id : '' }}"
                                        id="member_id">
                                    <img src="{{ isset($member_info->profile_pic) ? config('custom.image_base_url') . $member_info->profile_pic : asset('/no_image.jpg') }}"alt=""
                                        onerror="this.src='{{ asset('/no_image.jpg') }}'"
                                        class="img-fluid rounded-circle my-4" height="120" width="150">
                                </div>
                                <div class="user-info text-center">
                                    <h5 class="mb-2">
                                        {{ isset($member_info->member_name) ? $member_info->member_name : '-' }} </h5>

                                    <p> {{ isset($member_info->email) ? $member_info->email : '-' }}</p>
                                    <p class="text-danger">
                                        {{ isset($member_info->member_type) ? $member_info->member_type : '-' }}
                                        @if (isset($member_info->member_type) && $member_info->member_type == config('custom.subscription.name.standard'))
                                            <span class="badge badge-center rounded-pill bg-dark">S</span>
                                        @endif
                                    </p>

                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="row justify-content-center">
                                <div class="col-md-3 border m-2">
                                    <div class="d-flex justify-content-center mt-3 gap-3">
                                        {{-- <span class="badge bg-label-primary p-2 rounded"><i class="menu-icon  fa-regular fa-file-video fa-fw fs-3 pt-2"></i></span> --}}
                                        <div>
                                            <h5 class="m-0 text-center">
                                                {{ isset($member_info->total_posts) ? $member_info->total_posts : '-' }}
                                            </h5>
                                            <p class="mt-1">Posts</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 border m-2">
                                    <div class="d-flex justify-content-center mt-3 gap-3">
                                        {{-- <span class="badge bg-label-primary p-2 rounded"><i class="fa fa-users fs-4 pt-2"></i></span> --}}
                                        <div>
                                            <h5 class="m-0 text-center">
                                                {{ isset($member_info->total_followers) ? $member_info->total_followers : '0' }}
                                            </h5>
                                            <p class="mt-1">Followers</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 border m-2">
                                    <div class="d-flex justify-content-center mt-3 gap-3">
                                        {{-- <span class="badge bg-label-primary p-2 rounded"><i class="fa-solid fa-user-plus fs-4 pt-2"></i></span> --}}
                                        <div>
                                            <h5 class="m-0 text-center">
                                                {{ isset($member_info->total_followings) ? $member_info->total_followings : '-' }}
                                            </h5>
                                            <p class="mt-1">Followings</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Joined On:</span>
                                    <span>
                                        {{ isset($member_info) ? date('m-d-Y', strtotime($member_info->created_at)) : '-' }}
                                    </span>
                                </li>
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Deleted On:</span>
                                    <span>
                                        {{ isset($member_info->deleted_at) ? date('m-d-Y', strtotime($member_info->deleted_at)) : '-' }}
                                    </span>
                                </li>
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Level:</span>
                                    <span>
                                        {{ isset($member_info->pickleball_level_term) ? $member_info->pickleball_level_term : '-' }}
                                    </span>
                                </li>
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Dob:</span>
                                    <span>

                                        {{ isset($member_info->dob) ? date('m-d-Y', strtotime($member_info->dob)) : '-' }}
                                    </span>
                                </li>
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Phone:</span>
                                    <span>
                                        {{ isset($member_info->phone) ? '+' : '' }}{{ isset($member_info->country_code) && isset($member_info->phone) ? $member_info->country_code . ' ' : '' }}{{ isset($member_info->phone) ? $member_info->phone : '-' }}
                                    </span>
                                </li>
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Gender:</span>
                                    <span>
                                        {{ isset($member_info->gender_type_term) ? $member_info->gender_type_term : '-' }}
                                    </span>
                                </li>
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Age:</span>
                                    <span>
                                        {{ isset($member_info->age_group_term) ? $member_info->age_group_term : '-' }}
                                    </span>
                                </li>
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Level Of Sportsmenship:</span>
                                    <span>
                                        {{ isset($member_info->level_of_sportsmenship_term) ? $member_info->level_of_sportsmenship_term : '-' }}
                                    </span>
                                </li>
                                <li class="mb-3  mt-3">
                                    <span class="fw-bold me-2">Total Matches:</span>
                                    <span>
                                        {{ isset($member_info->total_matches_in_6months) ? $member_info->total_matches_in_6months : '-' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <ul class="nav nav-pills mb-3" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-orders">Orders</button>
                    </li>
                </ul>
                <div class="tab-content p-0 shadow-none">
                    {{-- Orders --}}
                    <div class="tab-pane fade border-top border-dark active show" id="navs-pills-orders" role="tabpanel">
                        <div class="card mt-4 p-3 shadow-none">
                            <div class="table-responsive">
                                <table class="table w-100" id="order_list_tbl">
                                    <thead>
                                        <tr>
                                            <th style="display:none;"></th>
                                            <th>#</th>
                                            <th>Club/Trainer</th>
                                            <th>Slot Date</th>
                                            <th>Total ($)</th>
                                            <th>Order Status</th>
                                            <th>Payment By</th>
                                            <th>Payment Status</th>
                                            <th>Refund Status</th>
                                            <th>Order On</th>
                                            <th>Cancelled On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="cursor-pointer"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal_likes_users" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_list_title"></h4>
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-12 liked_users_block">
                        <div class="row">
                            <div class="col-7 liked_post_details">

                            </div>
                            <div class="col-5 liked_member_list_block">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
{{-- vendor scripts --}}
@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
    {{-- @include('scripts.members.view_js') --}}
@endsection
