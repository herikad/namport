<script>

    window.onload = function() {
        'use strict';

        var MailTempRef = window.MailTempRef || {};
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
                MailTempRef.processExceptions(e);
            }
        };

        MailTempRef.mailTbls = '';
        MailTempRef.allMailTempLateDetails = [];
        MailTempRef.filterOption = false;

        MailTempRef.page_action = @json($page_action);

        MailTempRef.initEvents = function() {

            MailTempRef.mailTbls = $('#mail_list_tbl').DataTable( {
                responsive: true,
                processing: true,
                serverSide: true,
                "ajax": {
                    "type" : "POST",
                    "url" : baseUrl + 'setup/configuration/email-template/mail_template_setting_list_json',
                    "data": function ( d ) {
                        d._token = $('meta[name="csrf-token"]').attr('content'),
                        d.filter = MailTempRef.filterOption,
                        d.options = {
                            // 'payment_status_term' :  $('#payment_status_term').val(),
                            // 'status_term' :  $('#status_term').val(),
                            // 'start_date' :  $('#start_date').val() !== '' ? convertLocalDateTimeToUtcDateTime(moment($('#start_date').val()+" 00:00:00", "DD/MM/YYYY h:mm:ss").format("MM-DD-YYYY HH:mm:ss")) : '',
                            // 'end_date' :  $('#end_date').val() !== '' ? convertLocalDateTimeToUtcDateTime(moment($('#end_date').val()+" 23:59:59", "DD/MM/YYYY h:mm:ss").format("MM-DD-YYYY HH:mm:ss")) : '',
                        }
                    },
                    "dataSrc" : function (json) {
                        MailTempRef.allMailTempLateDetails = json.data;
                        return json.data;
                    }
                },
                "columns": [
                    { "data": "email_template_id",
                        "render": function ( data, type, row ) {
                            return row.email_template_id;
                        }
                    },
                    { "data": "email_template_id",
                        "render": function ( data, type, row, meta  ) {
                            return meta.row+1;
                        }
                    },
                    { "data": "mail_type"},
                    { "data": "email_id"},
                    { "data": "title" },
                    {
                        "data": "is_active",
                        "render": function(data, type, row) {

                            var html = '';

                            // if(typeof(MailTempRef.page_action) != "undefined" && MailTempRef.page_action !== null && MailTempRef.page_action.is_update === 1){


                              html+=` <div class="switches-stacked" title="${(parseInt(row.is_active) == 1 ? 'Click to Deactivate' : ' Click to Activate')}" ${(typeof(MailTempRef.page_action) != undefined && MailTempRef.page_action !== null && MailTempRef.page_action.is_update === 1) ? '' : 'style="pointer-events:none;"'}>
                                        <label class="switch">
                                            <input type="checkbox" class="switch-input mail_active_status" name="switches-stacked-radio" data-id="${row.email_template_id}" ${(parseInt(row.is_default) == 1 ? 'disabled' : '')} ${(parseInt(row.is_active) == 1 ? 'checked' : '')} />
                                            <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                            </span>
                                        </label></div>`;
                            // }else{

                            //   html +=     '<div class="custom-control custom-switch mr-1">';
                            //   html +=     '<input type="checkbox" '+ (parseInt(row.is_default) == 1 ? 'disabled' : '') +'  class="custom-control-input status_term" ' + (parseInt(row.is_active) == 1? 'checked' : '') + '>';
                            //   html +=     '<label class="custom-control-label"></label> ';
                            //   html +=     '</div>';

                            // }

                            return html;
                        }
                    },
                    {
                        "data": "email_template_id",
                        "render": function(data, type, row) {

                            var html = '';

                            // if(typeof(MailTempRef.page_action) != "undefined" && MailTempRef.page_action !== null && MailTempRef.page_action.is_update === 1 && parseInt(row.is_default) != 1){

                                html += ' <a title="Edit" '+(typeof(MailTempRef.page_action) != undefined && MailTempRef.page_action !== null && MailTempRef.page_action.is_update === 1 ? '' : 'style="pointer-events:none;"')+' href="' + baseUrl + 'setup/configuration/email-template/edit/' + row.email_template_id + '"><i class="bx bx-edit theme-text-secondary bx-sm mr-50"></i></a>';

                            // }

                            return html;
                        }
                    },

                ],
                "order": [[ 0, 'desc' ]],
                'columnDefs': [
                    {
                        'targets': [0 , 1 ,5 ,6], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    },
                    { "visible": false,  "targets": [ 0 ] }
                ]
            });

            $('body').on('change', '.mail_active_status', function() {

                let status_term = $(this).prop('checked') === true ? 1 : 0;
                let id = $(this).data('id');

                $.ajax({

                    type: "POST",
                    url:  baseUrl + 'setup/configuration/email-template/status/update',
                    data: { 'is_active': status_term, 'id': id },
                    success: function(data) {
                        // console.log(data.message);
                        showSuccessMessage(data.message);
                        MailTempRef.mailTbls.ajax.reload();
                    }
                });
            });

        }

        MailTempRef.processExceptions = function(e) {
            showErrorMessage(e);
        };

        MailTempRef.initEvents();
    };

</script>


