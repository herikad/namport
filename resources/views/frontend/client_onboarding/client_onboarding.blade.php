@php
    $configData = Helper::appClasses();
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts.blankLayout')

@section('title', 'Client Onboarding')

@section('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/pickers/pickadate/pickadate.css') }}">

@endsection

@section('page-style')

    <style>
        .step-card {
            display: none;
        }

        .step-card.active {
            display: block;
        }

        .step-indicator .nav-link.active {
            background-color: #0d6efd;
            color: white !important;
        }

        .profile-pic-preview {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            margin-top: 10px;
        }

        .clientboad button.nav-link {
            border-bottom: 4px solid #e5e9ed !important;
            padding: 0 0 10px 0 !important;
            border-radius: 0 !important;
            min-width: 150px;
            font-size: 14px !important;
            color: #666 !important;
            font-weight: 500;
        }

        .clientboad ul {
            grid-gap: 20px !important;
        }

        .clientboad .nav-link.active {
            background-color: transparent !important;
            color: #ef333f !important;
            box-shadow: none;
            border-color: #ef333f !important;
        }

        .titlenair {
            font-size: 20px;
            text-align: center;
            border-bottom: 3px solid #d4d8dd;
            padding-bottom: 15px;
        }

        .btn-primary {
            background: linear-gradient(128deg, rgba(196, 4, 134, 1) 0%, rgba(237, 84, 76, 1) 50%, rgba(65, 17, 88, 1) 100%) !important;
            border: none !important;
        }

        .btn-primary:hover {
            border-color: transparent !important;

        }

        .btn-secondary {
            color: #fff;
            background-color: #111;
            border-color: #111;
        }

        .btn-secondary:hover {
            color: #fff;
            background-color: #111 !important;
            border-color: #111 !important;
        }

        label.form-label {
            font-size: 14px;
            color: #666 !important;
            font-weight: 500;
            text-transform: capitalize;
        }
    </style>

@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

@endsection


