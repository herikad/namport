<script type="text/javascript">
  window.onload = function() {

      'use strict';
      var UserForm = window.UserForm || {};

      UserForm.regexTagMatch = /\[(.*?)\]/g
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
              UserForm.processExceptions(e);
          }
      };

      UserForm.initEvents = function() {

        $('#saveUserBtn').on('click', function(e) {

        //   showLoadingDialog();

          submitForm('#user_form_id', '', '', (response) => {

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

        $('#user_type_term').on('change', function(e) {
          var userTypeTerm = $(this).val();
          console.log(userTypeTerm)

          $.ajax({
            type: "GET",
            url:  baseUrl + 'setup/user-management/user/roles_by_type_term',
            data: { user_type_term: userTypeTerm },
            success: function(response) {
              // Update the role_id select element with the retrieved roles
              var roleSelect = $('select[name="role_id"]');
              roleSelect.empty(); // Clear previous options

              // Add the "Please select role" option as the default option
              roleSelect.append($('<option>', {
                value: '',
                text: 'Please select role'
              }));

              // Add the retrieved roles as options to the select element
              if (response.length > 0) {
                $.each(response, function(index, role) {
                  roleSelect.append($('<option>', {
                    value: role.role_id,
                    text: role.display_name
                  }));
                });
              }
            },
            error: function() {
              // Handle error case
              console.log('Error fetching roles');
            }
          });

        });
      };

      UserForm.processExceptions = function(e) {
          showErrorMessage(e);
      };
      UserForm.initEvents();

  };
</script>
