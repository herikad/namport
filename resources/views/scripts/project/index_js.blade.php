<script>
    window.onload = function() {
        'use strict';

        var ProjectList = window.ProjectList || {};
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
                ProjectList.processExceptions(e);
            }
        };

        ProjectList.initEvents = function() {
            
            
            // Trigger file input when box clicked  
            $('#logoUploadBox').click(() => $('#logoInput').click());
            $('#bannerUploadBox').click(() => $('#bannerInput').click());

            // Common preview handler
            function readAndPreview(input, previewSelector, boxSelector) {
                const file = input.files?.[0];
                if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $(previewSelector).attr('src', e.target.result).show();
                    $(boxSelector).find('.upload-placeholder').hide();
                };
                reader.readAsDataURL(file);
                }
            }

            // Bind change events
            $('#logoInput').on('change', function () {
                readAndPreview(this, '#logoPreview', '#logoUploadBox');
            });

            $('#bannerInput').on('change', function () {
                readAndPreview(this, '#bannerPreview', '#bannerUploadBox');
            });        

            // ckeditor load
            CKEDITOR.replace('short_description', {
                height: 150,
            });

            // date picker
            $('.flatpickr').flatpickr();

            $('#open_project_model').on('click', function () { 
                $('#newProjectModal').modal('show'); 
            });

        }

       

        ProjectList.processExceptions = function(e) {
            showErrorMessage(e);
        };

        ProjectList.initEvents();
    };
</script>
