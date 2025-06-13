<script>

  window.onload = function() {
      'use strict';

      var GeneralSettingRef = window.GeneralSettingRef || {};
      var xhr = null;

      if (window.XMLHttpRequest) {
          xhr = window.XMLHttpRequest;
      }
      else if(window.ActiveXObject('Microsoft.XMLHTTP')) {
          xhr = window.ActiveXObject('Microsoft.XMLHTTP');
      }

      var send = xhr.prototype.send;

      xhr.prototype.send = function(data) {
          try{
              send.call(this, data);
          }
          catch(e) {
              GeneralSettingRef.processExceptions(e);
          }
      };
      var allTermsArray = @json($term_categories);

      GeneralSettingRef.page_action = @json($page_action);

      GeneralSettingRef.initEvents = function() {

              $('table.general-stg-table').DataTable();

              $('body').on('click','.add-term',function(e){
                  e.preventDefault();
                  $('#termFrm')[0].reset();
                  $("#termFrm input[name='term_id']").val('');
                  $("#termFrm input[name='term_category_id']").val($(this).data('term-category-id'));
                  $('#term-modal-title').html('Add New '+$(this).data('title'));
                  $('#term_code_title').html($(this).data('title')+' Code');
                  $('#term_title').html($(this).data('title')+' Title');
                  $('#term_detail_title').html($(this).data('title')+' Details');
                  $('#term_icon').html($(this).data('title')+' Image');
                  $(".term_icon").attr("src", baseUrl+ 'no_image.jpg');
                  $('#termFrmBtn').html('Add');
                  $('#termAddUpdateModal').modal('show');
              });

              $('#termFrmBtn').on('click', function(e) {

                submitForm('#termFrm', "", false, (response) => {

                    hideLoadingDialog();
                    if(response.status=='1')
                    {
                        allTermsArray = response.data;
                        showSuccessMessage(response.message);
                        GeneralSettingRef.loadSelectedTermData($("#termFrm input[name='term_category_id']").val());
                        $('#termAddUpdateModal').modal('hide');
                    }else{
                        showErrorMessage(response.message);
                    }
                }, GeneralSettingRef.processExceptions);

              });

              $('body').on('click', '.edit-term', function(e) {

                  e.preventDefault();

                  var term_category_id = $(this).data('term-category-id');
                  var term_id = $(this).data('term-id');
                  var term_category_details = getDetailsFromObjectByKey(allTermsArray, term_category_id, 'term_category_id');

                  if(term_category_details){

                      var details = getDetailsFromObjectByKey(term_category_details.data, term_id, 'term_id');

                      if(details){

                          $('#termFrm')[0].reset();

                          $("#termFrm input[name='term_code']").val(details.term_code);
                          $("#termFrm input[name='term_name']").val(details.term_name);
                          $("#termFrm textarea[name='term_details']").val(details.term_details);
                          $("#termFrm input[name='term_id']").val(details.term_id);
                          $("#termFrm input[name='term_category_id']").val(details.term_category_id);
                          
                          if(details.term_icon!=null){

                            $(".term_icon").attr("src", details.term_icon);
                          }else{
                            $(".term_icon").attr("src", baseUrl+ 'no_image.jpg');
                          }

                          $('#term-modal-title').html('Update '+$(this).data('title'));
                          $('#term_code_title').html($(this).data('title')+' Code');
                          $('#term_title').html($(this).data('title')+' Title');
                          $('#term_detail_title').html($(this).data('title')+' Details');
                          $('#term_icon').html($(this).data('title')+' Image');
                          $('#termFrmBtn').html('Update');
                          $('#termAddUpdateModal').modal('show');
                      }
                  }

              });

              $('body').on('click', '.remove-term', function(e) {

                  e.preventDefault();

                  var id = $(this).attr('data-term-id');
                  var term_category_id = $(this).data('term-category-id');
                  confirmDialogMessage('Remove', 'Are you sure want to remove?', () => {
                      showLoadingDialog();
                      window.getResponseInJsonFromURL(baseUrl+'setup/configuration/general-settings/delete/'+id, '', (response) => {
                          hideLoadingDialog();
                          if(response.status=='1')
                          {
                              allTermsArray = response.data;
                              GeneralSettingRef.loadSelectedTermData(term_category_id);
                              showSuccessMessage(response.message);
                          }else{
                              showErrorMessage(response.message);
                          }
                      },  GeneralSettingRef.processExceptions, 'POST');


                  });

              });

      }

      GeneralSettingRef.loadSelectedTermData = function(term_category_id) {

          var details = getDetailsFromObjectByKey(allTermsArray, term_category_id, 'term_category_id');

          if(details){

              $('.general-stg-table[data-term-category-id="'+term_category_id+'"] tbody').empty();

              if(details.data.length > 0){
                  var html = '';
                  $.each(details.data, function (i, value) {

                      html+= '<tr>';
                      html+= '<td>'+(i+1)+'</td>';
                      html+= '<td>'+value.term_code+'</td>';
                      html+= '<td>'+value.term_name+'</td>';
                      html+= '<td>'+(value.term_details != '' && value.term_details != null ? value.term_details : '')+'</td>';
                      html+= '<td>';
                      if(value.is_default != 1){
                          html+= '<a href="#" data-term-id="'+value.term_id+'" data-term-category-id="'+value.term_category_id+'"  data-title="'+details.category_details+'" class="float-top edit-term"><i class="bx bx-edit theme-text-secondary bx-sm"></i></a>';
                          html+= '<a href="#" data-term-id="'+value.term_id+'" data-term-category-id="'+value.term_category_id+'"  data-title="'+details.category_details+'" class="float-top remove-term"><i class="bx bx-trash theme-text-secondary bx-sm"></i></a>';
                      }
                      html+= '</td>';
                      html+= '</tr>';
                  });

                  $('.general-stg-table[data-term-category-id="'+term_category_id+'"] tbody').append(html);

              }

          }

      };

      GeneralSettingRef.processExceptions = function(e) {
          showErrorMessage(e);
      };

      GeneralSettingRef.initEvents();
  };

</script>
