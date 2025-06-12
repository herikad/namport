<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UsrModule;
use App\Models\UsrRights;

use App\Models\UsrRole;
use App\Models\UsrRoleright;

use Helper;
use Exception;
use Log;
use DB;

class RoleController extends Controller
{
  public function index()
  {
    try {

      return view('user_management.role.index');

    } catch (Exception $exception) {
      return redirect()
        ->back()
        ->with(['status' => 0, 'message' => $exception->getMessage()]);
    }
  }

  public function create($role_id = '')
  {

    try {

      $user_types = \Helper::get_all_terms_by_category(config('custom.term_category.user_type'));

      if (!empty($role_id)) {

        $role_details = UsrRole::find($role_id);

        return view('user_management.role.create',compact('role_details', 'user_types'));

      } else {
        return view('user_management.role.create', compact('user_types'));
      }


    } catch (\Exception $exception) {
      return redirect()->back()->with(['status' => 0, 'message' => $exception->getMessage()]);
    }
  }

  public function get_module_right_list(request $request)
  {
    try {
      $rights = [];

      $user_type = $request->user_type;

      if (isset($user_type) && !empty($user_type)) {

        $parent_module_name = UsrModule::select('usrm.module_id as ModuleId', 'usrm.display_name as parent_module_name')
                                        ->from('pb_usr_module as usrm')
                                        ->where('usrt.user_type_term', $user_type)
                                        ->where('usrm.is_active', 1)
                                        ->join('pb_usr_rights as usrt', 'usrm.module_id', '=', 'usrt.module_id')
                                        ->distinct()
                                        ->orderby('usrm.module_id', 'ASC')
                                        ->get();

        if (count($parent_module_name) > 0) {

          foreach ($parent_module_name as $key => $value) {

            $module_names = UsrRights::select('right_id', 'display_name as module_name')
                                      ->where('module_id',$value->ModuleId)
                                      ->where('user_type_term',$user_type)
                                      ->where('is_active', 1)
                                      ->whereNotIn('right_id', function ($query) {
                                        $query->select('parent_form_id')->from('pb_usr_rights');
                                      });

            if (isset($request->role_id) && !empty($request->role_id)) {
              $role_id = $request->role_id;
              $module_names = $module_names->addSelect(
                DB::raw(
                  "(SELECT usr_roleright.is_view FROM pb_usr_roleright as usr_roleright WHERE usr_roleright.role_id = $role_id AND usr_roleright.right_id = pb_usr_rights.right_id) as is_view"
                ),
                DB::raw(
                  "(SELECT usr_roleright.is_create FROM pb_usr_roleright as usr_roleright WHERE usr_roleright.role_id = $role_id AND usr_roleright.right_id = pb_usr_rights.right_id) as is_create"
                ),
                DB::raw(
                  "(SELECT usr_roleright.is_update FROM pb_usr_roleright as usr_roleright WHERE usr_roleright.role_id = $role_id AND usr_roleright.right_id = pb_usr_rights.right_id) as is_update"
                ),
                DB::raw(
                  "(SELECT usr_roleright.is_delete FROM pb_usr_roleright as usr_roleright WHERE usr_roleright.role_id = $role_id AND usr_roleright.right_id = pb_usr_rights.right_id) as is_delete"
                ),
                DB::raw(
                  "(SELECT usr_roleright.is_export FROM pb_usr_roleright as usr_roleright WHERE usr_roleright.role_id = $role_id AND usr_roleright.right_id = pb_usr_rights.right_id) as is_export"
                )
              );
            }

            $module_names = $module_names->get()->toArray();

            $value->sub_modules = $module_names;
            $rights[] = $value;
          }
        }
      }

      // Log::info('log Request ' . print_r($rights, true));

      $view = view('user_management.role.role-rights-submodules-form', compact('rights'))->render();

      return response()->json(['status' => 1, 'view' => $view]);

      return response()->json([
        'status' => 1,
        'data' => $rights,
        'message' => trans('Response send successfully.'),
      ]);
    } catch (Exception $exception) {
      return response()->json(['status' => 0, 'error' => $exception->getMessage()]);
    }
  }

