<script>

window.onload = function() {

  'use strict';
  var AppSettingForm = window.AppSettingForm || {};

  AppSettingForm.regexTagMatch = /\[(.*?)\]/g
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
          AppSettingForm.processExceptions(e);
      }
  };

  AppSettingForm.initEvents = function() {

    $('#app-settings-frm-btn').on('click', function(e) {
      submitForm('#app-settings-frm', '', '', (response) => {

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

  };

  AppSettingForm.processExceptions = function(e) {
      showErrorMessage(e);
  };
  AppSettingForm.initEvents();

};
</script>
