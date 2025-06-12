<script>
    window.onload = function() {
        'use strict';

        var DesignationtList = window.DesignationtList || {};
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
                DesignationtList.processExceptions(e);
            }
        };

        DesignationtList.DesignationtTbls = '';
        DesignationtList.allbranchDetails = [];
        DesignationtList.filterOption = false;

        DesignationtList.initEvents = function() {

            DesignationtList.DesignationtTbls = $('#designation_list_tbl').DataTable({
                //   responsive: true,
                processing: true,
                serverSide: true,
                "ajax": {
                    "type": "POST",
                    "url": baseUrl + 'setup/designation/designation_json_list',
                    "data": function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content'),
                            d.filter = DesignationtList.filterOption,
                            d.is_coach = 0
                    },
                    "dataSrc": function(json) {
                        DesignationtList.allbranchDetails = json.data;
                        console.log(json.data);

                        return json.data;
                    }
                },
                "columns": [{
                        data: "designation_id",
                        "render": function(data, type, row) {

                            return row.designation_id;
                        }
                    },
                    {
                        data: "designation_id",
                        "render": function(data, type, row, meta) {

                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'designation_name'
                    },
                    {
                        data: 'company_name'
                    },
                    {
                        "data": "is_active",
                        "render": function(data, type, row) {
                            var html = '';
                            html += ` <div class="switches-stacked" title="${(parseInt(row.is_active) == 1 ? 'Click to Deactivate' : ' Click to Activate')}">
                                        <label class="switch">
                                            <input type="checkbox" class="switch-input member_status" name="switches-stacked-radio" data-id="${row.designation_id}"  ${(parseInt(row.is_active) == 1 ? 'checked' : '')}  />
                                            <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                            </span>
                                        </label></div>`;

                            return html;
                        }
                    },
                    {
                        data: 'created_at',
                        "render": function(data, type, row, meta) {
                            return formatDateValueInInput(row.created_at);
                        }
                    },
                    {
                        data: "designation_id",
                        render: function(data, type, row) {

                            var html = '';

                            html += ' <a title="Edit" href="' + baseUrl +
                                'setup/designation/create/' + row.designation_id +
                                '" class="text-end"><i class="bx bx-edit theme-text-secondary bx-sm mr-50"  ></i></a>';

                            return html;
                        }
                    },
                ],
                "order": [
                    [0, 'desc']
                ],
                'columnDefs': [{
                        'targets': [0, 6], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    },
                    {
                        "visible": false,
                        "targets": [0]
                    }
                ]
            });

            $('body').on('change', '.member_status', function() {

                let is_active = $(this).prop('checked') === true ? 1 : 0;
                let designation_id = $(this).data('id');

                $.ajax({
                    type: "POST",
                    url: baseUrl + 'setup/designation/status/update',
                    data: {
                        'is_active': is_active,
                        'designation_id': designation_id
                    },
                    success: function(data) {
                        showSuccessMessage(data.message);
                        DesignationtList.DesignationtTbls.ajax.reload();
                    }
                });
            });
        }

        DesignationtList.processExceptions = function(e) {
            showErrorMessage(e);
        };

        DesignationtList.initEvents();
    };
</script>
