<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

use App\Http\Controllers\Setup\Configuration\AppSettingController;
use App\Http\Controllers\Setup\Configuration\EmailConfigurationController;
use App\Http\Controllers\Setup\Configuration\EmailTemplateController;
use App\Http\Controllers\Setup\Configuration\GeneralSettingController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\UserManagement\RoleController;
use App\Repositories\MailRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientOnboarding\ClientOnboardingController;
use App\Http\Controllers\ClientOnboarding\ClientContactController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('/run-schedules', function(){
//   \Artisan::call('insert:trainer-time-slots');
//   return 'Done';
// });

// =================================================  Cron Run Using URL =====================//
Route::get('/booking-complete', function () {
    \Artisan::call('update-booking-status-completed');
    return 'Done';
});

Route::get('/run-cron/{cron_name}', function ($cron_name) {
    \Artisan::call($cron_name);
    return 'Done';
});

// =================================================  Cron Run Using URL END =====================//

// Route::get('/booking_payout', function(){

//     $month_start_date = date("Y-m-d", strtotime("first day of previous month"));      // get utc date of month start date
//     $month_end_date =  date("Y-m-d", strtotime("last day of previous month"));

//     dd($month_end_date);

// });

Route::get('/payout-view/{id}', function ($id) {

    // SendPayoutStatement::dispatch($id);
    // return;

});

Route::get('template', function () {

    // $email_data['user_name'] = 'Poonam Shah';
    // MailRepository::replace_email_template(config("custom.mail_send_types.MEMBER_WELCOME_MAIL"), $email_data, array('poonam_shah@yopmail.com'));

    $email_data['email_token'] = '';
    $email_data['user_name'] = 'Poonam Shah';

    $view = MailRepository::replace_email_template(config("custom.mail_send_types.RESEND_VERIFICATION_MAIL"), $email_data, array('payal.tripathi@softqubes.com'));
    // return $view;

    return view('email_templates.common_mail_template', compact('view'));
});

Route::get('/client/onboarding/{profile_link?}', [ClientOnboardingController::class, 'client_onboarding_page'])->name('client-onboarding-page');
Route::POST('/store/onboarding', [ClientOnboardingController::class, 'save_client_onboarding_page'])->name('store-client-onboarding-page');

Route::get('/crm/{crm_slug}', [CommonController::class, 'crm_slug'])->name('crm_slug');

Route::post('/city_list', function (Request $request) {
    return response()->json(['data' => Helper::get_city_list_by_name($request)], 200);
});

Route::post('city/state/{id}', function ($id) {
    return response()->json(['data' => Helper::get_state_list_of_city($id)], 200);
});

Route::get('/onbording/callback', [CommonController::class, 'stripe_connect_account_onbording_callback'])->name('stripe.account.onbording');

Auth::routes(['verify' => true]);

Route::get('/thank_you', [LoginController::class, 'thank_you'])->name('thank_you');

Route::get('/login/{claim_club_id?}', [LoginController::class, 'show_login_form'])->name('login');

Route::get('/club/signup/{claim_club_id?}', [LoginController::class, 'show_club_signup'])->name('club_signup');

Route::post('/club_signup', [LoginController::class, 'club_signup'])->name('post_club_signup');

Route::get('/email/verify/{token}', [LoginController::class, 'email_verify'])->name('email_verify');

Route::post('/basic_login', [LoginController::class, 'basic_login'])->name('basic_login');

Route::get('/forgot-password', [ForgotPasswordController::class, 'forgot_password'])->name('forgot_password');
Route::post('/check_email', [ForgotPasswordController::class, 'check_email'])->name('check_email');
Route::get('/password_reset/{token}', [ForgotPasswordController::class, 'password_reset'])->name('password_reset');
Route::post('/process_password_reset/{token}', [ForgotPasswordController::class, 'process_password_reset'])->name('process_password_reset');

Route::post('get_payment_onboarding_link', [ClubController::class, 'get_payment_onboarding_link'])->name('get_payment_onboarding_link');

Route::group(['middleware' => 'auth'], function () {

    Route::get('/logout', [LoginController::class, 'logout'])->name('user_logout');

    Route::get('/changepassword', [UserController::class, 'change_password'])->name('change_password');
    Route::post('/update_change_password', [UserController::class, 'update_change_password'])->name('update_change_password');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/get_analytics_chart_details', [DashboardController::class, 'get_analytics_chart_details'])->name('get_analytics_chart_details');
    Route::post('/get_analytics_order_chart_details', [DashboardController::class, 'get_analytics_order_chart_details'])->name('get_analytics_order_chart_details');
    Route::post('/get_assigned_club', [DashboardController::class, 'get_assigned_club'])->name('get_assigned_club');

    Route::get('my_profile', [UserController::class, 'my_profile'])->name('profile.show');
    Route::post('my_profile_update', [UserController::class, 'update_profile'])->name('profile.update');

    //Setup Routes
    Route::group(['prefix' => 'setup', 'as' => 'setup.'], function () {

        //Configuration Routes
        Route::group(['prefix' => 'configuration', 'as' => 'config.'], function () {

            // general settings
            Route::group(['prefix' => 'general-settings', 'as' => 'general-settings.'], function () {
                Route::get('/', [GeneralSettingController::class, 'index'])->name('index');
                Route::post('store', [GeneralSettingController::class, 'store'])->name('store');
                Route::post('delete/{id}', [GeneralSettingController::class, 'delete']);

            });

            //appsetting
            Route::group(['prefix' => 'app_setting', 'as' => 'app_setting.'], function () {

                Route::get('/', [AppSettingController::class, 'app_settings_index'])->name('app');

                Route::post('/app/save', [AppSettingController::class, 'saveAppSettings'])->name('app.save');
            });

            // Email Configuration Routes
            Route::group(['prefix' => 'email-configuration', 'as' => 'email_config.'], function () {
                Route::get('/index', [EmailConfigurationController::class, 'mail_setting_list_view'])->name('index');
                Route::post('/mail_setting_list_json', [EmailConfigurationController::class, 'mail_setting_list_json'])->name('mail_setting_list_json');
                Route::get('/create', [EmailConfigurationController::class, 'mail_create'])->name('create');
                Route::get('/edit/{id}', [EmailConfigurationController::class, 'mail_edit'])->name('edit');
                Route::post('/store', [EmailConfigurationController::class, 'store_or_update'])->name('store');
                Route::post('/status/update', [EmailConfigurationController::class, 'mail_status_update'])->name('status_update');
            });

            //Email Template Routes

            Route::group(['prefix' => 'email-template', 'as' => 'email_temp.'], function () {
                Route::get('/index', [EmailTemplateController::class, 'mail_template_setting_list_view'])->name('index');
                Route::post('/mail_template_setting_list_json', [EmailTemplateController::class, 'mail_template_setting_list_json'])->name('mail_template_setting_list_json');
                Route::get('/create', [EmailTemplateController::class, 'mail_template_create'])->name('create');
                Route::get('/edit/{id}', [EmailTemplateController::class, 'mail_template_edit'])->name('edit');
                Route::post('/store', [EmailTemplateController::class, 'store_or_update'])->name('store');
                Route::post('/status/update', [EmailTemplateController::class, 'mail_template_status_update'])->name('status_update');
            });
        });

        //User Management Routes
        Route::group(['prefix' => 'user-management', 'as' => 'user_management.'], function () {
            //Role Routes
            Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::get('create/{id?}', [RoleController::class, 'create'])->name('create');
                Route::POST('get_module_right_list', [RoleController::class, 'get_module_right_list'])->name('get_module_right_list');
                Route::POST('store', [RoleController::class, 'store'])->name('store');
                Route::POST('get_role_list_json', [RoleController::class, 'get_role_list_json'])->name('get_role_list_json');
                Route::post('/status/update', [RoleController::class, 'role_status_update'])->name('role_status_update');
            });
            //User Routes
            Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
                Route::get('roles_by_type_term', [UserController::class, 'roles_by_type_term'])->name('roles_by_type_term');
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('create/{id?}', [UserController::class, 'create'])->name('create');
                Route::post('store', [UserController::class, 'store'])->name('store');
                Route::post('user_json_list', [UserController::class, 'user_json_list'])->name('user_json_list');
                Route::post('/status/update', [UserController::class, 'user_status_update'])->name('user_status_update');
            });

        });

        // department

        Route::group(['prefix' => 'department', 'as' => 'department.'], function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('index');
            Route::post('department_json_list', [DepartmentController::class, 'department_json_list'])->name('department_json_list');
            Route::get('create/{id?}', [DepartmentController::class, 'create'])->name('create');
            Route::post('store', [DepartmentController::class, 'store'])->name('store');
            Route::post('/status/update', [DepartmentController::class, 'status_update'])->name('status_update');
        });

        // DesignationController
        Route::group(['prefix' => 'designation', 'as' => 'designation.'], function () {
            Route::get('/', [DesignationController::class, 'index'])->name('index');
            Route::post('designation_json_list', [DesignationController::class, 'designation_json_list'])->name('designation_json_list');
            Route::get('create/{id?}', [DesignationController::class, 'create'])->name('create');
            Route::post('store', [DesignationController::class, 'store'])->name('store');
            Route::post('/status/update', [DesignationController::class, 'status_update'])->name('status_update');
        });


    });

    Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
        Route::get('/list', [NotificationController::class, 'get_all_notification'])->name('all');
        Route::post('/get_unread_all_notifications', [NotificationController::class, 'get_unread_all_notifications'])->name(
            'get_unread_all_notifications'
        );
        Route::post('/mark_as_read_all_notifications', [
            NotificationController::class,
            'mark_as_read_all_notifications',
        ])->name('mark_as_read_all_notifications');
        Route::post('/selected_notification_read', [NotificationController::class, 'selected_notification_read'])->name(
            'selected_notification_read'
        );
        Route::post('/get_all_notifications_json', [NotificationController::class, 'get_all_notifications_json'])->name(
            'get_all_notifications_json'
        );
    });

    Route::group(['prefix' => 'client_contacts', 'as' => 'client_contacts.'], function () {
        Route::get('/', [ClientContactController::class, 'index'])->name('index');
        Route::get('/create', [ClientContactController::class, 'create'])->name('create');
        Route::post('/contact_list_json', [ClientContactController::class, 'contact_list_json'])->name('contact_list_json');
        Route::post('/store', [ClientContactController::class, 'store'])->name('store');
        Route::post('/active_status_update', [ClientContactController::class, 'active_status_update'])->name('active_status_update');
    });

});
