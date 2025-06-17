<script>
    window.onload = function() {
        'use strict';

        var ProjectList = window.ProjectList || {};
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
                ProjectList.processExceptions(e);
            }
        };

        const our_teams = @json($our_teams);

        ProjectList.initEvents = function() {
            
            
            // Trigger file input when box clicked  
            $('#logoUploadBox').click(() => $('#logoInput').click());
            $('#bannerUploadBox').click(() => $('#bannerInput').click());

            // Common preview handler
            function readAndPreview(input, previewSelector, boxSelector) {
                const file = input.files?.[0];
                if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $(previewSelector).attr('src', e.target.result).show();
                    $(boxSelector).find('.upload-placeholder').hide();
                };
                reader.readAsDataURL(file);
                }
            }

            // Bind change events
            $('#logoInput').on('change', function () {
                readAndPreview(this, '#logoPreview', '#logoUploadBox');
            });

            $('#bannerInput').on('change', function () {
                readAndPreview(this, '#bannerPreview', '#bannerUploadBox');
            });        

            // ckeditor load
            CKEDITOR.replace('short_description', {
                height: 150,
            });

            // date picker
            $('.flatpickr').flatpickr({
                defaultDate: new Date()
            });

            $('#open_project_model').on('click', function () { 
                $('#newProjectModal').modal('show'); 
            });

            // level tab select
                    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                        const categoryName = $(e.target).data('category'); // get actual category
                        $('#level_selected_tab').val(categoryName);
                    });

                    // Set default selected category on page load
                    const $defaultActive = $('a[data-bs-toggle="pill"].active');
                    if ($defaultActive.length) {
                        $('#level_selected_tab').val($defaultActive.data('category'));
                    }
            //level tab select

           //  teams start

            function updateDropdowns($container) {
                if (!$container || !$container.length) {
                    console.warn("updateDropdowns: invalid container", $container);
                    return;
                }

                const membersJson = $container.attr('data-members');

                if (!membersJson) {
                    console.warn("updateDropdowns: missing data-members attribute");
                    return;
                }

                let memberList;
                try {
                    memberList = JSON.parse(membersJson);
                } catch (e) {
                    console.error("updateDropdowns: invalid JSON in data-members", membersJson);
                    return;
                }

                const $selects = $container.find('.team-member-select');
                const selected = [];

                $selects.each(function () {
                    const val = $(this).val();
                    if (val) selected.push(val);
                });

                $selects.each(function () {
                    const currentVal = $(this).val();
                    const $select = $(this);

                    $select.empty().append('<option value="">Select Member</option>');

                    memberList.forEach(member => {
                        const idStr = member.id.toString();
                        const isUsed = selected.includes(idStr) && idStr !== currentVal;
                        if (!isUsed) {
                            const selectedAttr = idStr === currentVal ? 'selected' : '';
                            $select.append(`<option value="${member.id}" ${selectedAttr}>${member.name}</option>`);
                        }
                    });
                });
            }


            $('.team-repeater').each(function () {
                updateDropdowns($(this));
            });

            // Handle Add Button
            $('.add-btn').on('click', function () {
            const target = $(this).data('target');
            const $container = $(target);
            const $firstRow = $container.find('.repeater-item').first();
            const $newRow = $firstRow.clone();

            $newRow.find('select').val('');
            $newRow.find('input').val('');
            $container.append($newRow);

            updateDropdowns($container);
            });

            // Handle Remove Button
            $(document).on('click', '.remove-btn', function () {
            const $container = $(this).closest('.team-repeater');
            const $items = $container.find('.repeater-item');
            if ($items.length > 1) {
                $(this).closest('.repeater-item').remove();
                updateDropdowns($container);
            }
            });

            // Handle Change
            $(document).on('change', '.team-member-select', function () {
            const $container = $(this).closest('.team-repeater');
            updateDropdowns($container);
            });

            // Initial dropdown population
            $(document).ready(function () {
                updateDropdowns();
            });

           // teams end

           // MIA Agent start
            $('.mia-checkbox').on('change', function () {
                let selected = [];
                $('.mia-checkbox:checked').each(function () {
                    selected.push($(this).val());
                });

                console.log('Selected Agents:', selected);
                $('#selectedMiaAgentsInput').val(selected.join(','));
            });
           // MIA Agent end
      

            $(document).on('submit', '#store_project_adta', function(e) {
                    e.preventDefault();

                    const form = $(this)[0];
                    const formData = new FormData(form);

                    $.ajax({
                        url: "{{ route('project.store') }}", 
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            
                            $('#newProjectModal').modal('hide');
                            $('#store_project_adta').prop('disabled', true).text('Saving...');
                        },
                        success: function(response) {
                            
                            $('#newProjectModal').modal('hide');
                            $('#store_project_adta').prop('disabled', false).text('Save');

                            if (response.status === 1) {
                                $('#newProjectModal').modal('hide');
                                showSuccessMessage(response.message); // Optional
                                location.reload(); 
                            } else {
                                showErrorMessage(response.message); // Optional
                            }
                        },
                        error: function(xhr) {
                            $('#store_project_adta').prop('disabled', false).text('Save');
                            showErrorMessage("Something went wrong. Please try again.");
                        }
                    });
                });



        }

       

        ProjectList.processExceptions = function(e) {
            showErrorMessage(e);
        };

        ProjectList.initEvents();
    };
</script>
