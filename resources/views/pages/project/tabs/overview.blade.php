
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
      background-color: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-right: 10px;
    }
    .team-member {
      display: flex;
      align-items: center;
      margin-bottom: 12px;
    }
    .tab-pane .col-md-6 {
      padding: 5px 15px;
    }
    .border-divider {
      border-top: 1px solid #dee2e6;
      margin-top: 1.5rem;
      margin-bottom: 1rem;
    }

    .team-tabs .nav-link {
      color: #6c757d;
      font-weight: 500;
    }
    .team-tabs .nav-link.active {
      color: #000;
      border-bottom: 2px solid #000;
    }
    .milestone-row {
      display: flex;
      justify-content: space-between;
      padding: 3px 0;
    }
    .metrics-value {
      font-weight: bold;
      font-size: 18px;
    }
    .metrics-label {
      font-size: 14px;
      color: #6c757d;
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
            <div class="milestone-row"><span>Kick-off Workshop</span><span class="fw-semibold">Completed</span></div>
            <div class="milestone-row"><span>Tier Classification</span><span class="fw-semibold">Finalized</span></div>
            <div class="milestone-row"><span>SharePoint Repository Setup</span><span class="fw-semibold">In Progress</span></div>
            <div class="milestone-row"><span>EXCO Review Scheduled</span><span class="fw-semibold">15/06/2025</span></div>
            <div class="milestone-row"><span>Final Documentation</span><span class="fw-semibold">Due 25/06/2025</span></div>
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
                <div class="row">
                    @foreach ($our_teams as $teams)
                        @php
                            $teamImage = !empty($teams->profilepicture) && config('filesystems.default') == 's3'
                                ? env('AWS_URL') . $teams->profilepicture
                                : (!empty($teams->profilepicture)
                                    ? url($teams->profilepicture)
                                    : url('/no_image.jpg'));
                                
                                $selected_team = in_array( $teams->id, $exsting_teams);
                        @endphp
                            <div class="col-4 team-member">
                                <div class="position-relative">
                                    <img src="{{ $teamImage }}"
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

                <!-- Client Team Tab -->
                <div class="tab-pane fade" id="client-team">
                <div class="row">
                    <div class="col-4 team-member">
                    <img src="https://via.placeholder.com/56" class="team-avatar">
                    <div>Floyd</div>
                    </div>
                    <div class="col-4 team-member">
                    <img src="https://via.placeholder.com/56" class="team-avatar">
                    <div>Jenny</div>
                    </div>
                    <div class="col-4 team-member">
                    <img src="https://via.placeholder.com/56" class="team-avatar">
                    <div>Ralph</div>
                    </div>
                </div>
                </div>

                <!-- MIA Agents Tab -->
                <div class="tab-pane fade" id="mia-agents">
                <div class="row">
                    <div class="col-4 team-member">
                    <img src="https://i.pravatar.cc/100?img=12" class="team-avatar">
                    <div>Ruva</div>
                    </div>
                    <div class="col-4 team-member">
                    <img src="https://i.pravatar.cc/100?img=33" class="team-avatar">
                    <div>Tana</div>
                    </div>
                    <div class="col-4 team-member">
                    <img src="https://i.pravatar.cc/100?img=23" class="team-avatar">
                    <div>Kayo</div>
                    </div>
                    <div class="col-4 team-member">
                    <img src="https://i.pravatar.cc/100?img=15" class="team-avatar">
                    <div>Zali</div>
                    </div>
                    <div class="col-4 team-member">
                    <img src="https://i.pravatar.cc/100?img=29" class="team-avatar">
                    <div>Bako</div>
                    </div>
                    <div class="col-4 team-member">
                    <img src="https://i.pravatar.cc/100?img=37" class="team-avatar">
                    <div>Neli</div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    


