<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Log;

class CommonController extends Controller
{
    public function crm_slug($crm_slug)
    {
        try {

            $where = '';
            if ($crm_slug == config('custom.crm_slug.about_us')) {
                $where = "about_us";
            } elseif ($crm_slug == config('custom.crm_slug.privacy_policy')) {
                $where = "privacy_policy";
            } elseif ($crm_slug == config('custom.crm_slug.terms_and_conditions')) {
                $where = "terms_and_conditions";
            } elseif ($crm_slug == config('custom.crm_slug.delete_account')) {
                $where = "delete_account";
            }

            if ($where) {
                $setting = AppSetting::first($where);
                return view('pages.setup.configuration.app_setting.app_url', compact('setting', 'crm_slug'));
            }

        } catch (Exception $exception) {

            Log::info("crm_slug_error " . print_r($exception->getMessage(), true));

            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }
    }

    public function stripe_connect_account_onbording_callback()
    {
        return view('pages.common.stripe-onboarding-thankyou');
    }

}
