<script>

  window.onload = function() {
      'use strict';

      var RoleRef = window.RoleRef || {};
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
              RoleRef.processExceptions(e);
          }
      };

      RoleRef.RoleTbls = '';
      RoleRef.allRoleDetails = [];
      RoleRef.filterOption = false;

      RoleRef.page_action = @json($page_action);

      RoleRef.initEvents = function() {

          RoleRef.RoleTbls = $('#role_list_tbl').DataTable( {
              responsive: true,
              processing: true,
              serverSide: true,
              "ajax": {
                  "type" : "POST",
                  "url" : baseUrl + 'setup/user-management/role/get_role_list_json',
                  "data": function ( d ) {
                      d._token = $('meta[name="csrf-token"]').attr('content'),
                      d.filter = RoleRef.filterOption,
                      d.options = {
                          // 'payment_status_term' :  $('#payment_status_term').val(),
                          // 'status_term' :  $('#status_term').val(),
                          // 'start_date' :  $('#start_date').val() !== '' ? convertLocalDateTimeToUtcDateTime(moment($('#start_date').val()+" 00:00:00", "DD/MM/YYYY h:mm:ss").format("MM-DD-YYYY HH:mm:ss")) : '',
                          // 'end_date' :  $('#end_date').val() !== '' ? convertLocalDateTimeToUtcDateTime(moment($('#end_date').val()+" 23:59:59", "DD/MM/YYYY h:mm:ss").format("MM-DD-YYYY HH:mm:ss")) : '',
                      }
                  },
                  "dataSrc" : function (json) {
                      RoleRef.allRoleDetails = json.data;
                      return json.data;
                  }
              },
              "columns": [
                  { "data": "role_id",
                      "render": function ( data, type, row ) {
                          return row.role_id;
                      }
                  },
                  { "data": "role_id",
                      "render": function ( data, type, row, meta  ) {
                          return meta.row+1;
                      }
                  },
                  { "data": "role_name"},
                  { "data": "display_name"},
                  { "data": "user_type_term"},
                  { "data": "role_details" },
                  {
                      "data": "is_active",
                      "render": function(data, type, row) {

                          var html = '';

                            html+=` <div class="switches-stacked" title="${(parseInt(row.is_active) == 1 ? 'Click to Deactivate' : ' Click to Activate')}" ${(typeof(RoleRef.page_action) != undefined && RoleRef.page_action !== null && RoleRef.page_action.is_update === 1) ? '' : 'style="pointer-events:none;"'}">
                                      <label class="switch">
                                          <input type="checkbox" class="switch-input role_active_status" name="switches-stacked-radio" data-id="${row.role_id}"  ${(parseInt(row.is_active) == 1 ? 'checked' : '')}  />
                                          <span class="switch-toggle-slider">
                                          <span class="switch-on"></span>
                                          <span class="switch-off"></span>
                                          </span>
                                      </label></div>`;

                          return html;
                      }
                  },
                  {
                      "data": "role_id",
                      "render": function(data, type, row) {

                          var html = '';
                          html += ' <a title="Edit" href="' + baseUrl + 'setup/user-management/role/create/' + row.role_id + '"><i class="bx bx-edit theme-text-secondary bx-sm mr-50"></i></a>';
                          // html += ' <a '+(typeof(RoleRef.page_action) != undefined && RoleRef.page_action !== null && RoleRef.page_action.is_update === 1 ? '' : 'style="pointer-events:none;"')+' href="' + baseUrl + 'setup/user-management/role/create/' + row.role_id + '"><i class="bx bx-edit theme-text-secondary bx-sm mr-50"></i></a>';

                          return html;
                      }
                  },

              ],
              "order": [[ 0, 'desc' ]],
              'columnDefs': [
                  {
                      'targets': [0 , 6], // column index (start from 0)
                      'orderable': false, // set orderable false for selected columns
                  },
                  { "visible": false,  "targets": [ 0 ] }
              ]
          });

          $('body').on('change', '.role_active_status', function() {

              let is_active = $(this).prop('checked') === true ? 1 : 0;
              let role_id = $(this).data('id');

              $.ajax({

                  type: "POST",
                  url:  baseUrl + 'setup/user-management/role/status/update',
                  data: { 'is_active': is_active, 'role_id': role_id },
                  success: function(data) {
                      // console.log(data.message);
                      showSuccessMessage(data.message);
                      RoleRef.RoleTbls.ajax.reload();
                  }
              });
          });

      }

      RoleRef.processExceptions = function(e) {
          showErrorMessage(e);
      };

      RoleRef.initEvents();
  };

</script>
