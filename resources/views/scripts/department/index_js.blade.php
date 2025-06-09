<script>

  window.onload = function() {
      'use strict';

      var DepartmentList = window.DepartmentList || {};
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
              DepartmentList.processExceptions(e);
          }
      };

      DepartmentList.DepartmentTbls = '';
      DepartmentList.allbranchDetails = [];
      DepartmentList.filterOption = false;

      DepartmentList.initEvents = function() {

          DepartmentList.DepartmentTbls = $('#department_list_tbl').DataTable( {
            //   responsive: true,
              processing: true,
              serverSide: true,
              "ajax": {
                  "type" : "POST",
                  "url" : baseUrl + 'setup/department/department_json_list',
                  "data": function ( d ) {
                      d._token = $('meta[name="csrf-token"]').attr('content'),
                      d.filter = DepartmentList.filterOption,
                      d.is_coach = 0
                  },
                  "dataSrc" : function (json) {
                      DepartmentList.allbranchDetails = json.data;
                      console.log(json.data);
                      
                      return json.data;
                  }
              },
              "columns": [
                    { data: "department_id",
                        "render": function ( data, type, row ) {

                            return row.department_id;
                        }
                    },
                    { data: "department_id",
                        "render": function ( data, type, row, meta ) {

                            return meta.row+1;
                        }
                    },
                    
                    {data: 'department_name' },
                  
                    { data: 'department_code' },

                    { data: 'company_name' },
                   
                    
                    {
                        "data": "is_active",
                        "render": function(data, type, row) {
                            var html = '';
                              html+=` <div class="switches-stacked" title="${(parseInt(row.is_active) == 1 ? 'Click to Deactivate' : ' Click to Activate')}">
                                        <label class="switch">
                                            <input type="checkbox" class="switch-input member_status" name="switches-stacked-radio" data-id="${row.department_id}"  ${(parseInt(row.is_active) == 1 ? 'checked' : '')}  />
                                            <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                            </span>
                                        </label></div>`;

                            return html;
                        }
                    },
                    {data: 'created_at',
                        "render": function(data, type, row, meta) {
                           return formatDateValueInInput(row.created_at);
                        }
                    },
                    {
                        data: "department_id",
                        render: function(data, type, row) {

                            var html = '';

                                // html += ' <a title="View" href="' + baseUrl + 'members/view/' + row.member_id + '" class="text-end"><i class="bx bx-show theme-text-secondary bx-sm mr-50"  ></i></a>';

                            return html;
                        }
                    },


              ],
              "order": [[ 0, 'desc' ]],
              'columnDefs': [
                  {
                      'targets': [0 ,7], // column index (start from 0)
                      'orderable': false, // set orderable false for selected columns
                  },
                  { "visible": false,  "targets": [ 0 ] }
              ]
          });

            $('body').on('change', '.member_status', function() {

              let is_active = $(this).prop('checked') === true ? 1 : 0;
              let department_id = $(this).data('id');

              $.ajax({

                  type: "POST",
                  url:  baseUrl + 'setup/department/status/update',
                  data: { 'is_active': is_active, 'department_id': department_id },
                  success: function(data) {
                      showSuccessMessage(data.message);
                      DepartmentList.DepartmentTbls.ajax.reload();
                  }
              });
            });


      }


      DepartmentList.processExceptions = function(e) {
          showErrorMessage(e);
      };

      DepartmentList.initEvents();
  };

</script>
