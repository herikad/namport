@extends('layouts.layoutMaster')

{{-- title --}}
@section('title', 'My Profile')

{{-- vendor styles --}}
@section('vendor-styles')
@endsection

{{-- page styles --}}
@section('page-styles')
@endsection

@section('content')
    <section>
        <h5 class="py-3 breadcrumb-wrapper mb-2">
            <span class="text-muted fw-light"><a href="/"><i class="bx bx-home-alt"></i></a> / </span> Profile
        </h5>
        <div class="card">

            <div class="card-header pt-75 pb-75">
                <h4 class="card-title">Update Profile Details</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('profile.update') }}" id="update_profile_details" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="col-md-12 col-12">
                                <div class="card border shadow-none app-file-info">
                                    <div class="card-body text-center ps-0 pe-0">
                                        <div id="profile_pic_1_preview" class="image-fixed flex-shrink-0">
                                            <img src=" {{ isset($userinfo->profile_pic) ? config('custom.image_base_url') . $userinfo->profile_pic : asset('/no_image.jpg') }} "
                                                alt="" class="object-fit-md-contain rounded-2" height="150"
                                                width="150" style="object-fit: cover;" onerror="this.src='{{ asset('/no_image.jpg') }}'">
                                        </div>
                                    </div>
                                    <div class="card-footer pt-0">
                                        <div class="form-group add-new-file text-center">
                                            <label for="profile_pic_1" class="btn btn-sm btn-primary">Select</label>
                                            <input type="file" name="profile_pic" class="d-none" id="profile_pic_1"
                                                accept="image/x-png, image/jpeg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">

                            <div class="d-flex col-md-12 gap-2">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="d-block required">Name</label>
                                        <input type="text" class="form-control" id="display_name"
                                            value="{{ $userinfo->display_name }}" name="display_name"
                                            placeholder="Enter your name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="d-block required">Email</label>
                                        <input type="email" class="form-control" id="user_name"
                                            value="{{ $userinfo->user_name }}" name="user_name"
                                            placeholder="Enter your email" required readonly>
                                    </div>
                                </div>
                            </div>

                            @if ($user->user_type_term == config('custom.user_type_term.club_user'))
                                <div class="d-flex col-md-12 gap-2">
                                    <div class="col-md-6">
                                        <div class="controls mb-3">
                                            <label class="d-block">Gender</span></label>
                                            <select name="gender_type_term" class="select2 form-control"
                                                id="gender_type_term" data-element-ref="select2">
                                                <option value="">Select Gender</option>
                                                @if (count($gender_term))
                                                    @foreach ($gender_term as $key => $term)
                                                        <option value="{{ $term->value }}"
                                                            {{ isset($userinfo) && $userinfo->gender_type_term == $term->value ? 'selected' : '' }}>
                                                            {{ $term->label }} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 d-flex">
                                        <div class="col-md-4">
                                            <div class="controls mb-3">
                                                <label class="d-block required">Country
                                                    Code</span></label>
                                                <select name="country_code"
                                                    class="select2 form-control rounded-start rounded-0" id="country_code"
                                                    data-element-ref="select2" required>
                                                    <option value="" disabled>Select Country
                                                        Code
                                                    </option>

                                                    @if (count($countries))
                                                        @foreach ($countries as $key => $country)
                                                            <option value="{{ $country->phonecode }}"
                                                                {{ isset($userinfo) && $userinfo->country_code == $country->phonecode ? 'selected' : '' }}>
                                                                +{{ $country->phonecode }} -
                                                                {{ $country->sortname }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="controls mb-3">
                                                <label class="d-block required">Mobile</span></label>
                                                <input type="tel" maxlength="13" value="{{ $userinfo->phone }}"
                                                    name="phone" class="form-control border-start-0 rounded-end rounded-0"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endif

                            <div class="mb-0 text-end" style="padding-top: 50px;">
                                <button type="submit" class="btn btn-success" id="update_profile_btn">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </section>
@endsection

{{-- vendor scripts --}}
@section('vendor-scripts')
@endsection

{{-- page scripts --}}
@section('page-script')
    @include('scripts.dashboard.my_profile_js')
@endsection
