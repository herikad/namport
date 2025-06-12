<script type="text/javascript">
    window.onload = function() {

        'use strict';
        var ClientContactForm = window.ClientContactForm || {};
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
                ClientContactForm.processExceptions(e);
            }
        };

        ClientContactForm.initEvents = function() {

            CKEDITOR.replace('role', {
                height: 200,
            });

            CKEDITOR.replace('responsibilities', {
                height: 200,
            });

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
            });

            $('#saveContactBtn').on('click', function(e) {

                submitForm('#contact_formid', '', '', (response) => {

                    console.log("response");
                    console.log(response);
                    console.log(response.status);

                    hideLoadingDialog();
                    if (response.status == 1) {
                        showSuccessMessage(response.message);
                        window.location.href = response.redirect_url;

                    } else {
                        hideLoadingDialog();
                        showErrorMessage(response.message);
                    }

                }, (error) => {
                    // ajax error callback
                    hideLoadingDialog();
                    showErrorMessage(error);
                });
            });

        };

        ClientContactForm.processExceptions = function(e) {
            showErrorMessage(e);
        };
        ClientContactForm.initEvents();

    };
</script>
