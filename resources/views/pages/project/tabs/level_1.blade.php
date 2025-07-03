<style>
  .upload-box {
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    border-radius: 6px;
    background-color: #f8f9fa;
    cursor: pointer;
  }

  .upload-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
  }

  .upload-card {
    width: 160px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 0 4px rgba(0,0,0,0.1);
    overflow: hidden;
    text-align: center;
    font-size: 14px;
  }

  .upload-card img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-bottom: 1px solid #eee;
  }

  .upload-card .meta {
    padding: 8px;
  }

  .upload-card button {
    width: 100%;
    border: none;
    background: #f5f5f5;
    padding: 6px;
    cursor: pointer;
    color: red;
  }

  .upload-card button:hover {
    background: #ffe5e5;
  }

  input[type="file"] {
    display: none;
  }
</style>

<!-- Header with title, search and add button -->
<div class="d-flex justify-content-between flex-wrap align-items-center mb-4">
    <h4 class="fw-bold mb-2">{{ $level->level_name }}</h4>
    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-circle me-1"></i> Add {{ $level->level_name }}
        </button>
    </div>
</div>

<!-- Table Design -->
<div class="table-responsive">
    <table class="table table-hover align-middle table-bordered shadow-sm">
        <thead class="table-light">
            <tr class="align-middle text-center">
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col">{{ $level->level_name }}</th>
                <th scope="col">CEO</th>
                <th scope="col">MIA</th>
                @foreach($dynamicColumns as $col)
                    <th scope="col" data-level-no="{{$col['level_no']}}">{{ $col['level_name'] }}</th>
                @endforeach
                <th scope="col">Priority</th>
                <th scope="col" style="min-width: 120px;">Progress</th>
                <th scope="col" style="width: 80px;">Actions</th>
            </tr>
        </thead>
        <tbody id="categoryTableBody">
            {{-- Loop rows here --}}
        </tbody>
    </table>
</div>


