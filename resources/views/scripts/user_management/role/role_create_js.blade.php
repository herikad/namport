<script>

  window.onload = function() {
      'use strict';

      var RoleForm = window.RoleForm || {};
      var xhr = null;

      if (window.XMLHttpRequest) {
          xhr = window.XMLHttpRequest;
      }
      else if(window.ActiveXObject('Microsoft.XMLHTTP')) {
          xhr = window.ActiveXObject('Microsoft.XMLHTTP');
      }

      var send = xhr.prototype.send;

      xhr.prototype.send = function(data) {
          try{
              send.call(this, data);
          }
          catch(e) {
              RoleForm.processExceptions(e);
          }
      };

      RoleForm.valid = false;

      RoleForm.initEvents = function() {

            @if (isset($role_details))
                var user_type = $("#user_type_term").val();
                RoleForm.moduleRightsUi(user_type);
            @endif

            $('body').on('click','.chk_action',function(e){
                var action = $(this).data('action');
                var data_id = $(this).data('id');

                    if(action == 'view'){

                        if ($(this).is(':checked')){

                            $('.view_menu_chk[data-id="'+data_id+'"]').prop("checked", true);
                        }
                        else{

                            $('.view_menu_chk[data-id="'+data_id+'"]').prop("checked", false);
                        }

                    }

                    if(action == 'create'){

                        if ($(this).is(':checked')){

                            $('.create_menu_chk[data-id="'+data_id+'"]').prop("checked", true);
                        }
                        else{

                            $('.create_menu_chk[data-id="'+data_id+'"]').prop("checked", false);
                        }

                    }

                    if(action == 'update'){

                        if ($(this).is(':checked')){

                            $('.update_menu_chk[data-id="'+data_id+'"]').prop("checked", true);
                        }
                        else{

                            $('.update_menu_chk[data-id="'+data_id+'"]').prop("checked", false);
                        }

                    }

                    if(action == 'delete'){

                        if ($(this).is(':checked')){

                            $('.delete_menu_chk[data-id="'+data_id+'"]').prop("checked", true);
                        }
                        else{

                            $('.delete_menu_chk[data-id="'+data_id+'"]').prop("checked", false);
                        }

                    }

                    if(action == 'export'){

                        if ($(this).is(':checked')){

                            $('.export_menu_chk[data-id="'+data_id+'"]').prop("checked", true);
                        }
                        else{

                            $('.export_menu_chk[data-id="'+data_id+'"]').prop("checked", false);
                        }

                    }

            });

            $('body').on('click', '#saveRoleBtn', function(e){

                var options = {
                    rules: {

                        role_name:{
                            required:true,
                        },
                        display_name:{
                            required:true,
                        },
                        role_details:{
                            required:true,
                        },
                    },
                    messages:{

                    },
                };

                $('#user_role_Form_id').validate({

                    rules : options.rules ? options.rules : {},
                    messages : options.messages ? options.messages : {},
                    errorPlacement: function (error, element) {

                        if(element.attr("data-element-ref") == 'select2'){
                            $(':input[name="'+element.attr("name")+'"]').next().append(error);
                            // error.insertAfter(element);
                        }else{
                            error.insertAfter(element);
                        }

                    },

                    submitHandler: function(form, event) {
                        event.preventDefault();
                        var role_right_array =  [];

                        $(".role_right").each(function() {

                            var right_id = $(this).data('id');

                            if($('.role_right[data-id="'+right_id+'"] .form-check-input:checked').length > 0){

                                RoleForm.valid = true;

                                var is_view = 0;
                                    var is_create = 0;
                                    var is_update = 0;
                                    var is_delete = 0;
                                    var is_export =  0;

                                if($('.view_menu_chk[data-right_id="'+right_id+'"]').is(":checked")) {
                                    var is_view = $('.view_menu_chk').val();
                                }
                                if($('.create_menu_chk[data-right_id="'+right_id+'"]').is(":checked")) {
                                    var is_create = $('.create_menu_chk').val();
                                }
                                if($('.update_menu_chk[data-right_id="'+right_id+'"]').is(":checked")) {
                                    var is_update = $('.update_menu_chk').val();
                                }
                                if($('.delete_menu_chk[data-right_id="'+right_id+'"]').is(":checked")) {
                                    var is_delete = $('.delete_menu_chk').val();
                                }
                                if($('.export_menu_chk[data-right_id="'+right_id+'"]').is(":checked")) {
                                    var is_export = $('.export_menu_chk').val();
                                }

                                role_right_array.push({right_id:right_id,is_view: is_view,is_create: is_create,is_update: is_update,is_delete: is_delete,is_export:is_export});

                            }

                        })
                        if (RoleForm.valid) {
                            $("#role_right_id").val(JSON.stringify(role_right_array));
                            form.submit();
                        } else {
                            showErrorMessage("Please select role.")
                        }

                    }
                });
            });

            $('body').on('change','#user_type_term',function(e){

                var user_type = $("#user_type_term").val();
                RoleForm.moduleRightsUi(user_type);

            });

      }

      RoleForm.moduleRightsUi = function(user_type=''){

          $( "#rights_container" ).html("");
          showSpinner("#rights_container");

          var role_id = $("#role_id").val();

          var formData = new FormData();
          formData.append('user_type', user_type);

          if(role_id){
              formData.append('role_id', role_id);
          }

          window.getResponseInJsonFromURL(baseUrl + 'setup/user-management/role/get_module_right_list ', formData, (response) => {

                hideSpinner("#rights_container");
                $('#rights_container').append(response.view);

          }, (error) => { console.log(error); }, 'POST');

      };

      RoleForm.processExceptions = function(e) {
          showErrorMessage(e);
      };

      RoleForm.initEvents();
  };



</script>
