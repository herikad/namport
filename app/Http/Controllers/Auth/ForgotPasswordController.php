<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Jobs\SendEmailRegisterUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Repositories\MailRepository;
use Log;
use Validator;

class ForgotPasswordController extends Controller
{

    public function forgot_password(Request $request){

        try {

        return view('auth.forgot_password');

        } catch (\Throwable $th) {

        Log::info('forgot_password '. print_r($th->getMessage(), true));

        return redirect()->route('dashbaord')->with('error', 'Something went wrong!');
        }

    }

    public function check_email(Request $request){

        try {

            $email_token = Str::random(155);

            $user = User::where('user_name',$request->user_name)
                        ->where('is_active',1)
                        ->first();

            if($user){

                $user->email_token = $email_token;
                $user->save();

                if($user->email_token){

                    $email_data['email_token'] = $email_token;
                    $email_data['user_name'] = $user ? $user->user_name : '';

                    MailRepository::replace_email_template(config("custom.mail_send_types.RESET_PASSWORD"),$email_data ,array($user->user_name) ) ;

                    return redirect()->route('login')->with( "message" , "Password reset link has been sent to your mail.");

                }
                return redirect()->route('login')->with('message', 'Please check your email and update password!');

            }else{

                return redirect()->route('forgot_password')->with('error', 'Please enter valid email!');

            }

        } catch (\Throwable $th) {

            Log::info('check_email_error '. print_r($th->getMessage(), true));

            return redirect()->route('login')->with('error', 'Please enter valid email!');
        }
    }

    public function password_reset($email_token){
        try{
            $user = User::where('email_token',$email_token)
                            ->where('is_active', 1)
                            ->first();

            if($user){

                return view('email_templates.reset_pass',compact('user'));
            }
            else {

                return redirect()->route('forgot_password')->with('error', 'User data not found!');

            }

        } catch (\Throwable $th) {

            Log::info('password_reset_error '. print_r($th->getMessage(), true));

            return redirect()->route('login')->with('error', 'User data not found!');
        }
    }

    public function process_password_reset(Request $request){

        try {

                $validation_rules = [

                    'new_password' => 'required',

                ];

                $validator = Validator::make($request->all(), $validation_rules);

                if($validator->fails()) {

                    return redirect()->route('forgot_password')->with('message', implode(',', $validator->messages()->all()) );

                }
                else
                {
                    $user = User::where('email_token', $request->token)->where('is_active',1)->first();

                    if ($user) {

                        User::where('user_id', $user->user_id)
                            ->where('is_active', 1)
                            ->update([
                                'password' => Hash::make($request->new_password),
                                'email_token' => null
                            ]);

                        return redirect()->route('thank_you')->with("message" ,"Password created successfully!");

                    } else {
                        return redirect()->route('thank_you')->with("message" ,"Token Invalid!");
                    }
                }
        } catch (\Exception $e) {

            Log::info('process_password_reset_error '. print_r($th->getMessage(), true));

            return redirect()->route('login')->with('message','Something went wrong!');
        }

    }
}
