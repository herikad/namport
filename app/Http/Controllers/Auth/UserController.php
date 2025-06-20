<?php

namespace App\Http\Controllers\Auth;

use App\Models\ClubManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsrRole;
use App\Models\UsrUserrole;
use App\Models\MstEmployees;
use App\Models\Countries;
use App\Models\Club;
use App\Models\AssignClub;
use Illuminate\Support\Str;
use Auth;
use Log;
use Validator;
use DB;
use App\Repositories\MailRepository;

class UserController extends Controller
{

  public function change_password(Request $request){

    try {

      $user = Auth::user();

      return view('auth.change_password',compact('user'));

    } catch (\Throwable $th) {
      Log::info('change_password '. print_r($th->getMessage(), true));
      return redirect()->route('dashbaord')->with('error', 'Something went wrong!');
    }

  }

  public function update_change_password(Request $request){

    try {

      $user = Auth::user();

      if ($user)
      {
          if (password_verify($request->current_password, $user->password))
          {

              if($request->new_password == $request->current_password){
                  return redirect()->back()->with('error', 'New password must differ from the old one.');
              }

              if($request->new_password == $request->con_password){

                  $postArray = ['password' => bcrypt($request->new_password) ];

                  $login = User::where('user_id', $user->user_id)->update($postArray);

              }else{
                  return redirect()->back()->with('error', 'Your Re-type password not matched.');
              }

              if (isset($login) && $login)
              {
                return redirect()->back()->with(['message' =>  'Your password successfully changed.']);
              }
          }
          else
          {
            return redirect()->back()->with('error', 'Old Password is invalid, Please enter valid password!.');
          }
      }
      else
      {
        return redirect()->route('dashboard')->with('message', 'Something went wrong!');
      }


    } catch (\Throwable $th) {
      Log::info('update_change_password '. print_r($th->getMessage(), true));
      return redirect()->route('dashboard')->with('error', 'Something went wrong!');
    }


  }

  public function my_profile()
  {
      try {

          $user = auth()->user();

          $userinfo = User::select('user.user_id','user.profile_pic','user.display_name','user.user_name','user.email','user.country_code','user.phone','user.gender_type_term')
                            ->from('pb_users as user')
                            ->where('user.user_id', $user->user_id)
                            ->first();

          $gender_term = \Helper::get_all_terms_by_category(config('custom.term_category.gender_type'));

          $countries = Countries::select('id', 'phonecode')->get();

          // if ($user->association_type_term == config('custom.association_type_term.club_admin')) {

          //   return view('pages.dashboard.my_profile',compact('user','userinfo','gender_term', 'countries'));

          // }

          return view('pages.dashboard.my_profile',compact('user','userinfo','gender_term', 'countries'));

      } catch (Exception $exception) {

          Log::info("mail_setting_list_view_error ". print_r($exception->getMessage(), true));

          return redirect()->back()->with( "message" , trans('pages.something_wrong'));
      }
  }

  public function update_profile(Request $request)
  {

      try {
               $auth_user = auth()->user();

                $validation_rules = [
                    'display_name' => 'required',
                    'user_name' => 'required|unique:pb_users,user_name,'.$auth_user->user_id.',user_id',
                ];

                 $validator = Validator::make($request->all(), $validation_rules);

                if($validator->fails()) {

                    return redirect()->back()->with( 'error' , implode(',', $validator->messages()->all()) );

                }else{

                    $user = User::where('user_id',$auth_user->user_id)->first();

                    if($user){

                      if ($request->hasFile('profile_pic')){

                        \Helper::deleteFile($user->profile_pic);

                        $file = $request->file('profile_pic');
                        $file_name_to_store = time();
                        $file_uploaded_path ='images/user';

                        $file_path = \Helper::upload_file($file, "", $file_name_to_store, $file_uploaded_path);
                        $user->profile_pic = $file_path;

                      }

                        $user->user_name = $request->user_name;
                        $user->display_name = $request->display_name;
                        $user->country_code = $request->country_code;
                        $user->phone = $request->phone;
                        $user->gender_type_term = $request->gender_type_term;
                        $user->save();

                        if($user->profile_pic){

                          if ($user->association_type_term == config('custom.user_type.employee') && DB::table('pb_mst_employees')->where('employee_id',$user->association_id)->first()) {

                            $emp = MstEmployees::find($user->association_id);
                            $emp->profile_pic =  $user->profile_pic;
                            $emp->save();

                          }

                        }


                        return redirect()->back()->with(['status' => 1,'message' => 'Profile updated successfully!']);
                    }
                }

      } catch (Exception $exception) {

          Log::info("update_profile_error ". print_r($exception->getMessage(), true));

          return redirect()->back()->with( "message" , trans('pages.something_wrong'));
      }
  }