<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="addCategoryLabel">Add {{ $level->level_name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="categoryForm">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Process ID</label>
              <div class="form-control bg-light">#{{$process_id}}</div>
            </div>
            <input type="hidden" name="process_id" value="{{$process_id}}">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Title</label>
              <input type="text" class="form-control" name="processname" placeholder="Enter title">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Detail</label>
             <input type="text" class="form-control" name="processdetail" placeholder="Process Details">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea id="description" class="form-control" data-element-ref="ckeditor" name="detail_description" rows="4"></textarea>
            <div class="ck_editor_validate_msg"></div>
          </div>

          <div class="mb-4">
            <div class="bg-light p-3 rounded">
              <label class="form-label fw-semibold mb-2">Select Stakeholders</label>

              <div class="d-flex flex-wrap gap-3">

                  @if(isset($stake_holders) && !empty($stake_holders))
                  @foreach ($stake_holders as $teams)
                      @php
                          $clientImage = !empty($teams->clientContact->profile_pic) && config('filesystems.default') == 's3'
                          ? env('AWS_URL') . $teams->clientContact->profile_pic
                          : (!empty($teams->clientContact->profile_pic)
                              ? url('images/client_contact/profile'.$teams->clientContact->profile_pic)
                              : url('/no_image.jpg'));
                      @endphp
                    @if(isset($teams->clientContact->last_name) || isset($teams->clientContact->first_name))
                    <label class="border rounded p-2 bg-white d-flex align-items-center" style="min-width: 220px; cursor: pointer;">
                      <input type="checkbox" class="form-check-input me-2" name="teamids[]" value="{{$teams->teamid}}">
                      <img src="{{$clientImage}}" class="rounded-circle me-2" width="32" height="32">
                      <div>
                        
                        <div class="fw-bold small">{{@$teams->clientContact->first_name}} {{@$teams->clientContact->last_name}}</div>
                        <div class="text-muted small">{{$teams->designation}}</div>
                        
                      </div>
                    </label>
                    @endif
                  @endforeach
                  @endif
              </div>
            </div>

            </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Assign To</label>
              <select  name="employee_id" class="form-select">
                <option value="">-- Select Employee --</option>
                @foreach($employees as $member)
                    @if(isset($member->employee->display_name))
                      <option value="{{ $member->association_id }}">{{ @$member->employee->display_name }}</option>
                    @endif
                @endforeach
            </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">MIA Agent</label>
               <select  name="mia_id" class="form-select">
                <option value="">-- Select MIA Agent --</option>
                @foreach($mia_agents as $agent)
                      <option value="{{ $agent->association_id }}">{{ @$agent->miaAgent->nameofagent }}</option>
                @endforeach
            </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select  name="status_term" class="form-select">
                @foreach($status_term as $term)
                      <option value="{{ $term }}">{{ $term }}</option>
                @endforeach
            </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Priority</label>
             <select  name="priority_term" class="form-select">
                @foreach($priority_term as $term)
                      <option value="{{ $term }}">{{ $term }}</option>
                @endforeach
            </select>
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold">Workflow</label>
            <select  name="mia_id" class="form-select">
                <option value="">-- Select Workflow --</option>
                @foreach($workflows as $wkflow)
                      <option value="{{ $wkflow->id }}">{{ $wkflow->name }}</option>
                @endforeach
            </select>
          </div>

          <div class="mb-3">
              <label class="form-label fw-semibold">Attachments</label>

              <div class="upload-box" id="dropArea">
                <input type="file" name="attachments[]" id="fileInput" multiple accept=".jpg,.jpeg,.png,.gif,.bmp,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                <p><strong>Drag & Drop</strong> or <span style="color:blue; text-decoration: underline;">Browse Files</span></p>
              </div>

              <div id="previewArea" class="upload-preview"></div>
          </div>

        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="saveLevel1Btn" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
  $(document).ready(function () {
      CKEDITOR.replace('detail_description', {
          height: 200
      });
  });
</script>

<script>
  $(document).ready(function () {
    const $dropArea = $("#dropArea");
    const $fileInput = $("#fileInput");

    // Drag-over prevents default
    $(document).on("dragover drop", function (e) {
      e.preventDefault();
    });

    $dropArea.on("click", function () {
      // only trigger click, don't rebind or attach anything
      document.getElementById("fileInput").click();
    });

    $dropArea.on("dragover", function (e) {
      e.preventDefault();
      $(this).css("border-color", "blue");
    });

    $dropArea.on("dragleave", function () {
      $(this).css("border-color", "#ccc");
    });

    $dropArea.on("drop", function (e) {
      e.preventDefault();
      $(this).css("border-color", "#ccc");
      multipalHandleFiles(e.originalEvent.dataTransfer.files);
    });

    $fileInput.off("change").on("change", function () {
      // clear previous stack of files before triggering again
      multipalHandleFiles(this.files);
      $(this).val('');
    });
  });
</script>

<!-- Store data -->
<script>
$(document).ready(function () {

    $('#categoryForm').submit(function (e) {
        e.preventDefault(); 

          $('.is-invalid').removeClass('is-invalid');
          $('.ck_editor_validate_msg').text('');

        for (instance in CKEDITOR.instances) {
          CKEDITOR.instances[instance].updateElement();
        }


        let hasError = false;

        if (!$('input[name="processname"]').val().trim()) {
            $('input[name="processname"]').addClass('is-invalid');
            hasError = true;
        }

        if (!$('input[name="processdetail"]').val().trim()) {
            $('input[name="processdetail"]').addClass('is-invalid');
            hasError = true;
        }

        if (!$('#description').val().trim()) {
            $('.ck_editor_validate_msg').text('Description is required').css('color', 'red');
            hasError = true;
        }

        if (!$('select[name="employee_id"]').val()) {
            $('select[name="employee_id"]').addClass('is-invalid');
            hasError = true;
        }

        if (!$('select[name="mia_id"]').val()) {
            $('select[name="mia_id"]').addClass('is-invalid');
            hasError = true;
        }

        $('.teamids-error').remove();
        $('input[name="teamids[]"]').removeClass('is-invalid');

        if ($('input[name="teamids[]"]:checked').length === 0) {
          $('input[name="teamids[]"]').first().addClass('is-invalid');
          $('input[name="teamids[]"]').last().parent().after('<div class="text-danger teamids-error mt-1">Please select at least one stakeholder.</div>');
          hasError = true;
        }

        if (hasError) {
            return;
        }

        var formData = new FormData(this); 
        fileList.forEach((file, index) => {
          formData.append('attachments[]', file);
        });

        $.ajax({
            url: assetBaseUrl + 'project/level_1_store',
            type: 'POST',
            data: formData,
            processData: false, 
            contentType: false,
            success: function (response) {
                alert(response.message); 
                $('#categoryForm')[0].reset(); 
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Something went wrong';
                alert(msg);
            }
        });
    });



});
</script>


