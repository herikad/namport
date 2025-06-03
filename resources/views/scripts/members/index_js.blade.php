<script>

  window.onload = function() {
      'use strict';

      var MemberList = window.MemberList || {};
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
              MemberList.processExceptions(e);
          }
      };

      MemberList.UserTbls = '';
      MemberList.allbranchDetails = [];
      MemberList.filterOption = false;


      MemberList.initEvents = function() {

          MemberList.UserTbls = $('#member_list_tbl').DataTable( {
            //   responsive: true,
              processing: true,
              serverSide: true,
              "ajax": {
                  "type" : "POST",
                  "url" : baseUrl + 'members/member_json_list',
                  "data": function ( d ) {
                      d._token = $('meta[name="csrf-token"]').attr('content'),
                      d.filter = MemberList.filterOption,
                      d.is_coach = 0
                  },
                  "dataSrc" : function (json) {
                      MemberList.allbranchDetails = json.data;
                      return json.data;
                  }
              },
              "columns": [
                    { data: "member_id",
                        "render": function ( data, type, row ) {

                            return row.member_id;
                        }
                    },
                    { data: "member_id",
                        "render": function ( data, type, row, meta ) {

                            return meta.row+1;
                        }
                    },
                    {
                        data: 'profile_pic',
                        name: 'profile_pic',
                        render: function(data, type, full, meta) {
                        if (data) {

                                    return "<img src=\"" + data +
                                        "\" class='img-fluid rounded-circle w-px-50 h-px-50' width='50px' height='50px'/ onerror=\"this.src='{{ asset('/no_image.jpg') }}'\">";
                            } else {
                                return "<img src={{ asset('/no_image.jpg') }}  class='img-fluid rounded-circle w-px-50 h-px-50'>";
                            }
                        }
                    },
                    {data: 'member_name',
                        "render": function(data, type, row, meta) {
                           return showTextOnHoverDataTbl(data);
                        }
                    },
                    {data: 'email'},
                    {
                        data: "member_id",
                        "render": function(data, type, row, meta) {
                            var country_code = row.country_code;
                            var mobile = row.phone;
                            var phone = country_code + ' ' + mobile;
                            return (row.country_code != null) && (row.phone != null) ? '+' + phone : '-';
                        }
                    },
                    {
                        data: 'gender_type_term',

                    },
                    {
                        data: 'member_type',
                        "render": function(data, type, row, meta) {
                           return status_term_badge(data);
                        }
                    },
                    {data: 'pickleball_level_term'},
                    {
                        data: 'avg_rating',
                        "render": function(data, type, row, meta) {
                            var html = '';
                            if(data != null && data != ''){
                                html += '<i class="bx bx-star">'+data+'</i>';
                            }else{
                                html += '<i class="bx bx-star">0</i>';
                            }
                            return html;
                        }
                    },
                    {
                        data: 'deleted_at',
                        "render": function(data, type, row, meta) {
                            return (data ? convertUtcDateTimeToLocalDateTime(data,'MM-DD-YYYY') : '-');
                        }
                    },
                    {
                        "data": "is_active",
                        "render": function(data, type, row) {

                            var html = '';

                              html+=` <div class="switches-stacked" title="${(parseInt(row.is_active) == 1 ? 'Click to Deactivate' : ' Click to Activate')}">
                                        <label class="switch">
                                            <input type="checkbox" class="switch-input member_status" name="switches-stacked-radio" data-id="${row.member_id}"  ${(parseInt(row.is_active) == 1 ? 'checked' : '')}  />
                                            <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                            </span>
                                        </label></div>`;

                            return html;
                        }
                    },
                    {
                        data: "member_id",
                        render: function(data, type, row) {

                            var html = '';

                                html += ' <a title="View" href="' + baseUrl + 'members/view/' + row.member_id + '" class="text-end"><i class="bx bx-show theme-text-secondary bx-sm mr-50"  ></i></a>';

                            return html;
                        }
                    },


              ],
              "order": [[ 0, 'desc' ]],
              'columnDefs': [
                  {
                      'targets': [0 ,9, 10], // column index (start from 0)
                      'orderable': false, // set orderable false for selected columns
                  },
                  { "visible": false,  "targets": [ 0 ] }
              ]
          });

          $('body').on('change', '.member_status', function() {

              let is_active = $(this).prop('checked') === true ? 1 : 0;
              let member_id = $(this).data('id');

              $.ajax({

                  type: "POST",
                  url:  baseUrl + 'members/status/update',
                  data: { 'is_active': is_active, 'member_id': member_id },
                  success: function(data) {
                      showSuccessMessage(data.message);
                      MemberList.UserTbls.ajax.reload();
                  }
              });
              });


      }


      MemberList.processExceptions = function(e) {
          showErrorMessage(e);
      };

      MemberList.initEvents();
  };

</script>
