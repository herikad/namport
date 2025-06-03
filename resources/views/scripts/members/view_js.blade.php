<script type="text/javascript">
    window.onload = function() {

        'use strict';
        var MemberForm = window.MemberForm || {};

        MemberForm.regexTagMatch = /\[(.*?)\]/g
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
                MemberForm.processExceptions(e);
            }
        };

        MemberForm.initEvents = function() {

        };

        MemberForm.processExceptions = function(e) {
            showErrorMessage(e);
        };
        MemberForm.initEvents();

    };
</script>
