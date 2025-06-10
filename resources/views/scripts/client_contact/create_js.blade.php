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

            $("#contact_formid").validate({
                ignore: 'input[type=hidden]', // ignore hidden fields
                errorClass: 'danger',
                successClass: 'success',
                highlight: function(element, errorClass) {
                    $(element).removeClass(errorClass);
                },
                unhighlight: function(element, errorClass) {
                    $(element).removeClass(errorClass);
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                rules: {
                    first_name: {
                        required: true
                    },
                    last_name: {
                        required: true
                    },
                    email: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    showLoadingDialog();
                    form.submit();
                }
            });
        };

        ClientContactForm.processExceptions = function(e) {
            showErrorMessage(e);
        };
        ClientContactForm.initEvents();

    };
</script>
