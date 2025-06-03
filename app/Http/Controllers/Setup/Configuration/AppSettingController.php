<?php

namespace App\Http\Controllers\Setup\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Exception;
use Log;
use DB;
use Validator;
use Helper;
use Carbon\Carbon;

class AppSettingController extends Controller
{
  public function app_settings_index()
  {
      try{

           $app_settings = AppSetting::first();

           $discount_types = config('custom.discount_type');

           return view('pages.setup.configuration.app_setting.form', compact('app_settings','discount_types'));

      }
      catch(Exception $exception){
        return redirect()->route('dashboard')->with('error',  trans('pages.something_wrong') );
    }
  }


  public function saveAppSettings(Request $request)
    {

      try{

        $auth_user = auth()->user();

        if(!empty($request->app_settings_id)){

            $app_settings = AppSetting::find($request->app_settings_id);

            if($app_settings){

                $app_settings->android_minimum_version = $request->android_minimum_version;
                $app_settings->android_maximum_version = $request->android_maximum_version;
                $app_settings->ios_minimum_version = $request->ios_minimum_version;
                $app_settings->ios_maximum_version = $request->ios_maximum_version;
                $app_settings->is_undermaintenance = $request->is_undermaintenance;
                $app_settings->is_force_update_ios = $request->is_force_update_ios;
                $app_settings->is_force_update_android = $request->is_force_update_android;

                if($request->order_cancellation_hours != NULL){
                    $oc_seconds = $request->order_cancellation_hours * 60 * 60;
                    $app_settings->order_cancellation_hours = $oc_seconds;
                }

                $app_settings->payout_days = $request->payout_days;
                $app_settings->discount_type = $request->discount_type;
                $app_settings->discount_value = $request->discount_value;

                if($request->cart_timer_limit != NULL){
                    $ct_seconds = $request->cart_timer_limit * 60;
                    $app_settings->cart_timer_limit = $ct_seconds;
                }

                $app_settings->terms_and_conditions = htmlspecialchars_decode($request->terms_and_conditions);
                $app_settings->privacy_policy = htmlspecialchars_decode($request->privacy_policy);
                $app_settings->about_us = htmlspecialchars_decode($request->about_us);
                $app_settings->delete_account = htmlspecialchars_decode($request->delete_account);
                $app_settings->privacy_policy_url = $request->privacy_policy_url;
                $app_settings->about_us_url = $request->about_us_url;
                $app_settings->delete_account_url = $request->delete_account_url;
                $app_settings->term_and_conditions_url = $request->term_and_conditions_url;
                $app_settings->updated_by = $auth_user->user_id;
                $app_settings->update();

            }

        }
        else{

          $app_settings=new AppSetting();
          $app_settings->android_minimum_version = $request->android_minimum_version;
          $app_settings->android_maximum_version = $request->android_maximum_version;
          $app_settings->ios_minimum_version = $request->ios_minimum_version;
          $app_settings->ios_maximum_version = $request->ios_maximum_version;
          $app_settings->is_undermaintenance = $request->is_undermaintenance;
          $app_settings->is_force_update_ios = $request->is_force_update_ios;
          $app_settings->is_force_update_android = $request->is_force_update_android;
          $app_settings->order_cancellation_hours = $request->order_cancellation_hours;
          $app_settings->terms_and_conditions = htmlspecialchars_decode($request->terms_and_conditions);
          $app_settings->privacy_policy = htmlspecialchars_decode($request->privacy_policy);
          $app_settings->about_us = htmlspecialchars_decode($request->about_us);
          $app_settings->delete_account = htmlspecialchars_decode($request->delete_account);
          $app_settings->privacy_policy_url = $request->privacy_policy_url;
          $app_settings->about_us_url = $request->about_us_url;
          $app_settings->delete_account_url = $request->delete_account_url;
          $app_settings->term_and_conditions_url = $request->term_and_conditions_url;
          $app_settings->created_by = $auth_user->user_id;
          $app_settings->save();

        }

        if($app_settings){
          return response()->json( [ 'status' => 1, 'message' =>!empty($request->app_settings_id) ? 'App setting updated successfully!' : 'App setting created successfully!', 'redirect_url' => route('setup.config.app_setting.app')], 200 );
        }

    } catch(Exception $e){

      Log::info('saveAppSettings ' . print_r($e->getMessage(), true));
      return response()->json(['status' => 0, 'message' =>'something went wrong']);
    }
    }
}
