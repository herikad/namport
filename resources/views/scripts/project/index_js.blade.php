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

        var our_teams = @json($our_teams);
        var STORAGE_DRIVER = "{{ config('filesystems.default') }}";
        var AWS_URL = "{{ env('AWS_URL') }}";
        var APP_URL = "{{ url('/') }}";
        

        ProjectList.ProjectTbls = '';
        ProjectList.allbranchDetails = [];
        ProjectList.filterOption = false;

        ProjectList.initEvents = function() {

            ProjectList.ProjectTbls = $('#project_list_tbl').DataTable({
                //   responsive: true,
                processing: true,
                serverSide: true,
                "ajax": {
                    "type": "POST",
                    "url": baseUrl + 'project/project_json_list',
                    "data": function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content'),
                            d.filter = ProjectList.filterOption,
                            d.is_coach = 0
                    },
                    "dataSrc": function(json) {
                        ProjectList.allbranchDetails = json.data;
                        console.log(json.data);

                        return json.data;
                    }
                },
                "columns": [{
                        data: "project_id",
                        "render": function(data, type, row) {

                            return row.project_id;
                        }
                    },
                    {
                        data: "project_id",
                        "render": function(data, type, row, meta) {

                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'company_name'
                    },
                    {
                        data: 'projectname'
                    },

                    {
                        data: "project_id",
                        render: function(data, type, row) {

                            var mia_agent = row.mia_agent
                                        .map(entry => {
                                            const agent = entry.mia_agent;
                                            return agent ? {
                                                name: agent.nameofagent,
                                                image: agent.agentpersonafile
                                            } : null;
                                        })
                                        .filter(Boolean); 
                            return renderAvatarGroup(mia_agent);
                        }
                    },
                    {
                        data: "project_id",
                        render: function(data, type, row) {
                            var our_team = row.our_team
                                        .map(entry => {
                                            var employee = entry.employee;
                                            return employee ? {
                                                name: employee.display_name,
                                                image: getImageUrl(employee.profilepicture)
                                            } : null;
                                        })
                                        .filter(Boolean); 
                            return renderAvatarGroup(our_team);
                        }
                    },
                    {
                        data: "project_id",
                        render: function(data, type, row) {
                            var our_client = row.our_client
                                        .map(entry => {
                                            var client = entry.client_contact;
                                            return client ? {
                                                name: client.first_name +' '+ client.last_name,
                                                image: getImageUrl('images/client_contact/profile/' + client.profile_pic)
                                            } : null;
                                        })
                                        .filter(Boolean); 
                            return renderAvatarGroup(our_client);
                        }
                    },
                   
                    {
                        data: 'status_term',
                        "render": function(data, type, row, meta) {
                            return status_term_badge(row.status_term);
                        }
                    },
                    {
                        data: 'progress'
                    },
                    {
                        data: 'startdate',
                        "render": function(data, type, row, meta) {
                            return formatDateValueInInput(row.startdate);
                        }
                    },
                    {
                        data: 'enddate',
                        "render": function(data, type, row, meta) {
                            return formatDateValueInInput(row.enddate);
                        }
                    },
                    {
                        data: "project_id",
                        render: function(data, type, row) {
                            var html = '';
                            html += ' <a title="Edit" href="' + baseUrl +
                                'project/edit/' + row.enc_project_id +
                                '" class="text-end"><i class="bx bx-edit theme-text-secondary bx-sm mr-50"  ></i></a>';
                            return html;
                        }
                    },
                ],
                "order": [
                    [0, 'desc']
                ],
                'columnDefs': [{
                        'targets': [0, 4,5,6,11], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    },
                    {
                        "visible": false,
                        "targets": [0]
                    }
                ]
            });


            //////
            function getImageUrl(path) {
                if (!path) return APP_URL + '/no_image.jpg';
                if (STORAGE_DRIVER === 's3') {
                    return AWS_URL + path;
                }
                return APP_URL + '/' + path;
            }

            ////
            function renderAvatarGroup(members) {
                
                if (!members || members.length === 0) return '';

                let html = `<div class="avatar-group">`;
                let limit = 2;

                members.slice(0, limit).forEach(member => {
                    html += `
                        <img src="${member.image}" title="${member.name}" class="rounded-circle avatar-xs" />
                    `;
                });

                if (members.length > limit) {
                    html += `
                        <span class="rounded-circle avatar-xs bg-secondary text-white d-flex align-items-center justify-content-center">
                            +${members.length - limit}
                        </span>
                    `;
                }

                html += `</div>`;
                return html;
            }



            /////////


            // Project model js
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

                        $('#projectname').removeClass('is-invalid');
                        $('#projectname-error').hide();

                        $('#projectmanager_id').removeClass('is-invalid');
                        $('#projectmanager-error').hide();

                        let projectName = $('input[name="projectname"]').val().trim();
                        let projectManager = $('select[name="projectmanager_id"]').val()

                        let hasError = false;

                        if (projectName === "") {
                            $('#projectname').addClass('is-invalid');
                            $('#projectname-error').show();
                            hasError = true;
                        }

                        if (!projectManager || projectManager === "") {
                            $('#projectmanager_id').addClass('is-invalid');
                            $('#projectmanager-error').show();
                            hasError = true;
                        }

                        if (hasError) {
                            // Switch to the correct tab if needed
                            let tabTrigger = new bootstrap.Tab(document.querySelector('button[data-bs-target="#basicInfo"]'));
                            tabTrigger.show();

                            return;
                        }

                        const form = $(this)[0];
                        const formData = new FormData(form);

                        $.ajax({
                            url: "{{ route('project.store') }}", 
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                showLoadingDialog();
                            },
                            success: function(response) {
                                
                                hideLoadingDialog();
                                if (response.status === 1) {
                                    $('#newProjectModal').modal('hide');
                                    showSuccessMessage(response.message); // Optional
                                    location.reload(); 
                                } else {
                                    showErrorMessage(response.message); // Optional
                                }
                            },
                            error: function(xhr) {
                                hideLoadingDialog();
                                showErrorMessage("Something went wrong. Please try again.");
                            }
                        });
                });
            // Project model code

        }

       

        ProjectList.processExceptions = function(e) {
            showErrorMessage(e);
        };

        ProjectList.initEvents();
    };
</script>