@section('content')



    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light"
        style="background:url(https://supermia.ai/wp-content/uploads/2025/02/homepagebanner.png) no-repeat center center">
        <div class="bg-white p-4 rounded shadow clientboad" style="width: 100%; max-width: 950px; min-width: 750px;">
            <h3 class="mb-4 titlenair">Client Contact Onboarding</h3>
            <ul class="nav nav-pills mb-4 step-indicator">
                <li class="nav-item"><button class="nav-link active" type="button">Basic Info</button></li>
                <li class="nav-item"><button class="nav-link" type="button"> Profile Info</button></li>
                <li class="nav-item"><button class="nav-link" type="button"> Voice Profile & Role</button></li>
            </ul>
            <form id="onboardingForm" method="POST" action="{{ route('store-client-onboarding-page') }}"
                enctype="multipart/form-data" novalidate>
                @csrf
                <!-- Step 1 -->
                <input type="hidden" name="user_id" value="{{ $user_details->user_id }}">
                <div class="step-card active">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="{{ $contact_details->first_name }}"
                                name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="{{ $contact_details->last_name }}"
                                name="last_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mobile No</label>
                            <input type="text" class="form-control" value="{{ $contact_details->mobile_no }}"
                                name="mobile_no" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $contact_details->email }}" name="email"
                                required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender_type_term" class="select2 form-control" id="gender_type_term"
                                data-element-ref="select2">
                                <option value="">Select Gender</option>
                                @if (count($gender_term))
                                    @foreach ($gender_term as $key => $term)
                                        <option value="{{ $term->value }}"
                                            {{ isset($contact_details) && $contact_details->gender_term == $term->value ? 'selected' : '' }}>
                                            {{ $term->label }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reporting To</label>
                            <select name="reporting_to" class="select2 form-control" id="reporting_to"
                                data-element-ref="select2">
                                <option value="">Select Reporting To</option>
                                @if (count($reporting_to))
                                    @foreach ($reporting_to as $key => $reproting)
                                        <option value="{{ $reproting->client_contacts_id }}"
                                            {{ isset($contact_details) && $contact_details->reporting_to == $reproting->client_contacts_id ? 'selected' : '' }}>
                                            {{ $reproting->display_name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Profile Status</label>
                            <select name="profile_status_term" class="select2 form-control" id="profile_status_term"
                                data-element-ref="select2">
                                @if (count($profile_link_term))
                                    @foreach ($profile_link_term as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ isset($contact_details) && $contact_details->profile_status_term == $key ? 'selected' : '' }}>
                                            {{ $value }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                    </div>
                </div>
                <!-- Step 2 -->
                <div class="step-card">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ID No</label>
                            <input type="text" class="form-control" value="{{ $contact_details->id_no }}" readonly
                                name="id_no">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Joining</label>
                            <input type="date" class="form-control flatpickr"
                                value="{{ \Carbon\Carbon::parse($contact_details->date_of_joining)->format('Y-m-d') }}"
                                name="date_of_joining">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department" class="select2 form-control" id="department"
                                data-element-ref="select2">
                                <option value="">Select Department</option>
                                @if (count($departments))
                                    @foreach ($departments as $key => $dept)
                                        <option value="{{ $key }}"
                                            {{ isset($contact_details) && $contact_details->department == $key ? 'selected' : '' }}>
                                            {{ $dept }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <select name="designation" class="select2 form-control" id="designation"
                                data-element-ref="select2">
                                <option value="">Select Designation</option>
                                @if (count($designations))
                                    @foreach ($designations as $key => $designation)
                                        <option value="{{ $key }}"
                                            {{ isset($contact_details) && $contact_details->designation == $key ? 'selected' : '' }}>
                                            {{ $designation }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>

                        <!-- File input -->
                        <input type="file" class="form-control" name="profile_pic" accept="image/*"
                            onchange="previewImage(event)">

                        <!-- Image preview (existing or selected) -->
                        @if ($contact_details->profile_pic)
                            <img id="profilePicPreview"
                                src="{{ !empty($contact_details->profile_pic) ? env('AWS_URL') . 'images/client_contact/profile/' . $contact_details->profile_pic : url('/') . '/no_image.jpg' }}"
                                class="mt-2 rounded" style="width: 150px; height: 150px; object-fit: cover;"
                                onerror="this.src = '{{ url('/') . '/no_image.jpg' }}';">
                        @endif
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()">Back</button>
                        <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                    </div>
                </div>
                <!-- Step 3 -->
                <div class="step-card">
                    <div class="mb-3">
                        <label class="form-label">Voice Profile</label><br>
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#voiceProfileModal">
                            Create Voice Profile
                        </button>
                        <input type="hidden" name="voice_profile" id="voice_profile_data" required>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <textarea id="role" class="form-control" data-element-ref="ckeditor" name="role" rows="6">{{ $contact_details->role }}</textarea>
                                <div class="ck_editor_validate_msg"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="responsibilities" class="form-label">Responsibilities</label>
                                <textarea id="responsibilities" class="form-control" data-element-ref="ckeditor" name="responsibilities" rows="6">{{ $contact_details->responsibilities }}</textarea>
                                <div class="ck_editor_validate_msg"></div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" onclick="prevStep()">Back</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Voice Profile Modal -->
    <div class="modal fade" id="voiceProfileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Voice Profile Setup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Note:</strong> Please say the following:</p>
                    <blockquote class="blockquote">"My name is [Your Name], and this is my voice profile for verification."
                    </blockquote>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-primary" id="startRecord">Start Recording</button>
                        <button type="button" class="btn btn-secondary" id="playAudio" disabled>Hear</button>
                        <button type="button" class="btn btn-warning" id="reRecord" disabled>Re-record</button>
                        <button type="button" class="btn btn-success" id="confirmRecording" disabled>Confirm</button>
                    </div>
                    <audio id="audioPlayback" class="mt-3 w-100" controls style="display:none;"></audio>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('vendor-scripts')
    <script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/daterange/daterangepicker.js') }}"></script>
@endsection

@section('page-script')
    <!-- <script src="{{ asset('assets/js/jquery.min.js') }}"></script> -->
    <script src="{{ asset('extensions/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
    <script>
        let steps = document.querySelectorAll('.step-card');
        let navLinks = document.querySelectorAll('.step-indicator .nav-link');
        let currentStep = 0;

        function showStep(index) {
            steps.forEach((s, i) => s.classList.toggle('active', i === index));
            navLinks.forEach((l, i) => l.classList.toggle('active', i === index));
            currentStep = index;
        }

        function nextStep() {
            if (currentStep < steps.length - 1) showStep(currentStep + 1);
        }

        function prevStep() {
            if (currentStep > 0) showStep(currentStep - 1);
        }
        navLinks.forEach((btn, i) => btn.addEventListener('click', () => showStep(i)));

        function previewImage(event) {
            const input = event.target;
            const reader = new FileReader();

            reader.onload = function() {
                const img = document.getElementById('profilePicPreview');
                img.src = reader.result;
                img.style.display = 'block';
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script>
        let mediaRecorder, audioChunks = [];
        const startBtn = document.getElementById("startRecord");
        const playBtn = document.getElementById("playAudio");
        const reRecordBtn = document.getElementById("reRecord");
        const confirmBtn = document.getElementById("confirmRecording");
        const audioTag = document.getElementById("audioPlayback");
        const voiceProfileField = document.getElementById("voice_profile_data");

        startBtn.addEventListener("click", async () => {
            audioChunks = [];
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, {
                        type: 'audio/webm'
                    });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    audioTag.src = audioUrl;
                    audioTag.style.display = "block";
                    playBtn.disabled = reRecordBtn.disabled = confirmBtn.disabled = false;
                    const reader = new FileReader();
                    reader.readAsDataURL(audioBlob);
                    reader.onloadend = () => voiceProfileField.value = reader.result;
                };
                mediaRecorder.start();
                startBtn.textContent = "Recording...";
                startBtn.disabled = true;
                setTimeout(() => {
                    mediaRecorder.stop();
                    startBtn.textContent = "Start Recording";
                    startBtn.disabled = false;
                }, 10000);
            } catch (err) {
                alert("Microphone access denied or not available.");
            }
        });
        playBtn.addEventListener("click", () => audioTag.play());
        reRecordBtn.addEventListener("click", () => {
            playBtn.disabled = reRecordBtn.disabled = confirmBtn.disabled = true;
            audioTag.style.display = "none";
            audioTag.pause();
            audioTag.src = "";
            voiceProfileField.value = "";
        });
        confirmBtn.addEventListener("click", () => {
            if (!voiceProfileField.value) {
                alert("Please record your voice profile before continuing.");
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('voiceProfileModal')).hide();
        });
        $('.flatpickr').flatpickr();
    </script>
    <script>
        CKEDITOR.replaceAll(function(textarea, config) {
            config.height = 200;
            return true; // return true to replace this textarea
        });
    </script>
@endsection
