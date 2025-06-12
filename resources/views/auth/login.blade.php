@php
    $configData = Helper::appClasses();
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.blankLayout')

@section('title', 'Login')

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <style>
        .authentication-wrapper.authentication-basic .authentication-inner {
            max-width: 35rem !important;
        }

        .banner-img {
            /* background: url({{ asset('assets/img/project/login.png') }}); */
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
@endsection

@section('content')

    <div class="banner-img">

        <div class="container-lg">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner py-5">

                    <div class="card">
                        <div class="card-body p-5">

                            <div class="row">
                                <div class="">
                                    <div class="text-center">
                                        <img class="logo" style="width: 50%;" src="{{ asset('logo.png') }}">
                                        <h4 class="text-center mb-4 mt-4">Login to Your Account</h4>
                                    </div>

                                    <form id="formAuthentication" class="mb-3" action="{{ route('basic_login') }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="claim_club_id" value="{{ @$claim_club_id }}">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email or Username</label>
                                            <input type="text" class="form-control" id="email" name="user_name"
                                                placeholder="Enter your email or username" autofocus>
                                        </div>
                                        <div class="mb-3 form-password-toggle">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="password">Password</label>
                                            </div>
                                            <div class="input-group input-group-merge">
                                                <input type="password" id="password" class="form-control" name="password"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    aria-describedby="password" />
                                            </div>
                                            <div class="text-end">
                                                <a href="{{ route('forgot_password') }}">
                                                    <p class="theme-text-primary">Forgot Password?</p>
                                                </a>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary d-grid w-100 theme-btn-primary">
                                            Log in
                                        </button>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
