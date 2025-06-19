<div class="modal fade" id="newProjectModal" tabindex="-1"  aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">New Project</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form  id="store_project_adta" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="client_id" value="{{$client_id}}">
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="projectTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basicInfo" type="button">Basic Info</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#levels" type="button">Levels</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#team" type="button">Team</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#settings" type="button">Settings</button>
                </li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content">
                <!-- Basic Info Tab -->
                <div class="tab-pane fade show active" id="basicInfo">
                    
                    <div class="row g-3 mb-3">
                        <div class="upload-section">
                            <!-- Logo Upload -->
                            <div class="upload-box logo-box" id="logoUploadBox">
                                <span class="upload-placeholder">Click to Upload Logo</span>
                                <img id="logoPreview" style="display: none;" />
                            </div>
                            <input type="file" id="logoInput" name="logo_image" accept="image/*">

                            <!-- Banner Upload -->
                            <div class="upload-box banner-box" id="bannerUploadBox">
                                <span class="upload-placeholder">Click to Upload Banner</span>
                                <img id="bannerPreview" style="display: none;" />
                            </div>
                            <input type="file" id="bannerInput" name="banner_image" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                    <label class="form-label">Project Name</label>
                    <input type="text" class="form-control" name="projectname" value="" require/>
                    </div>

                    <div class="mb-3">
                    <label class="form-label">Project Manager</label>
                    <select class="form-select" name="projectmanager_id">
                        <option selected>Senior Project Manager</option>
                        @if (count($project_managers))
                            @foreach($project_managers as $key => $value)
                            <option value="{{$key}}" >{{$value}}</option>
                            @endforeach
                        @endif
                    </select>
                    </div>

                    <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Billing</label>
                        <select class="form-select" name="billingtype_term">
                        @if (count($billing_terms))
                            @foreach($billing_terms as $key => $value)
                            <option value="{{$key}}" >{{$value}}</option>
                            @endforeach
                        @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status_term">
                        @if (count($status))
                            @foreach($status as $key => $value)
                            <option value="{{$key}}" >{{$value}}</option>
                            @endforeach
                        @endif
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control flatpickr" name="startdate" value="">
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control flatpickr" name="enddate" value="">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">Short Description</label>
                        <textarea id="short_description" class="form-control" data-element-ref="ckeditor" name="short_description" rows="6"></textarea>
                        <div class="ck_editor_validate_msg"></div>
                    </div>
                    </div>
                </div>

                <!-- Levels Tab -->
                <div class="tab-pane fade" id="levels">
                    <input type="hidden" name="level_selected_tab" id="level_selected_tab">
                    <ul class="nav nav-pills mt-3" id="pills-tab" role="tablist">
                        @php $first = true; @endphp
                        @foreach($frameworkData as $category => $levels)
                            <li class="nav-item">
                                <a class="nav-link {{ $first ? 'active' : '' }}" data-bs-toggle="pill" href="#{{ Str::slug($category) }}" data-category="{{ $category }}">
                                    {{ ucwords(str_replace('_', ' ', $category)) }}
                                </a>
                            </li>
                            @php $first = false; @endphp
                        @endforeach
                    </ul>

                    <div class="tab-content mt-3">
                        
                        @php $first = true; @endphp
                        @foreach($frameworkData as $category => $levels)
                            <div class="tab-pane fade {{ $first ? 'show active' : '' }}" id="{{ Str::slug($category) }}">
                                <div class="row">
                                    <div class="col-12">
                                    @foreach($levels as $level)
                                            <div class="p-2 border rounded mb-2 bg-light">
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-bold me-1 text-primary small">Level {{ $level['levelno'] }}</span>
                                                    <span class="text-muted small">•</span>
                                                    <span class="ms-1 small">{{ $level['levelname'] }}</span>
                                                </div>
                                                @if(!empty($level['shortdescription']))
                                                    <div class="text-muted small mt-1 ps-2">{{ $level['shortdescription'] }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @php $first = false; @endphp
                        @endforeach
                    </div>
                </div>

                <!-- Team Tab -->
                <div class="tab-pane fade" id="team">
                <!-- Nav Tabs -->
                    <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#ourTeam">Our Team</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#clientTeam">Client Team</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#miaAgents">MIA Agents</a>
                    </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content pt-3">
                        <!-- OUR TEAM -->
                        <div class="tab-pane fade show active" id="ourTeam">
                            <div id="ourTeamRepeater" class="team-repeater" data-type="our" data-members='@json($our_teams)'>
                            <div class="row g-2 align-items-center mb-2 repeater-item">
                                <div class="col-md-5">
                                <select class="form-select team-member-select" name="our_team[]">
                                    <option value="">Select Member</option>
                                    @foreach ($our_teams as $member)
                                    <option value="{{ $member['id'] }}">{{ $member['name'] }}</option>
                                    @endforeach
                                </select>
                                </div>
                                <div class="col-md-5">
                                <input type="text" class="form-control" placeholder="Designation" name="our_team_designation[]">
                                </div>
                                <div class="col-md-2 text-end">
                                <a class="btn btn-danger btn-sm remove-btn">
                                    <i class="bx bx-x-circle"></i>
                                </a>
                                </div>
                            </div>
                            </div>
                            <div class="text-end mt-2">
                            <a class="btn btn-primary btn-sm add-btn" data-target="#ourTeamRepeater">
                                <i class="bx bx-plus-circle"></i> Add Our Team Member
                            </a>
                            </div>
                        </div>
                        <!-- CLIENT TEAM -->
                        <div class="tab-pane fade" id="clientTeam">
                            <div id="clientTeamRepeater" class="team-repeater" data-type="client" data-members='@json($client_teams)'>
                            <div class="row g-2 align-items-center mb-2 repeater-item">
                                <div class="col-md-5">
                                <select class="form-select team-member-select" name="client_team[]">
                                    <option value="">Select Member</option>
                                    @foreach ($client_teams as $member)
                                    <option value="{{ $member['id'] }}">{{ $member['name'] }}</option>
                                    @endforeach
                                </select>
                                </div>
                                <div class="col-md-5">
                                <input type="text" class="form-control" placeholder="Designation" name="client_designation[]">
                                </div>
                                <div class="col-md-2 text-end">
                                <a class="btn btn-outline-danger btn-sm remove-btn">
                                    <i class="bx bx-x-circle"></i>
                                </a>
                                </div>
                            </div>
                            </div>
                            <div class="text-end mt-2">
                            <a class="btn btn-outline-primary btn-sm add-btn" data-target="#clientTeamRepeater">
                                <i class="bx bx-plus-circle"></i> Add Client Team Member
                            </a>
                            </div>
                        </div>
                        <!-- MIA Agents -->
                        <div class="tab-pane fade" id="miaAgents">
                            <div class="mia-agents-container">
                                <input type="hidden" name="selected_mia_agents" id="selectedMiaAgentsInput" value="">
                                @foreach($mia_agents as $agent)
                                    <div class="mia-agent">
                                        <label>
                                            <input type="checkbox"
                                                class="mia-checkbox"
                                                name="mia_agents[]"
                                                value="{{ $agent['id'] }}">
                                            <div class="agent-box">
                                                <img src="{{ asset($agent['image']) }}" alt="{{ $agent['name'] }}">
                                                <span>{{ $agent['name'] }}</span>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Send Contacts notifications</label>
                        <select name="sendnotification_term" class="form-select">
                            @foreach($send_notificaiton_term as $key => $notificaiton)
                            <option value="{{$key}}">{{$notificaiton}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" name="allowcustomertasks" id="allowcustomertasks" >
                        <label class="form-check-label" for="allowcustomertasks">Allow Customers to view tasks</label>
                    </div>

                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" name="allowcustomertoedittask" id="allowcustomertoedittask" >
                        <label class="form-check-label" for="allowcustomertoedittask">Allow Customers to create/edit tasks</label>
                    </div>

                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" name="allowcustomertocommentonprojecttask" id="allowcustomertocommentonprojecttask">
                        <label class="form-check-label" for="allowcustomertocommentonprojecttask">Allow Customers to comment on project tasks</label>
                    </div>

                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" name="allowcustomertouploadattachmentontask" id="allowcustomertouploadattachmentontask">
                        <label class="form-check-label" for="allowcustomertouploadattachmentontask">Allow Customers to upload attachments on tasks</label>
                    </div>

                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" name="allowcustomertoviewloggedhrs" id="allowcustomertoviewloggedhrs">
                        <label class="form-check-label" for="allowcustomertoviewloggedhrs">Allow Customers to view logged hours</label>
                    </div>

                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" name="allowcustomertouploadfile" id="allowcustomertouploadfile">
                        <label class="form-check-label" for="allowcustomertouploadfile">Allow Customers to upload files</label>
                    </div>

                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" name="allowcustomertoviewteams" id="allowcustomertoviewteams">
                        <label class="form-check-label" for="allowcustomertoviewteams">Allow Customers to view team members</label>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" id="create_project_data" class="btn btn-primary">Submit</button>
            </div>
            </div>
        </form>
    </div>
</div>