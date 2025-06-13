<script>
    window.onload = function() {
        'use strict';

        var Template = window.Template || {};
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
                Template.processExceptions(e);
            }
        };

        Template.TempTbls = '';
        Template.allTempDetails = [];
        Template.filterOption = false;

        Template.page_action = @json($page_action);

        var defaultNoImg = "{{ asset('/no_image.jpg') }}";

        Template.initEvents = function() {

            Template.TempTbls = $('#contact_list_tbl').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                "ajax": {
                    "type": "POST",
                    "url": baseUrl + 'client_contacts/contact_list_json',
                    "data": function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content'),
                            d.filter = Template.filterOption
                    },
                    "dataSrc": function(json) {
                        Template.allTempDetails = json.data;
                        return json.data;
                    }
                },
                "columns": [{
                        "data": "client_contacts_id",
                        "render": function(data, type, row) {
                            return row.client_contacts_id;
                        }
                    },
                    {
                        "data": "client_contacts_id",
                        "render": function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        "data": "profile_pic",
                        "render": function(data, type, row) {

                            var html = '';
                            html +=
                                `<div class="avatar avatar-lg me-2"><img onerror="this.onerror=null;this.src='${defaultNoImg}';" class="rounded-circle" src="${row.profile_pic != null ?  assetBaseUrl +'images/client_contact/profile/'+ row.profile_pic : defaultNoImg}" alt="avtar img holder"></div>`
                            return html;
                        }
                    },
                    {
                        "data": "display_name",
                        "render": function(data, type, row, meta) {
                            return row.display_name;
                        }
                    },
                    {
                        "data": "email"
                    },
                    {
                        "data": "mobile_no",
                        "render": function(data, type, row, meta) {
                            return row.mobile_no ? row.mobile_no : '-';
                        }
                    },
                    {
                        "data": "created_at",
                        "render": function(data, type, row) {
                            return row.created_at ? convertUtcDateTimeToLocalDateTime(row
                                    .created_at) :
                                '-';
                        }
                    },
                    {
                        "data": "date_of_joining",
                        "render": function(data, type, row) {
                            return row.date_of_joining
                                ? moment(row.date_of_joining).format('DD/MM/YYYY')
                                : '-';
                        }
                    },
                    {
                        "data": "is_active",
                        "render": function(data, type, row) {
                            var html = '';
                            html += ` <div class="switches-stacked" title="${(parseInt(row.is_active) == 1 ? 'Click to Deactivate' : ' Click to Activate')}">
                                        <label class="switch">
                                            <input type="checkbox" class="switch-input contact_active_status" name="switches-stacked-radio" data-id="${row.client_contacts_id}"  ${(parseInt(row.is_active) == 1 ? 'checked' : '')}  />
                                            <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                            </span>
                                        </label></div>`;

                            return html;
                        }
                    },
                    {
                        "data": "client_contacts_id",
                        "render": function(data, type, row) {

                            var html = '';

                            if(page_action.is_update == 1) {
                                html += `<a href="${baseUrl}client_contacts/create/${row.client_contacts_id}"><i class="bx bx-edit theme-text-secondary bx-sm mr-50"></i></a>`;
                            }

                            // if (page_action.is_delete == 1) {
                            //     html += `<i class="bx bx-trash bx-sm text-danger delete_contact" client_contacts_id ="${row.client_contacts_id}" ></i>`;
                            // }

                            // if (page_action.is_update != 1 && page_action.is_delete != 1) {
                            if (page_action.is_update != 1) {
                                html += `<i class="fas fa-lock text-danger" title="${permission_denied}"></i>`;
                            }

                            return html;
                        }
                    }

                ],
                "order": [
                    [0, 'desc']
                ],
                'columnDefs': [{
                        'targets': [0, 1], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    },
                    {
                        "visible": false,
                        "targets": [0]
                    }
                ]
            });


            // $('body').on('click', '.delete_contact', function() {
            //     var client_contacts_id = $(this).attr('client_contacts_id');

            //     confirmDialogMessage("Delete", "Are you sure want to delete this contact permanently?",
            //         () => {
            //             showLoadingDialog();
            //             $.ajax({
            //                 type: "POST",
            //                 url: baseUrl + "client_contacts/delete",
            //                 data: {
            //                     client_contacts_id: client_contacts_id
            //                 },
            //                 success: function(response) {
            //                     hideLoadingDialog();
            //                     if (response.status == 1) {
            //                         Template.TempTbls.ajax.reload();
            //                         showSuccessMessage(response.message);
            //                     } else {
            //                         showErrorMessage(response.message);
            //                     }
            //                 },
            //             });
            //         }
            //     );
            // });

            $('body').on('change', '.contact_active_status', function() {

                let is_active = $(this).prop('checked') === true ? 1 : 0;
                let client_contacts_id = $(this).data('id');
                let checkbox = $(this);
                let is_default = checkbox.prop('checked') ? 1 : 0;
                $.ajax({

                    type: "POST",
                    url: baseUrl + 'client_contacts/active_status_update',
                    data: {
                        'is_active': is_active,
                        'client_contacts_id': client_contacts_id
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            Template.TempTbls.ajax.reload();
                            showSuccessMessage(data.message);
                        } else {
                            showErrorMessage(data.message);
                            checkbox.prop('checked', !is_default); // Revert toggle state
                        }

                    }
                });
            });
        }

        Template.processExceptions = function(e) {
            showErrorMessage(e);
        };

        Template.initEvents();
    };
</script>
