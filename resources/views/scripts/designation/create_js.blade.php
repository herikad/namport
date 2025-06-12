<script type="text/javascript">
  window.onload = function() {

      'use strict';
      var DesignationForm = window.DesignationForm || {};

      DesignationForm.regexTagMatch = /\[(.*?)\]/g
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
              DesignationForm.processExceptions(e);
          }
      };

      DesignationForm.initEvents = function() {

        $('#saveDesignationBtn').on('click', function(e) {

          submitForm('#designation_form_id', '', '', (response) => {

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


        CKEDITOR.replace('designation_role', {
            height: 200,
        });
         CKEDITOR.replace('desig_responsibilities', {
            height: 300,
        });

    
      };

      DesignationForm.processExceptions = function(e) {
          showErrorMessage(e);
      };
      DesignationForm.initEvents();

  };
</script>
