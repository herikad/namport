@extends('layouts.layoutMaster')

{{-- Page title --}}
@section('title', 'Client Contact')

{{-- Vendor styles (if any specific vendor CSS is needed, include it here) --}}
@section('vendor-styles')
    {{-- Example: <link rel="stylesheet" href="{{ asset('vendor/libs/some-library/some-library.css') }}" /> --}}
@endsection

{{-- Page styles (custom CSS for this specific page) --}}
@section('page-styles')
    <style>
        /* Profile Picture Preview Styling */
        .image-fixed {
            width: 120px;
            /* Fixed width for the circle */
            height: 120px;
            /* Fixed height for the circle */
            border-radius: 50%;
            /* Makes it a circle */
            overflow: hidden;
            /* Hides image overflow outside the circle */
            border: 2px solid #e0e0e0;
            /* Subtle border */
            background-color: #f8f8f8;
            /* Fallback background color */
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            /* Center the image container if not using flex on parent */
        }

        .image-fixed img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Ensures image covers the area without distortion */
            display: block;
        }

        /* Required field asterisk styling */
        .form-label.required::after {
            content: " *";
            color: red;
            margin-left: 2px;
        }

        /* Button glow (assuming 'glow' is a custom class from your theme) */
        .btn.glow {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
            /* Example glow effect */
        }

        /* Adjust profile picture column order on small screens */
        @media (max-width: 767.98px) {
            .profile-pic-col {
                order: -1;
                /* Puts profile pic column at the top on small screens */
                text-align: center;
                /* Center content horizontally */
                margin-bottom: 1.5rem;
                /* Add some space below on small screens */
            }

            .profile-pic-col .card-body {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            /* Override specific image-fixed dimensions for mobile if needed, or adjust above */
            .image-fixed {
                width: 100px;
                height: 100px;
            }
        }
    </style>
@endsection

{{-- Page content --}}
@section('content')
    <section>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title text-primary">Client Contact</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('client_contacts.store') }}" class="mail_form" id="contact_formid" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            {{-- Profile Picture Column --}}
                            <div class="col-12 col-md-4 mb-3 profile-pic-col">
                                <div class="card border h-100">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                        {{-- This div is now explicitly styled to control the image size --}}
                                        <div id="profile_pic_1_preview" class="image-fixed mb-3"
                                            style="width: 120px; height: 120px; overflow: hidden; border: 2px solid #e0e0e0; background-color: #f8f8f8;">
                                            <img src="" alt="Profile Picture" id="img_preview"
                                                style="object-fit: cover; width: 100%; height: 100%; display: block;"
                                                onerror="this.src = '{{ url('/') . '/no_image.jpg' }}';">
                                        </div>
                                        <div class="form-group add-new-file text-center">
                                            <label for="profile_pic_1" class="form-label d-block mb-1">Profile
                                                Picture</label>
                                            <label for="profile_pic_1"
                                                class="btn btn-primary btn-sm glow add-file-btn text-capitalize">
                                                Select Photo
                                            </label>
                                            <input type="file" name="profile_pic" data-file="doctor_image_round"
                                                class="d-none" id="profile_pic_1" accept="image/png, image/jpeg, image/gif">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Name & Email/Mobile Columns --}}
                            <div class="col-12 col-md-8">
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group"> {{-- Removed 'controls' class as it's not standard Bootstrap --}}
                                            <label class="form-label required" for="first_name">First Name</label>
                                            <input type="text" name="first_name" id="first_name"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                value="{{ old('first_name') }}" maxlength="30" required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label required" for="last_name">Last Name</label>
                                            <input type="text" name="last_name" id="last_name"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                value="{{ old('last_name') }}" maxlength="30" required>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label required" for="email">Email</label>
                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" maxlength="50" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label" for="mobile_no">Mobile No.</label>
                                            <input type="text" name="mobile_no" id="mobile_no"
                                                class="form-control @error('mobile_no') is-invalid @enderror"
                                                value="{{ old('mobile_no') }}">
                                            @error('mobile_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label required" for="id_no">ID No.</label>
                                            <input type="text" name="id_no" id="id_no"
                                                class="form-control @error('id_no') is-invalid @enderror"
                                                value="{{ old('id_no') }}" maxlength="20" required>
                                            @error('id_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit/Cancel Buttons --}}
                        <div class="row mt-3">
                            <div class="col-12 text-end">
                                <button type="submit" id="saveContactBtn" class="btn btn-success me-2">Save</button>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

{{-- Vendor scripts (if any specific vendor JS is needed, include it here) --}}
@section('vendor-scripts')
    {{-- Example: <script src="{{ asset('vendor/libs/some-library/some-library.js') }}"></script> --}}
@endsection

{{-- Page scripts (custom JS for this specific page) --}}
@section('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profilePicInput = document.getElementById('profile_pic_1');
            const imgPreview = document.getElementById('img_preview');

            profilePicInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Fallback to no_image.jpg if no file is selected (e.g., user cancels selection)
                    // Ensure this path is correct for your application's public directory
                    imgPreview.src = '{{ url('/') . '/no_image.jpg' }}';
                }
            });

            // Optional: Set initial image if 'old' value or a model property exists (for edit view)
            // This part would typically be added if you're editing an existing contact
            // Example (assuming you pass a $contact object from your controller):
            // @if (isset($contact) && $contact->profile_pic)
            //     imgPreview.src = '{{ asset('path/to/profile_pics/' . $contact->profile_pic) }}';
            // @else
            //     imgPreview.src = '{{ url('/') . '/no_image.jpg' }}';
            // @endif
        });
    </script>
    {{-- If you have other JS includes, place them here --}}
    @include('scripts.client_contact.create_js')
@endsection
