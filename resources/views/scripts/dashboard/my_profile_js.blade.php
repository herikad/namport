<script type="text/javascript">
    window.onload = function() {

        'use strict';
        var MyProfile = window.MyProfile || {};
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
                MyProfile.processExceptions(e);
            }
        };

        MyProfile.initEvents = function() {

            $('body').on('click', '#update_profile_btn', function(e) {
                
                // Custom validation method for email format
                $.validator.addMethod('customEmail', function(value, element) {

                // Regular expression to validate email format
                var emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

                // Check if the value matches the email format
                return this.optional(element) || emailRegex.test(value);

                }, 'Please enter a valid email address.');

                $('#update_profile_details').validate({

                    errorPlacement: function(error, element) {
                    
                        error.insertAfter(element);
                    },
                    rules: {
                        display_name: {
                                required: true,
                                maxlength: 20
                            },
                        user_name: {
                                required: true,
                                customEmail: true,
                                maxlength: 60
                            }
                    },
                    messages: {
                        display_name: {
                                required: 'Please enter Name',
                                maxlength: 'Name cannot exceed 20 characters'
                            },
                        user_name: {
                                required: 'Please enter email',
                                customEmail: 'Please enter a valid email address',
                                maxlength: 'Email cannot exceed 60 characters'
                            }
                        }
                });
            });

        };

        MyProfile.processExceptions = function(e) {
            showErrorMessage(e);
        };
        MyProfile.initEvents();

    };
</script>