  public function store(Request $request){

    try{

        $auth_user = auth()->user();

        $id = $request->id;

        if(empty($id)){

            $role = new UsrRole;
            $role->user_type_term = $request->user_type_term;
            $role->role_name = $request->role_name;
            $role->role_details = $request->role_details;
            $role->display_name = $request->display_name;
            $role->created_by = $auth_user->user_id;
            $role->save();
        }
        else{

            $role = UsrRole::findOrfail($id);
            $role->user_type_term = $request->user_type_term;
            $role->role_name = $request->role_name;
            $role->role_details = $request->role_details;
            $role->display_name = $request->display_name;
            $role->updated_by = $auth_user->user_id;
            $role->update();
        }

        $role_right = $request->role_right;
        $role_right_array = json_decode($role_right);

        if (count($role_right_array) > 0) {

            $role_id = $role->role_id;

            if(!empty($id)){
                UsrRoleright::where('role_id', $role_id)->delete();
            }

            if(isset($role_id) && !empty($role_id)){

                foreach ($role_right_array as $key => $value) {

                    $role_right = new UsrRoleright;
                    $role_right->role_id = $role_id;
                    $role_right->right_id = $value->right_id;
                    $role_right->is_view = $value->is_view;
                    $role_right->is_create = $value->is_create;
                    $role_right->is_update = $value->is_update;
                    $role_right->is_delete = $value->is_delete;
                    $role_right->is_export = $value->is_export;

                    $role_right->save();
                }
            }
        }

        if(empty($id)){
            return redirect()->route('setup.user_management.role.index')->with(['status' => 1,'message' => 'Role created successfully!']);
        }
        else{
            return redirect()->route('setup.user_management.role.index')->with(['status' => 1,'message' => 'Role updated successfully!']);
        }
    }
    catch(Exception $exception)
    {
      Log::info("role store".print_r($exception->getMessage(),true));
        return redirect()->route('setup.user_management.role.index')->with('error', 'Something went wrong');
    }

  }

  public function get_role_list_json(Request $request){

    try{

        $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

        $limit = (int)$request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
        $columnIndex = $request->input('order')[0]['column']; // Column index
        $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

        $main_query = UsrRole::select('role.role_id','role.role_name','role.display_name',
                                    'role.role_details','role.is_active','role.IsDefault','role.user_type_term')
                            ->from('pb_usr_role as role')
                            ->orderBy('role.role_id', $columnSortOrder);

        $data_list_for_count = $main_query->get();  // group by and direct count not working

        $recordsTotal = count($data_list_for_count);
        $recordsFiltered = $recordsTotal;

        if(empty($request->input('search.value'))){

            $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);

        }else {

            $search = $request->input('search.value');

            $search_query = $main_query->where('role.role_name','LIKE',"%{$search}%")
                                        ->orWhere('role.role_details', 'LIKE',"%{$search}%");

            $appointments = $search_query->paginate($limit, ['*'], 'page', $page_index);

            $search_list_for_count = $search_query->get();  // group by and direct count not working

            $recordsFiltered = count($search_list_for_count);

        }

        $response = array(
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => (int)$recordsTotal,
            "recordsFiltered" => (int)$recordsFiltered,
            "data" => $appointments->getCollection()
        );

        return response()->json($response, 200);

    }catch(Exception $e) {
        Log::info('log '. print_r($e->getMessage(), true));
       return response()->json(['status' => 0, 'message' => trans('pages.something_wrong')]);

    }
  }

  public function role_status_update(Request $request)
  {
      try{
              $role = UsrRole::where('role_id' , $request->role_id)->first() ;

              if($role){

                  $role->is_active = $request->is_active;
                  $role->save();

                  return response()->json(['status' => 1,  'message' => trans('pages.crud_messages.is_active_update' , ['attr' => 'Role']), 'data' => $request->is_active], 200);

              }else{

                  return response()->json(['status' => 0, "message" =>  trans('pages.crud_messages.no_data' ,['attr' => 'Role'])], 200);
              }
      }
      catch(Exception $exception) {

          Log::info("role_status_update_error ". print_r($exception->getMessage(), true));

          return redirect()->back()->with(['status' => 0,"message" => trans('pages.something_wrong')]);

      }
  }


}
