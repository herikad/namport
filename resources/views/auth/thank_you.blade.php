@php
    $configData = Helper::appClasses();
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.blankLayout')

@section('title', 'Thank You')



@section('page-style')
    <!-- Page -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <style>

        .authentication-wrapper.authentication-basic .authentication-inner{
            max-width:50rem !important;
        }
        .banner-img{
            background: url({{ asset('assets/img/project/bg_1.svg') }});
            background-repeat: no-repeat;
            background-size:cover;
        }
    </style>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection

@section('content')

    <div class="banner-img">

        <div class="container-lg">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner py-5">

                    <div class="row">
                        <div class="">
                            <div class="text-center">
                                <img class="logo" style="width: 50%;" src="{{ asset('logo.png')}}">
                                <h3 class="mt-5 mb-4"><b>Thank you for submitting! 🙌</b></h3>
                                <h6 class="text-center mb-5">Your password has been created successfully!🚀</h6>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
