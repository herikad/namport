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
            background: url({{ asset('assets/img/project/login.png') }});
            background-repeat: no-repeat;
            background-size:cover;
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
                                    <div class="text-center mb-4">
                                        <img class="logo" style="width: 50%;" src="{{ asset('logo.png')}}">
                                        <h4 class="text-center mt-4 mb-2">Reset Your Password</h4>
                                    </div>

                                    @if ($user)
                                        <form action="{{ route('process_password_reset', $user->email_token) }}"
                                            method="POST" id="resetPassForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="email" class="form-label">New Password</label>
                                                <input type="password" class="form-control" placeholder="New Password"
                                                    name="new_password" required id="new_password"
                                                    data-validation-required-message="The password field is required"
                                                    minlength="6">

                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Confirm Password</label>
                                                <input type="password" class="form-control" placeholder="Confirm Password"
                                                    name="con_password" required data-validation-match-match="new_password"
                                                    data-validation-required-message="The Confirm password field is required"
                                                    minlength="6">
                                            </div>

                                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                                <button type="submit" id="submitPass"
                                                    class="btn btn-success me-sm-3 me-1">Save</button>
                                                <a href="{{ route('login') }}" class="btn btn-light">Cancel</a>
                                            </div>
                                        </form>
                                    @else
                                        <div class="card-body">
                                            <div class="text-bold-600 text-dark">Your page is expired. Please contact to
                                                your administrator.</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {

        $('body').on('click', '#submitPass', function(e) {
            $('#resetPassForm').validate({
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                rules: {
                    new_password: {
                        required: true,
                        maxlength: 10
                    },
                    con_password: {
                        required: true,
                        maxlength: 10,
                        equalTo: "#new_password"
                    }
                },
                messages: {
                    new_password: {
                        required: 'Please enter a new password',
                        maxlength: 'Password cannot exceed 10 characters'
                    },
                    con_password: {
                        required: 'Please confirm the password',
                        maxlength: 'Password cannot exceed 10 characters',
                        equalTo: 'Passwords do not match'
                    }
                }
            });
        });


    });
</script>
