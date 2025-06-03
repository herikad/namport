<script type="text/javascript">
    window.onload = function() {

        'use strict';
        var MailForm = window.MailForm || {};
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
                MailForm.processExceptions(e);
            }
        };

        MailForm.initEvents = function() {

            $('body').on('click', '#saveMailSettingBtn', function(e) {
               
                $('#mail_form_id').validate({   
                    errorPlacement: function(error, element) {
            
                        error.insertAfter(element);
                    },  
                    rules: {

                                email_id: {
                                    maxlength: 255,
                                    required: true
                                },
                                from_name: {
                                    maxlength: 55,
                                    required: true
                                },

                                email_protocol: {
                                    maxlength: 15,
                                    required: true
                                },

                                smtp_host: {
                                    maxlength: 55,
                                    required: true
                                },
                                port: {
                                    maxlength: 15,
                                    required: true
                                },
                                smtp_crypto: {
                                    maxlength: 15,
                                    required: true
                                },
                                password: {
                                    maxlength: 55,
                                    required: true
                                },

                            },
                    messages: {
                        email_id: {
                            maxlength: "The email_id must be less than or equal to 255",
                            required: "This field is required"
                        },
                        from_name: {
                            maxlength: "The from_name must be less than or equal to 55",
                            required: "This field is required"
                        },

                        email_protocol: {
                            maxlength: "The email_protocol must be less than or equal to 15",
                            required: "This field is required"
                        },
                        smtp_host: {
                            maxlength: "The smtp_host must be less than or equal to 55",
                            required: "This field is required"
                        },
                        
                        port: {
                            maxlength: "The smtp_port must be less than or equal to 15",
                            required: "This field is required"
                        },
                        smtp_crypto: {
                            maxlength: "The smtp_crypto must be less than or equal to 15",
                            required: "This field is required"
                        },
                        password: {
                            maxlength: "The password must be less than or equal to 55",
                        },
                    },
                });
            });

        };

        MailForm.processExceptions = function(e) {
            showErrorMessage(e);
        };
        MailForm.initEvents();

    };
</script>
