
<script>
    @if(Session::has('message') && session('status') == '1')
        toastr.options =
        {
            "closeButton" : true,
        }
        toastr.success("{{ session('message') }}");

    @elseif(Session::has('message') )

        toastr.options =
        {

            "closeButton" : true,
        }
        toastr.success("{{ session('message') }}");
    @endif


    @if(Session::has('error') && session('status') == '0')
        toastr.options =
        {
            "closeButton" : true,
        }
        toastr.error("{{ session('message') }}");
    @elseif(Session::has('error'))
        toastr.options =
        {
            "closeButton" : true,
        }
        toastr.error("{{ session('error') }}");
    @endif

    @if(Session::has('info'))
        toastr.options =
        {
            "closeButton" : true,
        }
        toastr.info("{{ session('info') }}");
    @endif

    @if(Session::has('warning'))
        toastr.options =
        {
            "closeButton" : true,
        }
        toastr.warning("{{ session('warning') }}");
    @endif


    @if (Auth::check())

        function getNotifications(){

            window.getResponseInJsonFromURL(baseUrl+'notification/get_unread_all_notifications', '', (response) => {

                processNotificationResponse(response);

            }, (error) => { console.log(error); }, 'POST');

        }

        function processNotificationResponse(response){

            $('#notify-badge-count').hide();
            $('#notify-badge-count-title').hide();

            $('#unread-notifications-container').html('');
            $('#notify-badge-count-title').html('Notifications');
            if(response.total > 0){

                $('#notify-badge-count').html(response.total);
                $('#notify-badge-count-title').html(response.total+' New Notifications');
                $('#notify-badge-count').show();


                var html = '';
                html += ' <ul class="list-group list-group-flush">';

                  $.each(response.list, function (i, value) {

                      value.data = JSON.parse(value.data);

                    html += '   <li class="list-group-item list-group-item-action dropdown-notifications-item notify-action" data-notify-id="'+(value.notify_id ? value.notify_id : '')+'">';
                    html += '     <div class="d-flex">';
                    html += '       <div class="flex-grow-1">';
                    html += '         <h6 class="mb-1">'+(value.notification_title ? value.notification_title : '')+'</h6>';
                    html += '         <p class="mb-0">'+(value.notification_description ? value.notification_description : '')+'</p>';
                    html += '         <small class="text-muted">'+formatDateValue(value.created_at)+'</small>';
                    html += '       </div>';
                    html += '       <div class="flex-shrink-0 dropdown-notifications-actions">';
                    html += '         <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>';
                    html += '       </div>';
                    html += '     </div>';
                    html += '   </li>';


                  });

                html += ' </ul>';

                $('#unread-notifications-container').append(html);

            }else{
                $('#unread-notifications-container').append('<h6 class="text-center text-muted mt-2 mb-2">No Notifications!</h6>');
            }
            $('#notify-badge-count-title').show();
        }

        $('body').on('click', '.notify-action', function(e) {

            e.preventDefault();

            var notify_id = $(this).attr('data-notify-id');

            var formData = new FormData();
            formData.append('notify_id',notify_id);

            getResponseInJsonFromURL(baseUrl+'notification/selected_notification_read', formData, (response) => {

                window.location.reload();

            }, (error) => { console.log(error); });


        });

        getNotifications();

        setInterval(getNotifications, 10000);   // 10 seconds(miliseconds)

    @endif

</script>
