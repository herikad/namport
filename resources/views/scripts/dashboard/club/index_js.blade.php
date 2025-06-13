<script type="text/javascript">
    window.onload = function() {

        'use strict';
        var Dashboard = window.Dashboard || {};
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
                Dashboard.processExceptions(e);
            }
        };


        Dashboard.ClaimClubTbls = '';
        Dashboard.allClaimClubDetails = [];

        Dashboard.ClubTbls = '';
        Dashboard.allClubDetails = [];

        Dashboard.PendingPayClubTbls = '';
        Dashboard.PendingPayClubTblsList = [];

        Dashboard.filterOption = false;

        var auth_user_id = "{{ $auth_user->association_id }}";

        Dashboard.page_action = @json($page_action);

        Dashboard.initEvents = function() {

            // Claim Club Request
            Dashboard.ClaimClubTbls = $('#claim_club_list').DataTable({

                    // responsive: true,
                    processing: true,
                    serverSide: true,
                    "ajax": {
                        "type": "POST",
                        "url": baseUrl + 'club/claim_club_json_list',
                        "data": function(d) {

                            d._token = $('meta[name="csrf-token"]').attr('content'),
                                d.filter = Dashboard.filterOption,
                                d.options = {

                                },
                                d.club_manager_id = auth_user_id

                        },
                        "dataSrc": function(json) {
                            Dashboard.allClaimClubDetails = json.data;
                            return json.data;
                        }
                    },
                    "columns": [{
                            data: "claim_req_id",
                            "render": function(data, type, row) {
                                return row.claim_req_id;
                            }
                        },
                        {
                            data: "claim_req_id",
                            "render": function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {
                            data: "display_name"
                        },
                        {
                            data: "club_name",
                            'render': function (data, type, row) {
                                var name = `<a class="primary-text-color" href="${baseUrl}club/view/${row.claim_association_id}" >${showTextOnHoverDataTbl(data)}</a>`;
                                return (data ? name : '');
                            },
                        },
                        {
                            data: "address",
                            "render": function(data, type, row, meta) {
                                    return (data ? showTextOnHoverDataTbl(data) : '-');
                            }
                        },
                        {
                            "data": "created_at",
                            "render": function(data, type, row) {

                                return (data ? convertUtcDateTimeToLocalDateTime(data,'MM-DD-YYYY') : '-');
                            }
                        },
                        {
                            "data": "claim_status",
                            "render": function(data, type, row) {

                                return status_term_badge(row.claim_status);
                            }
                        },
                        {
                            "data": "approved_at",
                            "render": function(data, type, row) {

                                return (data ? convertUtcDateTimeToLocalDateTime(data) : '-');
                            }
                        },

                    ],
                    "order": [
                        [0, 'desc']
                    ],
                    'columnDefs': [{
                            'targets': [0],
                            'orderable': false,
                        },
                        {
                            "visible": false,
                            "targets": [0]
                        }
                    ]
            });

            // Assign Club
            Dashboard.ClubTbls = $('#club_list').DataTable({

                // responsive: true,
                processing: true,
                serverSide: true,
                "ajax": {
                    "type": "POST",
                    "url": baseUrl + 'get_assigned_club',
                    "data": function(d) {

                        d._token = $('meta[name="csrf-token"]').attr('content'),
                            d.filter = Dashboard.filterOption,
                            d.options = {

                            },
                            d.club_manager_id = auth_user_id

                    },
                    "dataSrc": function(json) {
                        Dashboard.allClubDetails = json.data;
                        return json.data;
                    }
                },
                "columns": [{
                        data: "club_id",
                        "render": function(data, type, row) {
                            return row.club_id;
                        }
                    },
                    {
                        data: "club_id",
                        "render": function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: "club_manager_name"
                    },
                    {
                        data: "club_name",
                        'render': function (data, type, row) {
                            var name = `<a class="primary-text-color" href="${baseUrl}club/view/${row.claim_association_id}" >${showTextOnHoverDataTbl(data)}</a>`;
                            return (data ? name : '');
                        },
                    },
                    {
                        data: "address",
                        "render": function(data, type, row, meta) {
                                return (data ? showTextOnHoverDataTbl(data) : '-');
                        }
                    },
                    {
                        "data": "approval_status",
                        "render": function(data, type, row) {

                            return status_term_badge(row.approval_status);
                        }
                    },

                ],
                "order": [
                    [0, 'desc']
                ],
                'columnDefs': [{
                        'targets': [0],
                        'orderable': false,
                    },
                    {
                        "visible": false,
                        "targets": [0]
                    }
                ]
            });

            // Order list
            Dashboard.OrdersTbls = $('#order_list_tbl').DataTable({

                // responsive: true,
                processing: true,
                serverSide: true,
                "ajax": {
                    "type": "POST",
                    "url": baseUrl +  'orders/order_json_list',
                    "data": function(d) {

                        d._token = $('meta[name="csrf-token"]').attr('content'),
                                d.filter = Dashboard.filterOption,
                                d.options = {

                                    payment_status : $('#payment_status').val(),
                                    order_item_status  : $('#order_item_status').val(),
                                    start_date   : $('#start_date_filter').val() ? convertLocalDateTimeToUtcDateTime($('#start_date_filter').val()+" 00:00:00") : '',
                                    end_date   : $('#end_date_filter').val() ? convertLocalDateTimeToUtcDateTime($('#end_date_filter').val()+" 23:59:59") : '',
                                }
                    },
                    "dataSrc": function(json) {
                        Dashboard.OrdersTblsList = json.data;
                        return json.data;
                    }
                },
                "columns": [
                        {
                            data: "order_detail_id",
                            "render": function(data, type, row) {
                                return row.order_detail_id;
                            }
                        },
                        {
                            data: "s_no",
                            "render": function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        // {
                        //     data: "order_detail_id",
                        //     "render": function(data, type, row, meta) {

                        //         return '<i class="collapse_icon fa fa-plus"></i>';
                        //     }
                        // },
                        // {
                        //     data: 'order_detail_id',
                        //     render: function(data, type, row, meta) {

                        //         var html = '';

                        //         if(row.item_type == "{{config('custom.cart_item_type.club_booking')}}"){

                        //             html = "<img src=\"" + row.club_profile + "\" class='rounded-circle w-px-40 h-px-40' onerror=\"this.src='{{ asset('/no_image.jpg') }}'\">";

                        //         }else if(row.item_type == "{{config('custom.cart_item_type.trainer_booking')}}"){

                        //             html = "<img src=\"" + row.trainer_profile + "\" class='rounded-circle w-px-40 h-px-40' onerror=\"this.src='{{ asset('/no_image.jpg') }}'\">";

                        //         }

                        //         return html;
                        //     }

                        // },
                        {
                            data: "member_name",
                            "render": function(data, type, row, meta) {
                            var html = `<a href="${baseUrl}members/view/${row.member_id}" class="primary-text-color">${showTextOnHoverDataTbl(row.member_name)} </a> `;
                            return row.member_name ? html : '-';
                            }
                        },
                        {
                            data: "phone",
                        },
                        {
                            data: "item_type",
                            "render": function(data, type, row, meta) {

                                var html = '';

                                if(row.item_type == "{{config('custom.cart_item_type.club_booking')}}"){

                                    html = `<a href="${baseUrl}club/view/${row.club_id}" class="primary-text-color">${showTextOnHoverDataTbl(row.club_name)} </a> `;


                                }else if(row.item_type == "{{config('custom.cart_item_type.trainer_booking')}}"){

                                    html = `<a href="${baseUrl}members/view/${row.club_id}" class="primary-text-color">${showTextOnHoverDataTbl(row.trainer_name)} </a> `;
                                }

                                return html;
                            }
                        },
                        {
                            data: "slot_start_time",
                            "render": function(data, type, row, meta) {

                                return row.slot_start_time ? convertUtcDateTimeToLocalDateTime(row.slot_start_time) +' - '+convertUtcDateTimeToLocalDateTime(row.slot_end_time, 'HH:mm') : '-';
                            }
                        },
                        {
                            data: "total_amount",
                            "render": function(data, type, row, meta) {

                                return row.total_amount ? '$'+formatFloatValue(row.total_amount,2) : '$0.00';
                            }
                        },
                        {
                            data: "order_item_status",
                            "render": function(data, type, row, meta) {

                                return row.order_item_status ? order_status_badge(row.order_item_status) : '-';
                            }
                        },
                        {
                            data: "payment_via",
                            "render": function(data, type, row, meta) {
                                return row.payment_via ? paymentby_status_badge(row.payment_via) : '-';
                            }
                        },
                        {
                            data: "payment_status",
                            "render": function(data, type, row, meta) {

                                return row.payment_status ? status_term_badge(row.payment_status) : '-';
                            }
                        },
                        {
                            data: "refund_status",
                            "render": function(data, type, row, meta) {

                                return row.refund_status ? status_term_badge(row.refund_status) : '-';
                            }
                        },
                        {
                            data: "created_at",
                            "render": function(data, type, row, meta) {

                                return row.created_at ? convertUtcDateTimeToLocalDateTime(row.created_at,'MM/DD/YYYY') : '-';
                            }
                        },
                        {
                            data: "cancelled_on",
                            "render": function(data, type, row, meta) {

                                return row.cancelled_on ? convertUtcDateTimeToLocalDateTime(row.cancelled_on) : '-';
                            }
                        },
                        {
                            data: "sub_order_id",
                            "render": function(data, type, row, meta) {
                                var html = `<a title="View" class="text-dark" href="${baseUrl}orders/view/${data}"><i class="bx bx-show"></i></a>`;
                                return html;
                            }
                        },
                    ],
                "order": [
                    [0, 'desc']
                ],
                'columnDefs': [{
                        'targets': [0,3], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    },
                    {
                        "visible": false,
                        "targets": [0]
                    }
                ]
            });


            // Claim Club Request
            Dashboard.PendingPayClubTbls = $('#pending_payment_verification_club_list').DataTable({

                // responsive: true,
                processing: true,
                serverSide: true,
                "ajax": {
                    "type": "POST",
                    "url": baseUrl + 'club/club_json_list',
                    "data": function(d) {

                        d._token = $('meta[name="csrf-token"]').attr('content'),
                            d.filter = Dashboard.filterOption,
                            d.options = {
                            },
                            d.is_stripe = 1,
                            d.club_manager_id = auth_user_id
                    },
                    "dataSrc": function(json) {
                        Dashboard.PendingPayClubTblsList = json.data;
                        return json.data;
                    }
                },
                "columns": [{
                        data: "club_id",
                        "render": function(data, type, row) {
                            return row.club_id;
                        }
                    },
                    {
                        data: "club_id",
                        "render": function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: "club_name",
                        'render': function (data, type, row) {
                            return (data ? showTextOnHoverDataTbl(data) : '');
                        },
                    },
                    {
                        "data": "club_id",
                        "render": function(data, type, row, meta) {

                                var country_code = row.country_code;
                                var mobile = row.phone_no;
                                var phone = country_code + ' ' + mobile;
                                return (row.country_code != null) && (row.phone_no != null) ? '+' + phone : '-';
                            }
                    },
                    {
                        data: "club_id",
                        "render": function(data, type, row, meta) {
                                return (row ? showTextOnHoverDataTbl(full_address(row)) : '');
                        }
                    },
                    {
                        "data": "approval_status",
                        "render": function(data, type, row) {

                            var html = '';

                                html += status_term_badge(row.approval_status);

                            return html;

                        }
                    },
                    {
                        "data": "club_id",
                        "render": function(data, type, row) {

                            var html = '';

                            if(row.stripe_connect_account_status == "{{ config('custom.stripe.connect_capability_status.active') }}"){
                                html +='<span class="text-success">Verified</span>';
                            }else if(row.stripe_connect_account_status == "{{ config('custom.stripe.connect_capability_status.pending') }}"){
                                html +='<span class="text-warning">Pending</span>';
                            }else{

                                if('{{ $auth_user->user_type_term == config("custom.user_type_term.club_user") }}'){

                                    html += '<a href="javascript:void(0)" class="btn btn-success btn-sm payment-onboarding-btn" data-club-id="'+ row.club_id +'">Verify Payment</a>';
                                }else{

                                    html +='<span class="text-warning">Pending</span>';

                                }

                            }

                            return html;

                        }
                    },
                    {
                        "data": "club_id",
                        "render": function(data, type, row) {

                            var html = '';

                            html += ' <a '+(typeof(Dashboard.page_action) != undefined && Dashboard.page_action !== null && Dashboard.page_action.is_update === 1 ? '' : 'style="pointer-events:none;"')+' href="' + baseUrl + 'club/create/' + row.association_id + '/' + row.club_id + '" class="text-end" title="Edit"><i class="bx bx-edit theme-text-secondary bx-sm mr-50"></i></a>';
                            html += ' <a title="View" href="' + baseUrl + 'club/view/' + row.club_id + '" class="text-end"><i class="bx bx-show theme-text-secondary bx-sm mr-50" ></i></a>';

                            return html;
                        }
                    },

                ],
                "order": [
                    [0, 'desc']
                ],
                'columnDefs': [{
                        'targets': [0, 7], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    },
                    {
                        "visible": false,
                        "targets": [0]
                    }
                ]
            });

        }

        $('body').on('click', '.payment-onboarding-btn', function(e) {
            e.preventDefault();

            var formData = new FormData();
            formData.append('club_id', $(this).data('club-id'));

            var selector = $(this);

            showSpinner(selector, 'sm', 'dark', 'i');

            getResponseInJsonFromURL('{{route("get_payment_onboarding_link")}}', formData, (response) => {

                hideSpinner('');
                if(response.status == 1){
                    window.open(response.data.link, '_blank');
                }else{
                    showErrorMessage(response.message);
                }

            }, processExceptions);
        });

        Dashboard.processExceptions = function(e) {
            showErrorMessage(e);
        };
        Dashboard.initEvents();

    };
</script>
