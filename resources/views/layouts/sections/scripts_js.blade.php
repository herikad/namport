<script>
    (function(window) {
        'use strict';
        $('.select2').select2();

        /*
         * Log application events for analytics usage.
         * @param string event The event name.
         * @param object data The event params.
         */

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        window.getResponseInJsonFromURL = function(urlToCall, dataToSend, furtherFuntionToCall, errorFuntionToCall,
            type = 'post') {

            $.ajax({
                type: type,
                url: urlToCall,
                data: dataToSend,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (typeof furtherFuntionToCall == 'function') {
                        furtherFuntionToCall(response);
                    }

                },
                error: function(jqXHR, textStatus, ex) {
                    if (typeof errorFuntionToCall == 'function') {
                        errorFuntionToCall(jqXHR.responseText);
                    }
                }
            });

        };

        window.getFormInputs = function(form_selector) {

            var formData = new FormData($(form_selector)[0]);

            return formData;
        }

        window.submitForm = function(selector, options, isDirectSubmit = false, furtherFuntionToCall,
            errorFuntionToCall) {

            $(selector).validate({
                rules: options.rules ? options.rules : {},
                messages: options.messages ? options.messages : {},
                errorPlacement: function(error, element) {

                    if (element.attr("data-element-ref") == 'select2') {
                        $(':input[name="' + element.attr("name") + '"]').next().append(error);
                    } else if (element.attr("data-element-ref") == 'image') {
                        error.insertAfter('.image_validate_msg');
                    } else if (element.attr("data-element-ref") == 'watermark_image') {
                        error.insertAfter('.watermark_image_validate_msg');
                    } else if (element.attr("data-element-ref") == 'input-group') {
                        error.insertAfter(element.closest(".input-group"));
                    } else {
                        error.insertAfter(element);
                    }

                },
                submitHandler: function(form, event) {

                    event.preventDefault();
                    if (isDirectSubmit == true) {
                        showLoadingDialog();
                        form.submit();
                    } else {

                        showLoadingDialog();

                        var methodType = form.getAttribute('method') != null ? form.getAttribute(
                            'method') : (options.type ? options.type : '');
                        var actionUrl = form.getAttribute('action') != null ? form.getAttribute(
                            'action') : (options.url ? options.url : '');

                        window.getResponseInJsonFromURL(actionUrl, getFormInputs(selector),
                            furtherFuntionToCall, errorFuntionToCall, methodType);

                        return false;
                    }

                }
            });

        }

        var loadingDialogToast = Swal.mixin({
            title: 'Please wait......',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        window.showLoadingDialog = function(target = '') {

            loadingDialogToast.fire({
                html: `<div class="sk-wave sk-primary m-auto"> <div class="sk-wave-rect"></div> <div class="sk-wave-rect"></div> <div class="sk-wave-rect"></div> <div class="sk-wave-rect"></div></div>`,
                target: (target != '' ? document.getElementById(target) : 'body'),
                onBeforeOpen: () => {
                    Swal.showLoading();
                }

            });
        }

        window.hideLoadingDialog = function() {

            loadingDialogToast.close();

        }

        window.showSuccessMessage = function(title = '', sub_title = '') {

            toastr.options = {
                "closeButton": true,
            }
            toastr.success(title, sub_title);
        }

        window.showErrorMessage = function(title = '', sub_title = '') {

            toastr.options = {
                "closeButton": true,
            }
            toastr.error(title, sub_title);

        }

        window.confirmDialogMessage = function(title_msg, sub_title_msg, furtherFuntionToCall, target = '') {

            Swal.fire({
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                title: title_msg,
                text: sub_title_msg,
                html: sub_title_msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ok',
                target: (target != '' ? document.getElementById(target) : 'body'),
                buttonsStyling: false
            }).then((result) => {

                if (result.value) {

                    furtherFuntionToCall();

                }

            });

        }

        window.showAlertMessage = function(icon_msg, title_msg, sub_title_msg, confirmButtonText = '', target = '',
            redirect_to = '', allowOutsideClick = true) {
            Swal.fire({
                icon: icon_msg,
                title: title_msg,
                text: sub_title_msg,
                target: (target != '' ? document.getElementById(target) : 'body'),
                confirmButtonText: confirmButtonText ? confirmButtonText : 'Ok',
                allowOutsideClick: allowOutsideClick
            }).then((result) => {

                if (result.value) {

                    if (redirect_to) {

                        window.location.href = redirect_to;

                    }
                }
            });
        }

        window.getDetailsFromObjectByKey = function(obj, id, key) {

            for (var data in obj) {
                var e = obj[data];
                if (e[key] == id) {
                    return e;
                }
            }
        };

        window.showSpinner = function(form_selector, size = 'lg', color = 'primary', tag = 'div') {
            if (tag == 'div') {

                //   $(form_selector).append(' <div class="d-flex justify-content-center col-12 mt-4 mb-4" id="spinner-ref"><div class="sk-fold sk-primary"> <div class="sk-fold-cube"></div> <div class="sk-fold-cube"></div> <div class="sk-fold-cube"></div> <div class="sk-fold-cube"></div></div></div>');
                $(form_selector).append(
                    ' <div class="d-flex justify-content-center col-12 mt-4 mb-4" id="spinner-ref"><div class="sk-swing sk-primary"> <div class="sk-swing-dot"></div> <div class="sk-swing-dot"></div> </div></div>'
                );

                // $(form_selector).append(
                //     '<div class="text-center col-12 mt-4 mb-4" id="spinner-ref"><div class="spinner-border spinner-border-' +
                //     size + ' text-' + color +
                //     '" role="status">  <span class="sr-only">Loading...</span> </div></div>' );
            } else {
                $(form_selector).append('<' + tag +
                    ' class="text-center ms-1" id="spinner-ref"><div class="spinner-border spinner-border-' +
                    size + ' text-' + color +
                    '" role="status">  <span class="sr-only">Loading...</span> </div></' + tag + '>');
            }

        }

        window.hideSpinner = function(form_selector) {
            $(form_selector + ' #spinner-ref').remove();
        }

        // Generate random stage code

        window.generateRandomStageCode = function(length) {
            var result = 'st_';
            var characters = '0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() *
                    charactersLength));
            }
            return result;
        }

        // Generate random mat code

        window.generateRandomMaterialCode = function(length) {
            var result = 'raw_mat_';
            var characters = '0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() *
                    charactersLength));
            }
            return result;
        }

        // Date functions

        window.formatDateValue = function(date, format = '') {
            return moment(date).format(format != '' ? format : 'MM/DD/YYYY');
        }

        window.formatDateValueInInput = function(date, format = '') {
            return moment(date).format(format != '' ? format : 'YYYY-MM-DD');
        }
        window.convertUTCToAnotherZone = function(str, userTimezone, format = 'HH:mm') {
            const dateInUserTimezone = moment.utc(str).tz(userTimezone).format(format);
            return dateInUserTimezone;
        }

        window.getAfterDateByDays = function(date = '', days = '') {

            var date = new Date(date);
            var after_date = new Date(date.setDate(date.getDate() + days));
            var formated_after_date = formatDateValueInInput(after_date);
            return formated_after_date;
        }

        window.getBeforeDateByDays = function(date = '', days = '') {

            var date = new Date(date);
            var before_date = new Date(date.setDate(date.getDate() - days));
            var formated_before_date = formatDateValueInInput(before_date);
            return formated_before_date;
        }

        // Get bank account details

        window.getBankAccountDetails = function(selector, id = "", type_term, module_name) {

            var formData = new FormData();
            formData.append('id', id);
            formData.append('type_term', type_term);
            formData.append('module_name', module_name);

            window.getResponseInJsonFromURL(baseUrl + 'get_bank_account_details', formData, (response) => {

                if (response.status == '1') {
                    $('#' + selector).html(response.view);
                } else {
                    $('#' + selector).html(response.view);
                }
            }, (error) => {}, 'POST');

        }

        // Get upload document

        window.getAllDocument = function(selector, id = "", type_term, module_name) {

            var formData = new FormData();
            formData.append('id', id);
            formData.append('type_term', type_term);
            formData.append('module_name', module_name);

            window.getResponseInJsonFromURL(baseUrl + 'get_upload_documents', formData, (response) => {

                if (response.status == '1') {
                    $('#' + selector).html(response.view);
                } else {
                    $('#' + selector).html("");
                }
            }, (error) => {}, 'POST');

        }

        // Preview uploaded file

        window.previewFile = function(url) {

            return window.open(url, '_blank');
        };


        window.formatFloatValue = function(value, decimal_point = '2') {

            return parseFloat(value).toFixed(decimal_point);

        }

        window.processExceptions = function(e) {
            showErrorMessage(e.message);
        };

    }(window));


    // For select file

    $('body').on('change', 'input[type=file]', function(e) {

        if (this.files && this.files[0]) {

            var selector = $(this).attr('id');
            var allowedExtensions = /(\jpg|\jpeg|\png|\gif|\JPG|\svg)$/i;
            var ext = this.files[0].type.split('/').pop();
            var fileName = e.target.files[0].name;

            fileName = fileName.replace(/ /g, "_").replace(/\./g, '_');

            $('#' + selector + '_preview').html('');


            if (allowedExtensions.exec(ext)) {

                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + selector + '_preview').append('<img class="img-fluid rounded-2" src="' + e
                        .target.result +
                        '" data-filename="' + fileName + '">');
                }
                reader.readAsDataURL(this.files[0]);

            } else {
                $('#' + selector + '_preview').append(
                    '<img class="img-fluid rounded-2" src="{{ asset('/no_image.jpg') }}">');
            }

        }

        // else{
        //     var selector = $(this).attr('id');
        //     $('#'+selector+'_preview').html('');
        //     $('#'+selector+'_preview').append('<img class="img-fluid" src="{{ asset('/no_image.jpg') }}">');
        // }


    });

    function imagePreview() {

        $('body').on('change', 'input[type=file]', function(e) {

            if (this.files && this.files[0]) {

                var selector = $(this).attr('id');
                var allowedExtensions = /(\jpg|\jpeg|\png|\gif|\JPG|\svg)$/i;
                var ext = this.files[0].type.split('/').pop();
                var fileName = e.target.files[0].name;

                fileName = fileName.replace(/ /g, "_").replace(/\./g, '_');

                $('#' + selector + '_preview').html('');


                if (allowedExtensions.exec(ext)) {

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#' + selector + '_preview').append('<img class="img-fluid" src="' + e.target
                            .result +
                            '" data-filename="' + fileName + '">');
                    }
                    reader.readAsDataURL(this.files[0]);

                } else {

                    $('#' + selector + '_preview').append(
                        '<img class="img-fluid" src="{{ asset('/no_image.jpg') }}">');
                }

            }

        });

    }

    // For address inputs

    $("#city_id").select2({

        width: "100%",
        ajax: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: baseUrl + 'city_list',
            dataType: 'json',
            method: "post",
            delay: 250,

            data: function(params) {
                return {
                    city: params.term, // search term
                    page: params.page
                };
            },

            processResults: function(data) {

                var retVal = [];
                for (var i = 0; i < data.data.length; i++) {
                    var lineObj = {
                        id: data.data[i]['id'],
                        text: data.data[i]['city_name'],
                        state: data.data[i]['state_code'],
                    }
                    retVal.push(lineObj);
                }
                return {
                    results: retVal
                };

            },

            cache: true
        },

        placeholder: 'Search for a city',
        minimumInputLength: 1,
        language: {
            inputTooShort: function(args) {
                return "";
            }
        }
    });

    $('.city').on('select2:select', function(e) {

        var state_selector = $(this).data('state_selector');
        var state_code_selector = $(this).data('state_code_selector');
        var country_selector = $(this).data('country_selector');

        show_state_list($(this).val(), e.params.data.state, 'select[name="' + state_selector + '"]',
            'select[name="' + state_code_selector + '"]', 'select[name="' + country_selector + '"]');

    });

    function show_state_list(city_id, selected_id = '', state_selector, state_code_selector, country_selector) {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "POST",
            url: baseUrl + 'city/state/' + city_id,
            processData: false,
            contentType: false,

            success: function(response) {

                if (response.data.length > 0) {

                    var stateSelector = $(state_selector);

                    $(stateSelector).empty();
                    $(state_code_selector).val('');
                    $(country_selector).val('');

                    if (response.data.length > 0) {

                        $(stateSelector).prepend("<option value=''>Please Select</option>");

                        $.each(response.data, function(index, value) {

                            $(stateSelector).append($("<option></option>")
                                .attr("value", value.id)
                                .attr("data-country", value.country_id)
                                .attr("data-gst_state_code", value.gst_state_code)
                                .text(value.name));

                        });

                        if (selected_id != '') {

                            $(state_selector + " option[value='" + selected_id + "']").prop("selected",
                                "selected");

                            var state_code = $(state_selector).find(':selected').attr(
                                'data-gst_state_code');
                            $(state_code_selector + " option[value='" + state_code + "']").prop("selected",
                                "selected");

                            var country_id = $(state_selector).find(':selected').attr('data-country');
                            $(country_selector + " option[value='" + country_id + "']").prop("selected",
                                "selected");

                        }

                    }
                }

            },
            error: function(jqXHR, textStatus, ex) {

            }
        });

    }

    function status_term_badge(status_term) {

        var html = '';

        if (status_term != null && status_term != '') {

            if (status_term == "in progress") {

                html += "<span class='badge rounded-pill bg-label-warning'>In Progress</span>";

            } else if (status_term == "in_progress") {

                html += "<span class='badge rounded-pill bg-label-warning'>In Progress</span>";

            } else if (status_term == "pending") {

                html += "<span class='badge rounded-pill bg-label-warning'>Pending</span>";

            } else if (status_term == "confirmed") {

                html += "<span class='badge rounded-pill bg-label-info'>Confirmed</span>";

            } else if (status_term == "completed") {
                html += "<span class='badge rounded-pill bg-label-success' >Completed</span>";

            } else if (status_term == "cancelled" || status_term == "canceled") {

                html += "<span class='badge rounded-pill bg-label-danger'>Cancelled</span>";

            } else if (status_term == "reject") {

                html += "<span class='badge rounded-pill bg-label-danger'>Rejected</span>";

            } else if (status_term == "accept") {

                html += "<span class='badge rounded-pill bg-label-success'>Approved</span>";
            } else if (status_term == "available") {

                html += "<span class='badge rounded-pill bg-label-info'>Available</span>";
            } else if (status_term == "booked") {

                html += "<span class='badge rounded-pill bg-label-success'>Booked</span>";
            } else if (status_term == "succeeded") {

                html += "<span class='badge rounded-pill bg-label-success'>Successful</span>";
            } else if (status_term == "processing") {

                html += "<span class='badge rounded-pill bg-label-warning'>processing</span>";

            } else if (status_term == "{{ config('custom.claim_request_status.approved') }}") {

                html += "<span class='badge rounded-pill bg-label-success'>Approved</span>";

            } else if (status_term == "{{ config('custom.claim_request_status.rejected') }}") {

                html += "<span class='badge rounded-pill bg-label-danger'>Rejected</span>";
            } else if (status_term == "accept") {

                html += "<span class='badge rounded-pill bg-label-success'>Paid</span>";
            } else if (status_term == "in_transit") {

                html += "<span class='badge rounded-pill bg-label-warning'>In Transit</span>";
            } else if (status_term == "failed") {

                html += "<span class='badge rounded-pill bg-label-danger'>Failed</span>";

            } else if (status_term == "Standard") {

                html +=
                    "<span class='text-danger m-2'>Standard</span><span class='badge badge-center rounded-pill bg-dark'>S</span>";

            } else {
                html = status_term;
            }

        }

        return html;

    }


    function order_status_badge(status_term) {

        var html = status_term;

        if (status_term != null && status_term != '') {

            if (status_term == "{{ config('custom.order_item_status.booked') }}") {

                html = "<span class='badge bg-success'>Booked</span>";

            } else if (status_term == "{{ config('custom.order_item_status.pending') }}") {

                html = "<span class='badge bg-warning'>Pending</span>";

            } else if (status_term == "{{ config('custom.order_item_status.completed') }}") {

                html = "<span class='badge bg-secondary'>completed</span>";

            } else if (status_term == "{{ config('custom.order_item_status.cancelled') }}") {

                html = "<span class='badge bg-danger'>cancelled</span>";

            }
        }

        return html;

    }

    function subscription_status_badge(status_term) {

        var html = status_term;
        console.log("status_term  ", status_term);

        if (status_term != null && status_term != '') {

            if (status_term == "{{ config('custom.subscription.status.active') }}") {

                html = "<span class='badge rounded-pill bg-success'>Active</span>";

            } else if (status_term == "{{ config('custom.subscription.status.cancelled') }}") {

                html = "<span class='badge rounded-pill bg-danger'>Cancelled</span>";

            } else if (status_term == "{{ config('custom.subscription.status.pending') }}") {

                html = "<span class='badge rounded-pill bg-secondary'>Pending</span>";

            } else if (status_term == "{{ config('custom.subscription.status.schedule') }}") {

                html = "<span class='badge rounded-pill bg-warning'>Schedule</span>";

            } else if (status_term == "{{ config('custom.subscription.status.expired') }}") {

                html = "<span class='badge rounded-pill bg-danger'>Expired</span>";

            }
        }

        return html;

    }

    function paymentby_status_badge(status_term) {

        var html = status_term;

        if (status_term != null && status_term != '') {

            if (status_term == "{{ config('custom.payment_via.wallet') }}") {

                html = "<span class='badge bg-success'>Wallet</span>";

            } else if (status_term == "{{ config('custom.payment_via.payment_method') }}") {

                html = "<span class='badge bg-success'>Online</span>";

            }
        }

        return html;

    }

    function social_groups_status_badge(status) {
        var html = '';
        if (status) {
            html = "<span class='badge bg-success'>Active</span>";
        } else {
            html = "<span class='badge bg-danger'>Inactive</span>";
        }
        return html;
    }


    function group_member_status_term_badge(status_term) {
        var html = '';
        if (status_term != null && status_term != '') {

            if (status_term == "accepted") {
                html += "<span class='badge rounded-pill bg-label-success'>Accepted</span>";

            } else if (status_term == "pending") {
                html += "<span class='badge rounded-pill bg-label-warning'>Pending</span>";

            } else {
                html = status_term;
            }

        }
        return html;
    }

    function group_member_ownership(ownership) {
        var html = '';
        if (ownership != null && ownership != '') {

            if (ownership == "group_admin") {
                html += "Admin";

            } else if (ownership == "group_member") {
                html += "<span>Member</span>";

            } else {
                html = ownership;
            }

        }
        return html;
    }

    function showTextOnHoverDataTbl(data) {
        var maxCharacters = 30;
        if (data && data.length > maxCharacters) {
            return `<span title="${data}">${data.substr(0, maxCharacters)}...</span>`;
        } else {
            return data;
        }
    }

    function convertUtcTimeToLocalTime(time, format = '') {
        return moment.utc(time, 'HH:mm').local().format(format != '' ? format : 'hh:mm A');
    }

    function convertUTCToLocalTime(utcTimeString) {
        var utcDate = new Date(utcTimeString + ' UTC');
        var options = {
            hour: 'numeric',
            minute: 'numeric',
            hour12: true
        };
        var localTime = utcDate.toLocaleTimeString(undefined, options);
        return localTime;
    }

    function convertUtcDateTimeToLocalDateTime(utcTime, format = '') {
        return moment.utc(utcTime).local().format(format != '' ? format : 'MM/DD/YYYY HH:mm');

    }

    function convertLocalDateTimeToUtcDateTime(localTime, format = '') {
        return moment(localTime).utc().format(format !== '' ? format : 'YYYY-MM-DD HH:mm:ss');
    }

    // function convertLocalDateTimeToUtcDateTime(date, format = '') {
    //     console.log("===>>> date ==> ",date);
    //     var date = moment(date).utc().format(format != '' ? format : 'YYYY-MM-DD HH:mm:ss');
    //     console.log("==>>>> ",date);
    //     return date
    // }

    function showUtcToLocalStartEndTime(row) {
        var start_time = row.start_time ? convertUTCToLocalTime(row.start_time, 'hh:mm A') : '';
        var end_time = row.end_time ? convertUTCToLocalTime(row.end_time, 'hh:mm A') : '';
        return start_time + ' To ' + end_time;
    }

    function showUtcToLocalDateTime(row) {
        var date = row.booking_date ? convertUtcDateTimeToLocalDateTime(row.booking_date, 'DD MMM, YYYY') : '';
        console.log(date);
        return date;
    }

    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function full_address(row) {
        var address = row.address;
        var city = row.city;
        var state = row.state;
        var country = row.country;
        var zipcode = row.zipcode;

        $full_address = '-';
        if (address && city) {
            $full_address = address + ', ' + city + ', ' + state + ' , ' + country + '-' + zipcode;
        }
        return $full_address;
    }

    function phone_number_format(selector) {

        var cleave = new Cleave(selector, {
            phone: true,
            phoneRegionCode: '{{ config('custom.default_country_shortcode') }}'
        });

    }

    $('body').on('keyup', '.dataTables_filter input[type="search"]', function() {

        // $('.dataTables_filter input[type="search"]').on('keyup', function(event) {

        var searchValue = $(this).val();

        // if (searchValue.includes('  ')) {
        //     $(this).val('');
        // }

        if (searchValue == ' ') {
            $(this).val('');
        }
    });

    // Function to add minutes to a given time
    function addMinutesToTime(time, minutes) {
        var [hours, mins] = time.split(':').map(Number);

        var totalMins = hours * 60 + mins + minutes;
        var newHours = Math.floor(totalMins / 60);
        var newMins = totalMins % 60;

        return `${String(newHours).padStart(2, '0')}:${String(newMins).padStart(2, '0')}`;
    }

    function convertReservationTypeToArray(data) {

        const reservationTypeConfig = @json(config('custom.reservation_type'));
        console.log("reservationTypeConfig   ", reservationTypeConfig);
        const valueArray = data.split(',');
        let newArray = [];

        valueArray.forEach(value => {
            // console.log(value);

            let val = reservationTypeConfig[value];

            newArray.push(val);
        });

        const commaSeparatedString = newArray.join(', ');

        // console.log(commaSeparatedString);
        return commaSeparatedString;
    }

    function calculateAppSettingDiscount(price, discount_type, discount_value) {

        if (price && discount_type && discount_value) {

            var dis_price;

            if (discount_type == "{{ config('custom.discount_type.percentage') }}") {

                var disc_amt = (price * discount_value / 100);

                dis_price = price + disc_amt;

            } else if (discount_type == "{{ config('custom.discount_type.value') }}") {

                dis_price = price + discount_value;
            } else {
                dis_price = price;
            }

            return dis_price;
        } else {
            return price;
        }

    }

</script>
