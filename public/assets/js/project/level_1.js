$(document).ready(function () {

    $('#addCategoryModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    // On modal close, reset everything
    $('#addCategoryModal').on('hidden.bs.modal', function () {
        $('#categoryForm')[0].reset();
        $('#fileInput').val('');
        $('#previewArea').empty();
        $('.select2').val(null).trigger('change'); // if using Select2 dropdowns
    });

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
            url: baseUrl + 'project/level_1_store',
            type: 'POST',
            data: formData,
            processData: false, 
            contentType: false,
            success: function (response) {
                showSuccessMessage(response.message); 
                $('#categoryForm')[0].reset(); 
                $('#fileInput').val('');
                $('#previewArea').empty(); 
                $('#addCategoryModal').modal('hide');
                $('#categoryTable').DataTable().ajax.reload();
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Something went wrong';
                showErrorMessage(msg);
            }
        });
    });

    CKEDITOR.replace('detail_description', {
        height: 200
    });

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

  


  let columns = [
      { data: 'id' },
      { data: 'processname' },
      { data: 'ceo' },
      { data: 'mia' },
  ];

  if (Array.isArray(window.dynamicColumns)) {
      window.dynamicColumns.forEach(col => {
          columns.push({ data: col });
      });
  }

  columns.push(
      { data: 'priority' },
      { data: 'progress' },
      { data: 'action' }
  );

// Now initialize DataTable
$('#categoryTable').DataTable({
    processing: true,
    serverSide: false,
    ordering: false,
    ajax: {
        url: baseUrl + 'project/level_json_data',
        type: 'GET',
        dataSrc: 'data',
        data: function (d) {
            d.project_id = $('#project_id').val(); 
            d.proj_process_framework_id = $('#level1id').val(); 
            d.level_no = $('#level_no').val(); 
        }
    },
    columns: columns
});


    
});