  public function index(Request $request){

    return view('user_management.user.index');

  }

  public function create($id=''){

    try {

      $auth_user = auth()->user();
      $countries = Countries::select('id', 'phonecode')->get();
      $role = UsrRole::where('is_active',1)->get(['display_name','role_id']);

      $user_types = \Helper::get_all_terms_by_category(config('custom.term_category.user_type'));

      if(!empty($id)){
        $user = User::find($id);
        $role = UsrRole::where('pb_usr_role.user_type_term',$user->user_type_term)
                        ->where('is_active',1)->get(['display_name','role_id']);

        $user_roles = UsrUserrole::select('pb_usr_role.display_name','pb_usr_userrole.role_id','pb_usr_userrole.user_id')
                      ->where('pb_usr_userrole.user_id',$user->user_id)
                      ->leftjoin('pb_usr_role','pb_usr_role.role_id','=','pb_usr_userrole.role_id')
                      ->get()->toArray();

        $assign_clubs = AssignClub::where('association_id', $id)->get()->toArray();

        return view('user_management.user.create',compact('role','user_types','user','user_roles','auth_user','countries','assign_clubs'));
      } else {
        return view('user_management.user.create',compact('role','user_types','auth_user','countries'));
      }



    } catch (\Throwable $th) {

          Log::info("update_create_error ". print_r($th->getMessage(), true));
          return redirect()->route('setup.user_management.user.index')->with("message" , trans('pages.something_wrong'));
    }

  }

