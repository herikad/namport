<script>
  window.onload = function() {
      'use strict';

      var ChangePassword = window.ChangePassword || {};
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
              ChangePassword.processExceptions(e);
          }
      };

      ChangePassword.initEvents = function() {

        $('#submitPass').on('click', function(e) {
            var options = {
                rules: {
                        'new_password': {
                        required: true
                        },
                        'con_password': {
                        required: true,
                        equalTo: '#new_password'
                        }
              },

            }
          submitForm('#changePassForm', options, true, (response) => {

            hideLoadingDialog();

          }, (error) => {
                    // ajax error callback
                    hideLoadingDialog();
                    showErrorMessage(error);
          });
        });

      }

      ChangePassword.processExceptions = function(e) {
          showErrorMessage(e);
      };

      ChangePassword.initEvents();
  };
</script>
