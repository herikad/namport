<script type="text/javascript">
    window.onload = function() {

        'use strict';
        var MailTempForm = window.MailTempForm || {};

        MailTempForm.regexTagMatch = /\[(.*?)\]/g
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
                MailTempForm.processExceptions(e);
            }
        };

        MailTempForm.initEvents = function() {

            CKEDITOR.replace('mail-body-textarea', {
                allowedContent:true,
            });

            let tagsArray = CKEDITOR.instances['mail-body-textarea'].getData().match(MailTempForm
                    .regexTagMatch);

                $('#body-tags').html('');
                if (tagsArray) {
                    $.each(tagsArray, function(index, tag) {

                        $('#body-tags').append(`<span class="h6 mb-0 mr-1">${tag}</span>`)

                    })
                }


            $('body').on('click', '#saveMailTempSettingBtn', function(e) {

                $('#tag_name').val(JSON.stringify(CKEDITOR.instances['mail-body-textarea'].getData().match(MailTempForm.regexTagMatch)))

                $('#mail_template_form_id').validate({
                    errorPlacement: function(error, element) {

                        error.insertAfter(element);
                    },
                    rules: {
                        title: {
                            required: true,
                            maxlength: 50,
                        },
                        body: {
                            required: true
                        },
                        mail_type:{
                            required: true,
                            maxlength: 50,
                        },
                        email_setting_id : {
                            required: true
                        }
                    },
                    messages: {
                        title: {
                            maxlength: "The title must be less than or equal to 20 characters.",
                            required: "This feild is required.",
                        },
                        body: {
                            required: "This feild is required.",
                        },
                        email_setting_id : {
                            required: "This feild is required.",
                        },
                        mail_type : {
                            maxlength: "The title must be less than or equal to 20 characters.",
                            required: "This feild is required.",
                        }
                    }
                });
            });

            CKEDITOR.instances['mail-body-textarea'].on('change', function(e) {

                let tagsArray = CKEDITOR.instances['mail-body-textarea'].getData().match(MailTempForm
                    .regexTagMatch);

                $('#body-tags').html('');
                if (tagsArray) {
                    $.each(tagsArray, function(index, tag) {

                        $('#body-tags').append(`<span class="h6 mb-0 mr-1">${tag}</span>`)

                    })
                }

            })

            
        };

        $('.email_setting_id_select').select2();

        MailTempForm.processExceptions = function(e) {
            showErrorMessage(e);
        };
        MailTempForm.initEvents();

    };
</script>
