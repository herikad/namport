<script type="text/javascript">
  window.onload = function() {

      'use strict';
      var DepartmentForm = window.DepartmentForm || {};

      DepartmentForm.regexTagMatch = /\[(.*?)\]/g
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
              DepartmentForm.processExceptions(e);
          }
      };

      DepartmentForm.initEvents = function() {

        $('#saveDepartmentBtn').on('click', function(e) {

          submitForm('#department_form_id', '', '', (response) => {

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


        CKEDITOR.replace('detail_description', {
            height: 300,
        });

    
      };

      DepartmentForm.processExceptions = function(e) {
          showErrorMessage(e);
      };
      DepartmentForm.initEvents();

  };
</script>
