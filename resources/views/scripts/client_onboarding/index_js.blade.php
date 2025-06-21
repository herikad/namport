<script type="text/javascript">
    window.onload = function() {

        'use strict';
        var OnboardingForm = window.OnboardingForm || {};

        OnboardingForm.regexTagMatch = /\[(.*?)\]/g
        var xhr = null;
        if (window.XMLHttpRequest) {
            xhr = window.XMLHttpRequest;
        } else if (window.ActiveXObject('Microsoft.XMLHTTP')) {

            xhr = window.ActiveXObject('Microsoft.XMLHTTP');
        }
        var send = xhr.prototype.send;
        xhr.prototype.send = function(data) {
            try {
                send.call(this, data);
            } catch (e) {
                OnboardingForm.processExceptions(e);
            }
        };

        OnboardingForm.initEvents = function() {

            $('.flatpickr').flatpickr();
            // step wise code start
            let steps = document.querySelectorAll('.step-card');
            let navLinks = document.querySelectorAll('.step-indicator .nav-link');
            let currentStep = 0;

            function showStep(index) {
                steps.forEach((s, i) => s.classList.toggle('active', i === index));
                navLinks.forEach((l, i) => l.classList.toggle('active', i === index));
                currentStep = index;
            }

            window.nextStep = function() {
                if (currentStep < steps.length - 1) showStep(currentStep + 1);
            };

            window.prevStep = function() {
                if (currentStep > 0) showStep(currentStep - 1);
            };

            navLinks.forEach((btn, i) => {
                btn.addEventListener('click', () => showStep(i));
            });


            document.getElementById('profileInput').addEventListener('change', function(event) {
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
            });
            // step wise code end

            /// audio profile code start


            let recorder, audioBlob, interval;
            let seconds = 0;
            const maxSeconds = 30;

            const startBtn = document.getElementById('startRecording');
            const stopBtn = document.getElementById('stopRecording');
            const playBtn = document.getElementById('playAudio');
            const reRecordBtn = document.getElementById('reRecord');
            const confirmBtn = document.getElementById('confirmRecording');
            const timeText = document.getElementById('recordingTime');
            const audioPreview = document.getElementById('audioPreview');
            const hiddenInput = document.getElementById('voice_profile_data');

            startBtn.addEventListener('click', async () => {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: true
                    });
                    // recorder = RecordRTC(stream, { type: 'audio' });
                    recorder = RecordRTC(stream, {
                        type: 'audio',
                        mimeType: 'audio/webm'
                    });

                    recorder.startRecording();

                    startBtn.disabled = true;
                    stopBtn.disabled = false;
                    playBtn.disabled = true;
                    confirmBtn.disabled = true;
                    audioPreview.hidden = true;

                    seconds = 0;
                    timeText.textContent = `${seconds}s`;

                    interval = setInterval(() => {
                        seconds++;
                        timeText.textContent = `${seconds}s`;
                        if (seconds >= maxSeconds) stopRecording();
                    }, 1000);
                } catch (err) {
                    alert("Microphone access is required.");
                    console.error(err);
                }
            });

            stopBtn.addEventListener('click', stopRecording);
            function stopRecording() {
                
                clearInterval(interval);
                recorder.stopRecording(() => {
                    audioBlob = recorder.getBlob();
                        console.log("Recorded blob size (bytes):", audioBlob.size); // <== ADD THIS LINE

                    const audioURL = URL.createObjectURL(audioBlob);
                        console.log("audioURL:", audioURL); // <== ADD THIS LINE

                    audioPreview.src = audioURL;
                    audioPreview.hidden = false;

                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                    playBtn.disabled = false;
                    confirmBtn.disabled = false;

                    const reader = new FileReader();
                    reader.onload = function() {
                        hiddenInput.value = reader.result;
                    };
                    reader.readAsDataURL(audioBlob);
                });
            }

            playBtn.addEventListener('click', () => {
                if (audioPreview.src) {
                    audioPreview.play().catch((err) => {
                        console.error("Playback failed:", err);
                        alert("Audio playback failed.");
                    });
                }
            });

            reRecordBtn.addEventListener('click', () => {
                clearInterval(interval);
                seconds = 0;
                timeText.textContent = '0s';
                audioPreview.src = '';
                audioPreview.hidden = true;

                startBtn.disabled = false;
                stopBtn.disabled = true;
                playBtn.disabled = true;
                confirmBtn.disabled = true;
            });

            confirmBtn.addEventListener('click', () => {
                bootstrap.Modal.getInstance(document.getElementById('voiceProfileModal')).hide();
            });

            $('body').on('click', '#reRecordBtn', function() {
                const modalEl = document.getElementById('voiceProfileModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            });
            document.getElementById('voiceProfileModal').addEventListener('hidden.bs.modal', function() {

                // Clean stuck backdrop if any
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                // Remove modal-open class from body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';

                // Optional: Reset recording fields if needed
                const audioPreview = document.getElementById('audioPreview');
                if (audioPreview) audioPreview.src = '';
            });
            // audio profile code end

            // store data code start
            function validateVoiceProfile() {
                var existingAudio = `{{ $contact_details->voice_profile ?? '' }}`.trim(); 
                var newAudio = $('#voice_profile_data').val().trim();

                console.log(existingAudio);
                console.log(newAudio);
                
                // Clear any old error style
                $('#voice_profile_data').removeClass('is-invalid');

                if (!existingAudio && !newAudio) {
                    $('button[data-bs-target="#voiceProfileTab"]').tab('show');

                    $('#voice_profile_data').addClass('is-invalid');

                    showErrorMessage('Please create a voice profile.');

                    return false; 
                }

                return true;
            }


            $('#submitOnboardingForm').on('click', function(e) {

                if (!validateVoiceProfile()) return false;

                submitForm('#onboardingForm', '', '', (response) => {

                    hideLoadingDialog();
                    if(response.status == 1){
                        showSuccessMessage(response.message);
                        window.location.href = response.redirect_url;

                    }else{
                        hideLoadingDialog();
                        showErrorMessage(response.message);
                    }

                }, (error) => {
                        // ajax error callback
                        hideLoadingDialog();
                        showErrorMessage(error);
                });
            });

            // store data code end

        };

        OnboardingForm.processExceptions = function(e) {
            showErrorMessage(e);
        };
        OnboardingForm.initEvents();

    };
</script>
