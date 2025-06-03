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
                                    <div class="text-center mb-4">
                                        <img class="logo" style="width: 50%;" src="{{ asset('logo.png') }}">
                                        <h4 class="text-center mt-4 mb-2">Forgot Password?</h4>
                                        <p>No worries, we'll send you reset instructions.</p>
                                    </div>

                                    <form id="forgot_pwd_form" class="mb-3" action="{{ route('check_email') }}"
                                        method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="email" name="user_name"
                                                placeholder="Enter your email" autofocus required>
                                        </div>



                                        <button type="submit"
                                            class="submit_mail btn btn-primary glow position-relative w-100">
                                            Send Password <i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
                                        </button>
                                        <div class="text-center mt-3">

                                            <a href="{{ route('login') }}">
                                                <p class="theme-text-primary"><i id="icon-arrow"
                                                        class="bx bx-left-arrow-alt"></i>&nbsp; Back to Login</p>
                                            </a>

                                        </div>
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
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {

        // Custom validation method for email format
        $.validator.addMethod('customEmail', function(value, element) {

            // Regular expression to validate email format
            var emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

            // Check if the value matches the email format
            return this.optional(element) || emailRegex.test(value);

        }, 'Please enter a valid email address.');

        $('body').on('click', '.submit_mail', function(e) {

            $('#forgot_pwd_form').validate({

                errorPlacement: function(error, element) {

                    error.insertAfter(element);
                },
                rules: {
                    user_name: {
                        required: true,
                        customEmail: true,
                        maxlength: 60
                    }
                },
                messages: {
                    user_name: {
                        required: 'Please enter email',
                        customEmail: 'Please enter a valid email address',
                        maxlength: 'Email cannot exceed 60 characters'
                    }
                }
            });
        });

    });
</script>
