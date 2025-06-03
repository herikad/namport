<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Repositories\ClubManagerRepository;
use App\Repositories\ClubRepository;
use App\Models\User;
use App\Models\Countries;
use App\Models\ClubManager;
use App\Models\TimeZone;
use Illuminate\Support\Str;
use Auth;
use Closure;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Log;

class LoginController extends Controller
{
  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = RouteServiceProvider::HOME;

  public function show_login_form(Request $request, $claim_club_id='')
  {
    if (Auth::check()) {
      return redirect()->route('dashboard');
    } else {
      return view('auth.login', compact('claim_club_id'));
    }
  }

  public function basic_login(Request $request)
  {
    $credentials = $request->only('user_name', 'password');

    $user_detail = User::where('is_active',1)->where('is_deleted', 0)
                        ->where('user_name',$request->user_name)
                        ->where('association_type_term', '!=', config('custom.association_type_term.member'))
                        // ->whereIn('association_type_term', [config('custom.association_type_term.admin'), config('custom.association_type_term.club_admin'), config('custom.association_type_term.club_user')])
                        ->first();

    if($user_detail)
    {

      if($user_detail->association_type_term == config('custom.association_type_term.club_admin')){

        if($user_detail->is_email_verify == 0){

          return redirect()
                  ->back()
                  ->with(['error' => "Your email is not verified. Please check your email and verify your account before logging in."]);

        }

        $club_owner = ClubManager::find($user_detail->association_id);

        if($club_owner){

          if(in_array($club_owner->approval_status, [ config('custom.approval_status.reject') ])){

            return redirect()
                      ->back()
                      ->with(['error' => trans('auth.failed')]);

          }

        }else{

          return redirect()
                    ->back()
                    ->with(['error' => trans('auth.failed')]);

        }

      }

      if (auth()->attempt($credentials)) {

        $user = Auth::user();

        if ($user) {

          if($user->association_type_term == config('custom.association_type_term.club_admin')){

            // SAVE CLub claim request on login time

            if(isset($request->claim_club_id) && !empty($request->claim_club_id)){

              try {

                $request->request->add(['association_id' => $user->association_id]);
                $request->request->add(['association_type_term' => $user->association_type_term]);
                $request->request->add(['claim_association_id' => $request->claim_club_id]);
                $request->request->add(['claim_association_type_term' => config('custom.claim_types.club')]);

                ClubRepository::save_claim_request($request);

              } catch (\Throwable $th) {

                \Log::info("unable to save claim login ". print_r($th->getMessage(), true));

              }
            }

          }

          return redirect()
            ->route('dashboard')
            ->with(['message' => trans('auth.success_login')], 422);

        } else {
          Auth::logout();

          $request->session()->invalidate();

          $request->session()->regenerateToken();

          return redirect()
            ->back()
            ->with(['error' => trans('auth.failed')]);
        }
      } else {
        return redirect()
          ->back()
          ->with(['error' => trans('auth.failed')]);
      }
    }else {
      return redirect()
        ->back()
        ->with(['error' => 'User Not Found!']);
    }

  }

  public function show_club_signup(Request $request, $claim_club_id='')
  {
    if (Auth::check()) {
      return redirect()->route('dashboard');
    } else {

      $countries = Countries::select('id', 'phonecode', 'sortname')->where('is_active',1)->get();

      $gender_term = \Helper::get_all_terms_by_category(config('custom.term_category.gender_type'));

      $timezone = TimeZone::select('timezone_id', 'label')->get();

      return view('auth.club_signup', compact('gender_term', 'countries', 'timezone', 'claim_club_id'));
    }
  }

  public function club_signup(Request $request)
  {

    $result = ClubManagerRepository::save_clubowner($request);

    if($result['status']){

        $status = $result['status'];
        $result['status'] = $status;
        $result['redirect_url'] = route('login');
        $result['message'] = trans('pages.club_signup_success');

    }
    return response()->json($result);

  }

  public function email_verify($email_token)
  {

    $data = [
      'status' => 0,
      'message' => trans('pages.email_activation_link_invalid')
    ];

    try{

        $user = User::where('email_token', $email_token)
                      ->where('is_active', 1)
                      ->where('is_deleted', 0)
                      ->first();

        if($user){

          if($user->is_email_verify == 1){

            $data['status'] = 1;
            $data['message'] = trans('pages.club_email_verified_already');

          }else{

            $user->email_token = NULL;
            $user->is_email_verify = 1;
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();

            $data['status'] = 1;

            if($user->association_type_term == config('custom.association_type_term.member')){

                $data['message'] = trans('pages.member_email_verified_success');

            }else{

                $data['message'] = trans('pages.club_email_verified_success');

            }

          }

        }

        return view('pages.common.email-verification', compact('data','user'));

    } catch (\Throwable $th) {

        Log::info('email_verify '. print_r($th->getMessage(), true));

        return view('pages.common.email-verification', compact('data'));

    }

  }

  public function logout(Request $request)
  {
    $this->guard()->logout();

    $request->session()->invalidate();

    return $this->loggedOut($request) ?: redirect('/login');
  }

  public function thank_you()
  {

    return view('auth.thank_you');
  }
}
