<script>

  window.onload = function() {
      'use strict';

      var UserList = window.UserList || {};
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
              UserList.processExceptions(e);
          }
      };

      UserList.UserTbls = '';
      UserList.allUserDetails = [];
      UserList.filterOption = false;

      UserList.page_action = @json($page_action);

      UserList.initEvents = function() {

          UserList.UserTbls = $('#user_list_tbl').DataTable( {
              responsive: true,
              processing: true,
              serverSide: true,
              "ajax": {
                  "type" : "POST",
                  "url" : baseUrl + 'setup/user-management/user/user_json_list',
                  "data": function ( d ) {
                      d._token = $('meta[name="csrf-token"]').attr('content'),
                      d.filter = UserList.filterOption,
                      d.options = {
                          // 'payment_status_term' :  $('#payment_status_term').val(),
                          // 'status_term' :  $('#status_term').val(),
                          // 'start_date' :  $('#start_date').val() !== '' ? convertLocalDateTimeToUtcDateTime(moment($('#start_date').val()+" 00:00:00", "DD/MM/YYYY h:mm:ss").format("MM-DD-YYYY HH:mm:ss")) : '',
                          // 'end_date' :  $('#end_date').val() !== '' ? convertLocalDateTimeToUtcDateTime(moment($('#end_date').val()+" 23:59:59", "DD/MM/YYYY h:mm:ss").format("MM-DD-YYYY HH:mm:ss")) : '',
                      }
                  },
                  "dataSrc" : function (json) {
                      UserList.allUserDetails = json.data;
                      return json.data;
                  }
              },
              "columns": [
                  { "data": "user_id",
                      "render": function ( data, type, row ) {
                          return row.user_id;
                      }
                  },
                  { "data": "user_id",
                      "render": function ( data, type, row, meta  ) {
                          return meta.row+1;
                      }
                  },
                  { "data": "display_name"},
                  { "data": "user_name" },
                  // { "data": "association_type_term" },
                  {
                      "data": "is_active",
                      "render": function(data, type, row) {

                          var html = '';

                            html+=` <div class="switches-stacked" title="${(parseInt(row.is_active) == 1 ? 'Click to Deactivate' : ' Click to Activate')}" ${(typeof(UserList.page_action) != undefined && UserList.page_action !== null && UserList.page_action.is_update === 1) ? '' : 'style="pointer-events:none;"'}">
                                      <label class="switch">
                                          <input type="checkbox" class="switch-input user_active_status" name="switches-stacked-radio" data-id="${row.user_id}"  ${(parseInt(row.is_active) == 1 ? 'checked' : '')}  />
                                          <span class="switch-toggle-slider">
                                          <span class="switch-on"></span>
                                          <span class="switch-off"></span>
                                          </span>
                                      </label></div>`;

                          return html;
                      }
                  },
                  {
                      "data": "user_id",
                      "render": function(data, type, row) {

                            var html = '';

                                html += ' <a title="Edit"'+(typeof(UserList.page_action) != undefined && UserList.page_action !== null && UserList.page_action.is_update === 1 ? '' : 'style="pointer-events:none;"')+'href="' + baseUrl + 'setup/user-management/user/create/' + row.user_id + '" class="text-end"><i class="bx bx-edit theme-text-secondary bx-sm mr-50"></i></a>';

                          return html;
                      }
                  },

              ],
              "order": [[ 0, 'desc' ]],
              'columnDefs': [
                  {
                      'targets': [0 ,1, 4, 5], // column index (start from 0)
                      'orderable': false, // set orderable false for selected columns
                  },
                  { "visible": false,  "targets": [ 0 ] }
              ]
          });

          $('body').on('change', '.user_active_status', function() {

              let is_active = $(this).prop('checked') === true ? 1 : 0;
              let user_id = $(this).data('id');

              $.ajax({

                  type: "POST",
                  url:  baseUrl + 'setup/user-management/user/status/update',
                  data: { 'is_active': is_active, 'user_id': user_id },
                  success: function(data) {
                      // console.log(data.message);
                      showSuccessMessage(data.message);
                      UserList.UserTbls.ajax.reload();
                  }
              });
          });

      }

      UserList.processExceptions = function(e) {
          showErrorMessage(e);
      };

      UserList.initEvents();
  };

</script>
