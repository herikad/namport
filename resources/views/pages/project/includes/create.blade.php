<div class="modal fade" id="newProjectModal" tabindex="-1"  aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">New Project</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

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
                    <input type="file" id="logoInput" accept="image/*">

                    <!-- Banner Upload -->
                    <div class="upload-box banner-box" id="bannerUploadBox">
                        <span class="upload-placeholder">Click to Upload Banner</span>
                        <img id="bannerPreview" style="display: none;" />
                    </div>
                    <input type="file" id="bannerInput" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                <label class="form-label">Project Name</label>
                <input type="text" class="form-control" value="Namport Process Mapping" />
                </div>

                <div class="mb-3">
                <label class="form-label">Project Manager</label>
                <select class="form-select">
                    <option selected>Senior Project Manager</option>
                    <option>Project Coordinator</option>
                    <option>Team Lead</option>
                </select>
                </div>

                <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Project Type</label>
                    <select class="form-select">
                    <option>Fixed Cost</option>
                    <option>Hourly</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                    <option>Not Started</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                    </select>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control flatpickr" value="2025-07-01">
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control flatpickr" value="2025-12-31">
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
                <ul class="nav nav-pills mt-3" id="pills-tab" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#apqc">APQC</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#bpmn">BPMN</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#sixsigma">Six Sigma</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#iso9001">ISO 9001</a></li>
                </ul>
                <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="apqc">
                    <div class="row">
                    <div class="col-12">
                        <label>Level 1</label>
                        <select class="form-select mb-2"><option>Categories</option></select>
                        <label>Level 2</label>
                        <select class="form-select mb-2"><option>Process Group</option></select>
                        <label>Level 3</label>
                        <select class="form-select mb-2"><option>Processes</option></select>
                        <label>Level 4</label>
                        <select class="form-select mb-2"><option>Activities</option></select>
                        <label>Level 5</label>
                        <select class="form-select"><option>Tasks</option></select>
                    </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="bpmn">BPMN content...</div>
                <div class="tab-pane fade" id="sixsigma">Six Sigma content...</div>
                <div class="tab-pane fade" id="iso9001">ISO 9001 content...</div>
                </div>
            </div>

            <!-- Team Tab -->
            <div class="tab-pane fade" id="team">
                <h6 class="mt-3">Our Team</h6>
                <div class="row g-2">
                <div class="col-md-6"><input type="text" class="form-control" value="Black, Marvin" readonly></div>
                <div class="col-md-6"><input type="text" class="form-control" value="QA Engineer" readonly></div>

                <div class="col-md-6"><input type="text" class="form-control" value="Henry, Arthur" readonly></div>
                <div class="col-md-6"><input type="text" class="form-control" value="Chief Operating Officer" readonly></div>

                <div class="col-md-6"><input type="text" class="form-control" value="Cooper, Kristin" readonly></div>
                <div class="col-md-6"><input type="text" class="form-control" value="DevOps" readonly></div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings">
                <div class="mt-3">
                <label class="form-label">Send Contacts notifications</label>
                <select class="form-select">
                    <option>To all contacts with notifications for projects enabled</option>
                </select>
                </div>
                <div class="mt-3">
                <label class="form-label">Visible Tabs</label>
                <select class="form-select">
                    <option>Tasks, Timesheets, Milestones, Files, Discussions, Gantt, Tickets...</option>
                </select>
                </div>
                <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Allow Customers to view tasks</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Allow Customers to Create tasks</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">Allow Customers to comment on project tasks</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">Allow Customers to upload files</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Allow Customers to open discussions</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">Allow Customers to view milestones</label>
                </div>
            </div>
            </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Submit</button>
        </div>
        </div>
    </div>
</div>