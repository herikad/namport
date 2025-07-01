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

      <form id="categoryForm" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Process ID</label>
              <div class="form-control bg-light">#579246</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Title</label>
              <input type="text" class="form-control" name="title" placeholder="Enter title">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Detail</label>
            <div class="form-control bg-light">Placeholder</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea class="form-control" rows="3" placeholder="Describe the category..."></textarea>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Stakeholders</label>
            <div class="d-flex flex-wrap gap-2">
              <!-- Repeat this block for each person -->
              <div class="d-flex align-items-center border rounded px-2 py-1">
                <img src="https://via.placeholder.com/32" class="rounded-circle me-2" width="32" height="32">
                <div>
                  <div class="fw-bold small">Jerome Bell</div>
                  <div class="text-muted small">Content Writer</div>
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger ms-2"><i class="bi bi-x-circle-fill"></i></button>
              </div>
              <!-- Add More -->
              <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                <i class="bi bi-plus-circle me-1"></i> Add
              </button>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Assign To</label>
              <select class="form-select">
                <option selected>Jerome Bell</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">MIA Agent</label>
              <select class="form-select">
                <option selected>Ruva</option>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select class="form-select">
                <option>In Progress</option>
                <option>Pending</option>
                <option>Completed</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Priority</label>
              <select class="form-select">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Attachments</label>
            <div class="border rounded p-4 text-center text-muted" style="border-style: dashed;">
              <i class="bi bi-upload display-6"></i><br>
              Drag & Drop or <a href="#">Browse Files</a>
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold">Workflow</label>
            <div class="form-control bg-light">Placeholder</div>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>

      </form>
    </div>
  </div>
</div>


<script>



</script>