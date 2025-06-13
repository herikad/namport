<script>
    window.onload = function() {

        'use strict';
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

                Notification.processExceptions(e);

            }

        };

        Notification.processExceptions = function(e) {
            showErrorMessage(e);
        };

        Notification.notificationsPageIndex = 1;
        Notification.notificationsTotalPage = 1;
        Notification.isActiveAjax = false;
        Notification.user_type = '{{ \Session::get('login_type') }}';

        Notification.initEvents = function() {

            Notification.getAllNotifications();

        }


        Notification.getAllNotifications = function() {

            if (Notification.notificationsPageIndex <= Notification.notificationsTotalPage && Notification
                .isActiveAjax == false) {

                var formData = new FormData();
                formData.append('page', Notification.notificationsPageIndex);

                showSpinner('#notifications-container');
                Notification.isActiveAjax = true;
                getResponseInJsonFromURL(baseUrl + 'notification/get_all_notifications_json', formData,
                    Notification.processNotifications, Notification.processExceptions);
            }

        }

        Notification.processNotifications = function(response) {

            if (response.status == 1) {

                var html = '';

                Notification.notificationsTotalPage = response.total;

                if (response.data.list.length > 0) {

                    $.each(response.data.list, function(i, value) {

                        value.data = JSON.parse(value.data);

                        html += '<li class="list-group-item">';
                        html += '   <div class="d-flex justify-content-between align-items-center">';
                        html += '       <div class="sales-item-name">';
                        html += '           <p class="mb-0"><b>' + value.notification_title +
                            '</b></p>';
                        html += '           <p class="mb-0">' + value.notification_description + '</p>';
                        html += '           <small class="text-muted">' + formatDateValue(value
                            .created_at) + '</small>';
                        html += '       </div>';

                        if (value.data && value.data.hasOwnProperty("association_id")) {
                            if (value.data.hasOwnProperty("send_association_type") && value.data.send_association_type == "admin") {

                                html += '<a href="' + baseUrl + 'club-manager/edit/' + value.data
                                    .association_id + '" class="btn btn-primary btn-sm">View</a>';
                            } else if (value.data.hasOwnProperty("send_association_type") && value.data.send_association_type == "club_admin") {

                                html += '<a href="' + baseUrl + 'club/create/' + value.data.association_id + '/' + value.data
                              .club_id + '" class="btn btn-primary btn-sm">View</a>';
                            }
                        }

                        html += '   </div>';
                        html += '</li>';

                    });

                } else {
                    if (Notification.notificationsTotalPage == Notification.notificationsPageIndex &&
                        Notification.notificationsPageIndex == 1) {
                        html += '<h3 class="text-center text-muted mt-4">No Notifications!</h3>';
                    } else {
                        html += '<h3 class="text-center text-muted mt-4">No Notifications!</h3>';
                    }

                }
                hideSpinner('#notifications-container');
                $("#notifications-container").append(html);
                Notification.notificationsPageIndex++;

            } else {
                hideSpinner('#notifications-container');
                showErrorMessage(response.error);
            }
            Notification.isActiveAjax = false;

        }

        $(window).scroll(function() {

            if ($(document).height() <= $(window).scrollTop() + $(window).height()) {
                Notification.getAllNotifications(Notification.notificationsPageIndex);
            }
        });


        Notification.processExceptions = function(e) {
            hideLoadingDialog();
            showErrorMessage(e);
        };
        Notification.initEvents();

    };

    var Notification = window.Notification || {};
</script>