  public function store(Request $request){

    try {

      $id = $request->id;
      $auth_user = auth()->user();

      if(!empty($id)){

        $validation_rules = [
          'display_name' => 'required',
          'user_name' => 'required|unique:pb_users,user_name,'.$id.',user_id',
          'role_id' => 'required',
        ];

      }else{

        $validation_rules = [
          'display_name' => 'required',
          'user_name' => 'required|unique:pb_users,user_name',
          'role_id' => 'required',
        ];

      }

      $validator = Validator::make( $request->all(), $validation_rules );

      if($validator->fails()) {
        // return redirect()->back()->with( 'error' , implode(',', $validator->messages()->all()) );
          return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()) ]);

      } else {


        $new_user = false;

        if(empty($id)){

            $user = new User;
            $user->user_type_term = $request->user_type_term;
            $user->display_name = $request->display_name;
            $user->email = $request->user_name;
            $user->user_name = $request->user_name;
            $user->country_code = $request->country_code;
            $user->phone = $request->mobile;
            $user->password = bcrypt($request->password);
            $user->created_by = $auth_user->user_id;
            $user->email_token = Str::random(155);
            if(auth()->user()->association_type_term == config('custom.association_type_term.club_admin')){
              $user->association_type_term = config('custom.association_type_term.club_user');
              $user->club_manager_id = $auth_user->user_id;
            }else{
              $user->association_type_term = config('custom.association_type_term.admin');
            }
            $user->save();
            $user->association_id = $user->user_id;
            $user->save();
            $new_user = true;

        }
        else{

            $user = User::findOrfail($id);
            $user->user_type_term = $request->user_type_term;
            $user->display_name = $request->display_name;
            $user->user_name = $request->user_name;
            $user->updated_by = $auth_user->user_id;
            $user->update();
        }

        if ($user) {

          UsrUserrole::where('user_id', $user->user_id)->delete();

            $assign_role = new UsrUserrole;
            $assign_role->user_id = $user->user_id;
            $assign_role->role_id = $request->role_id;
            $assign_role->save();

            // Entry in club manager
            if ($id) {
              $club_manager = ClubManager::where('user_id', $id)->first();
              $club_manager->updated_by = $auth_user->user_id;
            } else {
              $club_manager = new ClubManager();
              $club_manager->user_id = $user->user_id;
              $club_manager->created_by = $auth_user->user_id;
            }

            $club_manager->user_type_term = config('custom.user_type_term.club_user');
            $club_manager->club_manager_name = $request->display_name;
            $club_manager->email = $request->user_name;
            $club_manager->country_code = $request->country_code;
            $club_manager->mobile = $request->mobile;
            $club_manager->status_term = config('custom.status_term.active');
            $club_manager->timezone_term = $request->timezone_term;
            $club_manager->save();

            // Club Binding
            if(isset($request->clubs)){
                AssignClub::where('association_id', $user->user_id)->delete();
                foreach ($request->clubs as $club_id) {

                  $assign_club = new AssignClub;
                  $assign_club->association_id = $user->user_id;
                  $assign_club->association_type_term = config('custom.user_type_term.club_user');
                  $assign_club->club_manager_id = $user->club_manager_id;
                  $assign_club->club_id = $club_id;
                  $assign_club->save();

                }
            }

            if($new_user){

              $email_data['email_token'] = $user->email_token;
              $email_data['user_name'] = $user->display_name;

              MailRepository::replace_email_template(config("custom.mail_send_types.WELCOME_MAIL"),$email_data ,array($user->user_name) ) ;
            }

        }
            return response()->json( [ 'status' => 1, 'message' =>!empty($id) ? 'User updated successfully!' : 'User created successfully!', 'redirect_url' => route('setup.user_management.user.index')], 200 );
      }

    } catch (\Throwable $th) {

          Log::info("update_store_error ". print_r($th->getMessage(), true));
          return redirect()->route('setup.user_management.user.index')->with( "message" , trans('pages.something_wrong'));
    }

  }

  public function user_json_list(Request $request){

    try {

      $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

      $limit = (int)$request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
      $columnIndex = $request->input('order')[0]['column']; // Column index
      $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
      $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

      $main_query =  User::from('pb_users as users')
                            // ->leftjoin('pb_usr_role as role','role.role_id','=','users.role_id')
                            ->select('users.user_id','users.display_name','users.user_name','users.is_active','users.association_type_term');

      $main_query =  $main_query->where('users.user_type_term','!=', config('custom.association_type_term.member'));
      $main_query =  $main_query->where('users.user_id','!=',auth()->user()->association_id);

      if(auth()->user()->association_type_term == config('custom.association_type_term.admin')){

        $main_query =  $main_query->where('users.user_type_term', config('custom.user_type_term.super_admin'));

      } else if(auth()->user()->association_type_term == config('custom.association_type_term.club_admin')){

        $main_query =  $main_query->where('users.club_manager_id',auth()->user()->association_id);
      } else{

        $main_query =  $main_query->whereNull('users.club_manager_id');
      }

      $main_query =  $main_query->orderBy($columnName, $columnSortOrder);

      $data_list_for_count = $main_query->get();  // group by and direct count not working
      $recordsTotal = count($data_list_for_count);

      $recordsFiltered = $recordsTotal;

      if(empty($request->input('search.value'))){

          $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);

      }else {

          $search = $request->input('search.value');

          // $search_query = $main_query->where('users.user_name','LIKE',"%{$search}%")
          //                             ->orWhere('users.association_type_term','LIKE',"%{$search}%")
          //                             ->orWhere('users.display_name','LIKE',"%{$search}%");

          $search_query = $main_query->where(function ($query) use ($search) {
                                $query->where('users.user_name', 'LIKE', "%{$search}%")
                                      ->orWhere('users.association_type_term', 'LIKE', "%{$search}%")
                                      ->orWhere('users.display_name', 'LIKE', "%{$search}%");
          });

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

    }catch(\Exception $e) {

      Log::info("UserController index - ". $e->getMessage());
      return redirect()->route('dashboard')->with(['error' => "Something want wrong.", 'status' => 0]);

    }

  }

  public function user_status_update(Request $request)
  {
      try{
              $user = User::where('user_id' , $request->user_id)->first() ;

              if($user){

                  $user->is_active = $request->is_active;
                  $user->save();

                  return response()->json(['status' => 1,  'message' => trans('pages.crud_messages.is_active_update' , ['attr' => 'User']), 'data' => $request->is_active], 200);

              }else{

                  return response()->json(['status' => 0, "message" =>  trans('pages.crud_messages.no_data' ,['attr' => 'User'])], 200);
              }
      }
      catch(Exception $exception) {

          Log::info("user_status_update_error ". print_r($exception->getMessage(), true));

          return redirect()->back()->with(['status' => 0,"message" => trans('pages.something_wrong')]);

      }
  }

  public function roles_by_type_term(Request $request)
  {
    $userTypeTerm = $request->input('user_type_term');

    // Query the roles based on the user_type_term
    $roles = UsrRole::where('user_type_term', $userTypeTerm)->get(['role_id', 'display_name']);

    return response()->json($roles);
  }

}
