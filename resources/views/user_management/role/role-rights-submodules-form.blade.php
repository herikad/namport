<div class="accordion mt-3 accordion-header-primary" id="accordionWithIcon">

  @if (is_array($rights) && count($rights))

    @foreach ($rights as $item_key => $item)

    <div class="accordion-item card">
      <h2 class="accordion-header d-flex align-items-center">
        <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionWithIcon-{{$item_key}}" aria-expanded="false">
          {{ $item->parent_module_name }}
        </button>
      </h2>
      <div id="accordionWithIcon-{{$item_key}}" class="accordion-collapse collapse">
        <div class="accordion-body">

          <div class="table-responsive">

            <table id="" class="table">

              <thead>
                <tr>
                  <th class="w-25">{{ trans('pages.menu_name') }}</th>
                  <th>
                    <div class="checkbox">
                      <input type="checkbox" class="form-check-input chk_action" id="chkViewId{{$item->ModuleId}}" data-id="{{$item->ModuleId}}" data-action="view">
                      <label for="chkViewId{{$item->ModuleId}}" class="pl-25">{{ trans('pages.view') }}</label>
                    </div>
                  </th>

                  <th>
                    <div class="checkbox">
                      <input type="checkbox" class="form-check-input chk_action" id="chkCreateId{{$item->ModuleId}}" data-id="{{$item->ModuleId}}" data-action="create">
                      <label for="chkCreateId{{$item->ModuleId}}" class="pl-25">{{ trans('pages.create') }}</label>
                    </div>
                  </th>

                  <th>
                    <div class="checkbox">
                      <input type="checkbox" class="form-check-input chk_action" id="chkUpdateId{{$item->ModuleId}}" data-id="{{$item->ModuleId}}" data-action="update">
                      <label for="chkUpdateId{{$item->ModuleId}}" class="pl-25">{{ trans('pages.update') }}</label>
                    </div>
                  </th>

                  <th>
                    <div class="checkbox">
                      <input type="checkbox" class="form-check-input chk_action" id="chkDeleteId{{$item->ModuleId}}" data-id="{{$item->ModuleId}}" data-action="delete">
                      <label for="chkDeleteId{{$item->ModuleId}}" class="pl-25">{{ trans('pages.delete') }}</label>
                    </div>
                  </th>

                  <th>
                    <div class="checkbox">
                      <input type="checkbox" class="form-check-input chk_action" id="chkExportId{{$item->ModuleId}}" data-id="{{$item->ModuleId}}" data-action="export">
                      <label for="chkExportId{{$item->ModuleId}}" class="pl-25">{{ trans('pages.export') }}</label>
                    </div>
                  </th>

                </tr>

              </thead>

              @if (is_array($item->sub_modules) && count($item->sub_modules))

                @foreach ($item->sub_modules as $sub_key => $sub_item)
                  <tbody>
                    <tr class="role_right" data-id="{{ $sub_item['right_id'] }}">
                      <td class="w-25">{{ $sub_item['module_name'] }}</td>
                      <td>
                        <div class="checkbox">
                          <input type="checkbox" name="IsView" class="form-check-input view_menu_chk" id="ViewModuleId{{$sub_item['right_id']}}" data-id="{{$item->ModuleId}}" data-right_id="{{$sub_item['right_id']}}" value="1"  {{@$sub_item['is_view'] == 1 ? "checked" : ""}}>
                          <label for="ViewModuleId{{$sub_item['right_id']}}"></label>
                        </div>
                      </td>

                      <td>
                        <div class="checkbox">
                          <input type="checkbox" name="IsCreate" class="form-check-input create_menu_chk" id="CreateModuleId{{$sub_item['right_id']}}" data-id="{{$item->ModuleId}}" data-right_id="{{$sub_item['right_id']}}" value="1"  {{@$sub_item['is_create'] == 1 ? "checked" : ""}} >
                          <label for="CreateModuleId{{$sub_item['right_id']}}"></label>
                        </div>
                      </td>

                      <td>
                        <div class="checkbox">
                          <input type="checkbox" name="IsUpdate" class="form-check-input update_menu_chk" id="UpdateModuleId{{$sub_item['right_id']}}" data-id="{{$item->ModuleId}}" data-right_id="{{$sub_item['right_id']}}" value="1"  {{@$sub_item['is_update'] == 1 ? "checked" : ""}} >
                          <label for="UpdateModuleId{{$sub_item['right_id']}}"></label>
                        </div>
                      </td>

                      <td>
                        <div class="checkbox">
                          <input type="checkbox" name="IsDelete" class="form-check-input delete_menu_chk" id="DeleteModuleId{{$sub_item['right_id']}}" data-id="{{$item->ModuleId}}" data-right_id="{{$sub_item['right_id']}}" value="1"  {{@$sub_item['is_delete'] == 1 ? "checked" : ""}} >
                          <label for="DeleteModuleId{{$sub_item['right_id']}}"></label>
                        </div>
                      </td>

                      <td>
                        <div class="checkbox">
                          <input type="checkbox" name="IsExport" class="form-check-input export_menu_chk" id="ExportModuleId{{$sub_item['right_id']}}" data-id="{{$item->ModuleId}}" data-right_id="{{$sub_item['right_id']}}" value="1"  {{@$sub_item['is_export'] == 1 ? "checked" : ""}} >
                          <label for="ExportModuleId{{$sub_item['right_id']}}"></label>
                        </div>
                      </td>

                    </tr>
                  </tbody>

                @endforeach

              @endif

            </table>

          </div>

        </div>
      </div>
    </div>


    @endforeach

  @endif

</div>
