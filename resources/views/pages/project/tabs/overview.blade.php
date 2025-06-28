
  <style>
    .header-banner {
     
      background-size: cover;
      height: 180px;
      position: relative;
    }
    .profile-circle {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background-color: #e2e6ea;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: #6c757d;
      border: 5px solid white;
      position: absolute;
      bottom: -40px;
      left: 30px;
    }
    .edit-icon {
      position: absolute;
      right: 20px;
      top: 20px;
      font-size: 1.2rem;
      color: #6c757d;
      cursor: pointer;
    }
    .info-label {
      font-size: 0.875rem;
      color: #6c757d;
    }
    .info-value {
      font-weight: 600;
    }
      .team-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid transparent;
      position: relative;
    }
    .team-item.active .team-avatar {
      border-color: #28a745;
    }
    .checkmark {
      position: absolute;
      bottom: -4px;
      right: -4px;
      background-color: #28a745;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 12px;
      color: white;
    }
    .team-member {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 15px;
      position: relative;
    }
    .tab-content {
      padding-top: 15px;
    }
  </style>

    <div class="container-fluid p-0">
    <!-- Header Banner -->
        @php
            $bannerImagePath = !empty($project_details->banner_image) && config('filesystems.default') == 's3'
                ? env('AWS_URL') . 'images/project/banner/' . $project_details->banner_image
                : (!empty($project_details->banner_image)
                    ? url('images/project/banner/' . $project_details->banner_image)
                    : 'https://demos.pixinvent.com/frest-html-laravel-admin-template/demo/assets/img/pages/profile-banner.png');

            $profileImage = !empty($project_details->banner_image) && config('filesystems.default') == 's3'
                ? env('AWS_URL') . 'images/project/logo/' . $project_details->banner_image
                : (!empty($project_details->banner_image)
                    ? url('images/project/logo/' . $project_details->banner_image)
                    : '/no_image.jpg');
        @endphp
        <div class="header-banner" style="background-image: url('{{ $bannerImagePath }}');">

        @if(!empty($profileImage))
        <div class="profile-circle p-0 overflow-hidden">
            <img src="{{ $profileImage }}" alt="Profile" class="w-100 h-100" style="object-fit: cover;">
        </div>
        @else
        <div class="profile-circle"><i class="bi bi-person"></i></div>
        @endif
        <i class="bi bi-pencil-square edit-icon"></i>
    </div>

    <!-- Content -->
    <div class="container bg-white pt-5 pb-4">
        <!-- Project Info -->
        <h3 class="fw-bold">{{ isset($project_details->projectname) ? $project_details->projectname : '-'}}</h3>
        <div class="row mb-3">
        <div class="col-md-2"><div class="info-label">Project Lead</div><div class="info-value">{{ isset($project_manager->display_name) ? $project_manager->display_name : '-'}}</div></div>
        <div class="col-md-2"><div class="info-label">Departments Involved</div><div class="info-value">{{isset($departments) ? $departments : ''}}</div></div>
        <div class="col-md-2"><div class="info-label">Status</div><div class="info-value">{{ config('custom.profile_status_term')[$project_details->status_term] ?? '-' }}</div></div>
        <div class="col-md-2"><div class="info-label">Completion</div><div class="info-value">{{ isset($project_details->progress) ? $project_details->progress : '0'}}%</div></div>
        <div class="col-md-2"><div class="info-label">Start Date</div><div class="info-value">{{ isset($project_details->startdate) ? \Carbon\Carbon::parse($project_details->startdate)->format('d/m/Y') : '-'}}</div></div>
        <div class="col-md-2"><div class="info-label">End Date</div><div class="info-value">{{ isset($project_details->enddate) ? \Carbon\Carbon::parse($project_details->enddate)->format('d/m/Y') : '-'}}</div></div>
        </div>

        <!-- Objective -->
        <h6 class="fw-bold">Objective</h6>
        <p>
        {!! $project_details->shortdetail !!}
        </p>
    </div>
    </div>
    <!-- Divider -->
    <div class="border-divider"></div>

    <div class="container py-4">
        <div class="row">
            <!-- Key Metrics + Milestones -->
            <div class="col-md-6">
            <h5 class="fw-bold mb-3">Key Metrics</h5>
            <div class="row text-center mb-4">
                <div class="col-4 mb-3">
                <div class="metrics-value">00</div>
                <div class="metrics-label">Total Categories</div>
                </div>
                <div class="col-4 mb-3">
                <div class="metrics-value">00</div>
                <div class="metrics-label">Process Groups</div>
                </div>
                <div class="col-4 mb-3">
                <div class="metrics-value">00</div>
                <div class="metrics-label">Processes Identified</div>
                </div>
                <div class="col-4 mb-3">
                <div class="metrics-value">00</div>
                <div class="metrics-label">Activities Mapped</div>
                </div>
                <div class="col-4 mb-3">
                <div class="metrics-value">00</div>
                <div class="metrics-label">Documents Collected</div>
                </div>
                <div class="col-4 mb-3">
                <div class="metrics-value">00</div>
                <div class="metrics-label">Pending Tasks</div>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Milestone Highlights</h5>
            <div class="milestone-row"><span>Kick-off Workshop :</span><span class="fw-semibold"> Completed</span></div>
            <div class="milestone-row"><span>Tier Classification :</span><span class="fw-semibold"> Finalized</span></div>
            <div class="milestone-row"><span>SharePoint Repository Setup :</span><span class="fw-semibold"> In Progress</span></div>
            <div class="milestone-row"><span>EXCO Review Scheduled :</span><span class="fw-semibold"> 15/06/2025</span></div>
            <div class="milestone-row"><span>Final Documentation :</span><span class="fw-semibold"> Due 25/06/2025</span></div>
            </div>

            <!-- Team Section -->
            <div class="col-md-6">
            <ul class="nav nav-tabs team-tabs mb-3" id="teamTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#our-team">Our Team</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#client-team">Client Team</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#mia-agents">MIA Agents</button></li>
                <li class="ms-auto"><i class="bi bi-plus-circle fs-5 text-primary" style="cursor: pointer;"></i></li>
            </ul>

            <div class="tab-content">
                <!-- Our Team Tab -->
                <div class="tab-pane fade show active" id="our-team">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                          <h5 class="mb-0">Our Team</h5>
                          <div class="d-flex gap-2">
                            
                            <a class="btn btn-outline-primary btn-sm" id="addTeamMemberBtn" title="Add Team Member"
                                data-bs-toggle="modal" data-bs-target="#teamMemberModal">
                                <i class="bx bx-plus-circle"></i>
                            </a>

                          </div>
                      </div>
                    <div class="row">
                        @php
                            $our_team_ids = collect($exsting_teams['our_team'] ?? [])->pluck('association_id')->toArray();
                            $our_team_designation = collect($exsting_teams['our_team'] ?? [])->pluck('designation','association_id')->toArray();
                        @endphp

                        @foreach ($our_teams as $teams)
                            @php
                                $teamImage = !empty($teams->profilepicture) && config('filesystems.default') == 's3'
                                    ? env('AWS_URL') . $teams->profilepicture
                                    : (!empty($teams->profilepicture)
                                        ? url($teams->profilepicture)
                                        : url('/no_image.jpg'));

                                $selected_team = in_array($teams->id, $our_team_ids);
                            @endphp

                            <div class="col-6 col-md-4 mb-3 team-member {{ $selected_team ? 'active' : '' }}" data-id="{{ $teams->id }}">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="position-relative">
                                        <img src="{{ $teamImage }}"
                                            class="team-avatar {{ $selected_team ? 'border border-2 border-success' : '' }}"
                                            style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">

                                        @if ($selected_team)
                                            <!-- Green check -->
                                            <div class="checkmark">✔</div>

                                            <!-- Remove button -->
                                            <button class="btn btn-sm btn-danger position-absolute top-0 start-0 translate-middle removeMemberBtn"
                                                    title="Remove" style="font-size: 0.6rem; line-height: 1;" data-member-id="{{ $teams->id }}"
                                                    data-association-type="{{config('custom.association_type_term.our_team')}}">
                                                ×
                                            </button>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $teams->name }}</div>
                                        <div class="text-muted small">{{ $our_team_designation[$teams->id] ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>


                <!-- Client Team Tab -->
                <div class="tab-pane fade" id="client-team">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                          <h5 class="mb-0">Client Team</h5>
                          <div class="d-flex gap-2">
                            <a class="btn btn-outline-primary btn-sm" id="addTeamMemberBtn" title="Add Team Member"
                                data-bs-toggle="modal" data-bs-target="#clientMemberModal">
                                <i class="bx bx-plus-circle"></i>
                            </a>
                          </div>
                      </div>
                    <div class="row">
                        @php
                            $client_team_ids = collect($exsting_teams['client_contact'] ?? [])->pluck('association_id')->toArray();
                            $client_team_designation = collect($exsting_teams['client_contact'] ?? [])->pluck('designation','association_id')->toArray();
                        @endphp

                        @foreach ($client_teams as $teams)
                            @php
                                $clientImage = !empty($teams->profile_pic) && config('filesystems.default') == 's3'
                                ? env('AWS_URL') . $teams->profile_pic
                                : (!empty($teams->profile_pic)
                                    ? url($teams->profile_pic)
                                    : url('/no_image.jpg'));

                                $selected_team = in_array($teams->id, $client_team_ids);
                            @endphp

                            <div class="col-6 col-md-4 mb-3 team-member {{ $selected_team ? 'active' : '' }}" data-id="{{ $teams->id }}">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="position-relative">
                                        <img src="{{ $clientImage }}"
                                            class="team-avatar {{ $selected_team ? 'border border-2 border-success' : '' }}"
                                            style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">

                                        @if ($selected_team)
                                            <!-- Green check -->
                                            <div class="checkmark">✔</div>

                                            <!-- Remove button -->
                                            <button class="btn btn-sm btn-danger position-absolute top-0 start-0 translate-middle removeMemberBtn"
                                                    title="Remove" style="font-size: 0.6rem; line-height: 1;" data-member-id="{{ $teams->id }}"
                                                data-association-type="{{config('custom.association_type_term.client_contact')}}">
                                                ×
                                            </button>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $teams->name }}</div>
                                        <div class="text-muted small">{{ $client_team_designation[$teams->id] ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- MIA Agents Tab -->
                <div class="tab-pane fade" id="mia-agents">
                <div class="row">
                    @php
                    $mia_team_ids = collect($exsting_teams['mia_agent'] ?? [])->pluck('association_id')->toArray();
                    @endphp
                    @foreach ($agents as $teams)
                        @php
                          $selected_team = in_array( $teams->id, $mia_team_ids);
                        @endphp
                            <div class="col-4 team-member">
                                <div class="position-relative">
                                    <img src="{{ $teams->image }}"
                                        class="team-avatar {{ $selected_team ? 'border border-3 border-primary shadow-sm' : '' }}">
                                    @if($selected_team)
                                        <i class="bi bi-check-circle-fill position-absolute top-0 start-100 translate-middle text-primary bg-white rounded-circle"
                                        style="font-size: 1.2rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $teams->name }}</div>
                                </div>
                            </div>
                      @endforeach            
                </div>
            </div>
            </div>
        </div>
    </div>
    

<!-- Team Member Modal -->
<div class="modal fade" id="teamMemberModal" tabindex="-1" aria-labelledby="teamMemberModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="teamMemberModalLabel">Add Team Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="teamMemberForm">
          <div class="mb-3">
            <label for="our_team_member_select" class="form-label">Select Member</label>
            <select id="our_team_member_select" name="our_team" class="form-select">
              <option value="">-- Select Member --</option>
              @foreach($our_teams as $member)
                @if(!in_array((int)$member->id, $our_team_ids))
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endif
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="our_team_designation_select" class="form-label">Select Designation</label>
            <select id="our_team_designation_select" name="our_team_designation" class="form-select">
              <option value="">-- Select Designation --</option>
              @foreach(config('custom.designation_term') as $designation)
                <option value="{{ $designation }}">{{ $designation }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Project Role</label>
             <input type="text" class="form-control" placeholder="Project Role" id="our_project_role_term" name="our_project_role_term">
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveTeamMemberBtn">Add Member</button>
      </div>
    
    </div>
  </div>
</div>

<!--  -->
<!-- Client Member Modal -->
<div class="modal fade" id="clientMemberModal" tabindex="-1" aria-labelledby="clientMemberModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="clientMemberModalLabel">Add Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="teamMemberForm">
          <div class="mb-3">
            <label for="client_team_member_select" class="form-label">Select Member</label>
            <select id="client_team_member_select" name="our_team" class="form-select">
              <option value="">-- Select Member --</option>
              @foreach($client_teams as $member)
                @if(!in_array((int)$member->id, $client_team_ids))
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endif
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="client_team_designation_select" class="form-label">Select Designation</label>
            <select id="client_team_designation_select" name="our_team_designation" class="form-select">
              <option value="">-- Select Designation --</option>
              @foreach(config('custom.designation_term') as $designation)
                <option value="{{ $designation }}">{{ $designation }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Project Role</label>
             <input type="text" class="form-control" placeholder="Project Role" id="client_project_role_term" name="client_project_role_term">
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveClientMemberBtn">Add Member</button>
      </div>
    
    </div>
  </div>
</div>

<script>
$(document).ready(function () {

    $('#saveTeamMemberBtn').on('click', function (e) {
        e.preventDefault();
        var member_id = $('#our_team_member_select').val();
        var designation = $('#our_team_designation_select').val();
        var project_role_term = $('#our_project_role_term').val();
        var project_id = "{{$project_details->project_id}}";

        if (!member_id || !designation) {
            showErrorMessage('Please select both fields.');
            return;
        }
        showLoadingDialog();
        $.ajax({
            url: "{{ route('project.store_project_member') }}", 
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                member_id: member_id,
                designation: designation,
                project_id: project_id,
                client_id: "{{$project_details->client_id}}",
                customer_id: "{{$project_details->customer_id}}",
                association_type_term: "{{config('custom.association_type_term.our_team')}}",
            },
            success: function (response) {
                hideLoadingDialog();
                $('#teamMemberModal').modal('hide');
                showSuccessMessage(response.message);
                location.reload(); 
            },
            error: function (xhr) {
                hideLoadingDialog();
                showErrorMessage('Failed to add member. Please try again.');
            }
        });
    });

    $('#saveClientMemberBtn').on('click', function (e) {
        e.preventDefault();
        var member_id = $('#client_team_member_select').val();
        var designation = $('#client_team_designation_select').val();
        var project_role_term = $('#client_project_role_term').val();
        var project_id = "{{$project_details->project_id}}";

        if (!member_id || !designation) {
            showErrorMessage('Please select both fields.');
            return;
        }
        showLoadingDialog();
        $.ajax({
            url: "{{ route('project.store_project_member') }}", 
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                member_id: member_id,
                designation: designation,
                project_id: project_id,
                client_id: "{{$project_details->client_id}}",
                customer_id: "{{$project_details->customer_id}}",
                association_type_term: "{{config('custom.association_type_term.client_contact')}}",
            },
            success: function (response) {
                hideLoadingDialog();
                $('#clientMemberModal').modal('hide');
                showSuccessMessage(response.message);
                location.reload(); 
            },
            error: function (xhr) {
                hideLoadingDialog();
                showErrorMessage('Failed to add member. Please try again.');
            }
        });
    });
// removeMemberBtn

    $('.removeMemberBtn').on('click', function (e) {
            e.preventDefault();
            var memberId = $(this).data('member-id');
            var associationType = $(this).data('association-type');
            var project_id = "{{$project_details->project_id}}";

            
            confirmDialogMessage('Delete', 'Are you sure want to delete ?', () => {
                    showLoadingDialog();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: "{{ route('project.remove_project_member') }}",
                        data: {
                            association_id: memberId,
                            association_type_term: associationType,
                            project_id: project_id,
                        },
                        cache: false,
                        success: function(data) {
                            hideLoadingDialog();
                            if (data.status == 1) {
                                showSuccessMessage(data.message)
                                location.reload();
                            } else {
                                showErrorMessage(data.message)
                            }

                        },
                        error: function(jqXHR, textStatus, ex) {

                            console.log(jqXHR.responseText);

                        }
                    });

                });
        });

    });
</script>
