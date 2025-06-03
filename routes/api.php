<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\Club\ClubController;
use App\Http\Controllers\App\TermController;
use App\Http\Controllers\App\MemberController;
use App\Http\Controllers\App\Club\PackageController;
use App\Http\Controllers\App\Club\ClubSubscriptionController;
use App\Http\Controllers\App\GroupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1', 'namespace' => 'App'], function () {

    // Web Hook
    Route::post("/stripe/webhook","WebhookController@stripe_webhook_response");
    Route::post("/stripe/connect/webhook","WebhookController@stripe_connect_webhook_response");

    Route::post('/stripe/connect/webhook/callback', 'CommonController@stripe_webhook_callback');

    Route::post('/get_terms', [TermController::class, 'get_terms']);

    Route::post('/get_sign_up_steps', [TermController::class, 'sign_up_step_terms']);

    Route::post('/get_country_list', 'CommonController@get_country_list');

    Route::post('/user_signup', 'AuthenticationController@user_signup');

    Route::post('/send_otp_without_auth', 'AuthenticationController@send_otp_without_auth');

    Route::post('/verify_otp', 'AuthenticationController@verify_otp');

    Route::post('/user_login', 'AuthenticationController@user_login');

    Route::post('/forgot_password', 'AuthenticationController@forgot_password');

    Route::post('/reset_password', 'AuthenticationController@reset_password');

    Route::post('app_setting', 'CommonController@get_app_setting');

    Route::post('/get_global_clubs', 'CommonController@get_global_clubs');
    Route::post('/get_global_club_details', 'CommonController@get_global_club_details');

    // calendar
    Route::post('/get_schedule_dates_by_year', 'CommonController@get_schedule_dates_by_year');
    Route::post('/get_schedules_by_date', 'CommonController@get_schedules_by_date');

    // Verify user

    // Route::post('/user_verify/{token}', 'AuthenticationController@user_verify');
    Route::post('/testSubscDemo', [PackageController::class, 'buy_subscription_demo']);

    Route::middleware(['auth:sanctum'])->group(function () {

        // Change password

        Route::post('/change_password', 'AuthenticationController@change_password');
        Route::post('/update_device_token', 'AuthenticationController@update_device_token');
        Route::post('/logout', 'AuthenticationController@logout');

        Route::post('/save_user_profile_steps', [MemberController::class, 'save_user_profile_steps']);

        Route::post('/get_user_profile', 'UserController@get_user_profile');

        Route::post('/update_user_profile', 'UserController@update_user_profile');

        Route::post('/get_notifications', 'UserController@get_notifications');
        Route::post('/mark_as_read_notifications', 'UserController@mark_as_read_notifications');

        Route::post('/save_address', 'UserController@save_address');

        Route::post('/send_otp_with_auth', 'AuthenticationController@send_otp_with_auth');

        Route::post('/delete_account', 'MemberController@delete_account');

        // Wallet
        Route::post('/my_wallet', 'WalletController@my_wallet');
        Route::post('/wallet_details', 'WalletController@wallet_details');
        Route::post('/add_money_to_wallet', 'WalletController@add_money_to_wallet');

        // This is for stripe
        Route::post('/get_ephemeral_key', 'UserController@get_ephemeral_key');

        // session
        Route::post('/session/create', 'CommonController@create_stripe_checkout_session');

        Route::group(['prefix' => 'club'], function () {

            Route::post('/list', [ClubController::class, 'get_club_list']);
            Route::post('/details', [ClubController::class, 'get_club_details']);
            Route::post('/add_favourite', 'Club\ClubController@add_favourite');
            Route::post('/get_booking_dates', [ClubController::class, 'get_booking_dates']);
            Route::post('/get_slots_by_date', [ClubController::class, 'get_slots_by_date']);
            Route::post('/get_courts_by_date', [ClubController::class, 'get_courts_by_date']);
            Route::post('/save_club_details', [ClubController::class, 'save_club_details']);
            Route::post('/get_count_by_date', [ClubController::class, 'get_count_by_date']);
            Route::post('/get_dropin_participants', 'OrderController@get_dropin_participants');
            Route::post('/notify_dropin_booking', 'OrderController@notify_dropin_booking');

            // Package
            Route::post('/package/list', 'Club\PackageController@get_package_list');

            // subscription
            Route::post('/subscription/details', 'Club\ClubSubscriptionController@subscription_details');
            Route::post('/subscription/cancel', 'Club\ClubSubscriptionController@cancel_club_subscription');

        });

        // club level subscription buyed members
        Route::post('/subscription_purchased_members', 'Club\ClubSubscriptionController@get_players');

        // Route::post('/update_member_timezone', 'CommonController@update_member_timezone');

        // Search member
        Route::post('/search_member', 'MemberController@search_member');
        Route::post('/global_search', 'CommonController@global_search');

        // Member profile
        Route::post('/get_member_profile', 'MemberController@get_member_profile');

        // Follow, Unfollow
        Route::post('/follow_unfollow_member', 'MemberController@follow_unfollow_member');
        // My friends
        Route::post('/member/connections', 'MemberController@my_connections');
        Route::post('/member/get_followers_members', 'MemberController@get_followers_members');
        Route::post('/member/get_following_members', 'MemberController@get_following_members');
        Route::post('/member/remove_followers', 'MemberController@remove_followers');
        Route::post('/member/block', 'MemberController@block_member');
        Route::post('/member/unblock', 'MemberController@unblock_member');
        Route::post('/member/block_list', 'MemberController@get_member_block_list');
        Route::post('/member/update_location', 'MemberController@update_member_location');

        // All count
        Route::post('/get_recent_updates', 'CommonController@get_recent_updates');

        // suggested people
        Route::post('/suggested_people', 'CommonController@suggested_people');
        Route::post('/recent_bookings', 'CommonController@recent_bookings');

        // Save Activity

        Route::group(['prefix' => 'activity'], function () {

          Route::post('/save', 'ActivityController@save_activity');
          Route::post('/list', 'ActivityController@get_activity_list');

        });
        // Post
        Route::group(['prefix' => 'post'], function () {

          Route::post('/create', 'PostController@create_post');
          Route::post('/get_dashboard_feeds', 'PostController@get_dashboard_feeds');
          Route::post('/like_dislike_post', 'PostController@like_dislike_post');
          Route::post('/post_comment', 'PostController@post_comment');
          Route::post('/get_comments', 'PostController@get_comments');
          Route::post('/list', 'PostController@get_member_posts');
          Route::post('/add_interest', 'PostController@add_interest');
          Route::post('/get_liked_users', 'PostController@get_liked_users');
          Route::post('/delete', 'PostController@delete_post');
          Route::post('/delete_comment', 'PostController@delete_comment');
          Route::post('/interested_users', 'PostController@interested_user_list');
          Route::post('/remove_interest', 'PostController@remove_interest');
          Route::post('/report', 'PostController@report_post');
        });

        // Cart
        Route::group(['prefix' => 'cart'], function () {

          Route::post('/add', 'CartController@add_to_cart');
          Route::post('/remove', 'CartController@remove_cart');
          Route::post('/list', 'CartController@cart_listing');
          Route::post('/get_order_confirmation_details', 'CartController@get_order_confirmation_details');

        });

        //Order
        Route::group(['prefix' => 'order'], function () {

          Route::post('/book', 'OrderController@book_order');
          Route::post('/list', 'OrderController@order_list');
          Route::post('/booking_details', 'OrderController@booking_details');
          Route::post('/cancel', 'OrderController@cancel_order');
          Route::post('/get_payment_client_secret', 'OrderController@get_payment_client_secret');
          Route::post('/get_order_status', 'OrderController@get_order_status');
          Route::post('/players/save', 'OrderController@save_booking_players');
          Route::post('/players/save_limit', 'OrderController@save_players_limit');
          Route::post('/players/accept_reject_invitation', 'OrderController@accept_reject_invitation');
          Route::post('/players/leave_invitation', 'OrderController@leave_invitation');
          Route::post('/player/remove', 'OrderController@remove_booking_player');
          // allow access to add member for group
          Route::post('/players/allow_access', 'OrderController@booking_player_access');
          Route::post('/booking_connections', 'OrderController@get_booking_connections');
          Route::post('/upload_score', 'OrderController@upload_score');

        });

        // Ratings
        Route::post('/save_rating', 'RatingController@save_rating');
        Route::post('/get_ratings', 'RatingController@get_ratings');

        // Trainer
        Route::post('/get_trainer_by_time', 'TrainerController@get_trainer_by_time');
        Route::post('/get_club_by_time', [ClubController::class, 'get_club_by_time']);

        Route::group(['prefix' => 'trainer'], function () {

          Route::post('/get_availability_slots', 'TrainerController@get_availability_slots');
          Route::post('/save_availability', 'TrainerController@save_availability');
          Route::post('/schedule_dates', 'TrainerController@schedule_dates');
          Route::post('/slots_by_date', 'TrainerController@slots_by_date');
          Route::post('/delete_slots_by_date', 'TrainerController@delete_slots_by_date');
          Route::post('/slot/delete', 'TrainerController@delete_slot');
          Route::post('/list', 'TrainerController@trainer_list');
          Route::post('/my_appointments', 'TrainerController@my_appointments');
          Route::post('/get_booking_dates',  'TrainerController@get_booking_dates');
          Route::post('/get_slots_by_date', 'TrainerController@get_slots_by_date');
          Route::post('/my_classes', 'TrainerController@my_classes');
          Route::post('/trainer_details', 'TrainerController@trainer_details');
          Route::post('/my_schedule', 'TrainerController@my_schedule');
          Route::post('/get_payment_onboarding_link', 'TrainerController@get_payment_onboarding_link');

        });

        Route::group(['prefix' => 'settings'], function () {

          Route::post('/matches_scores', 'CommonController@matches_scores');

        });

        Route::group(['prefix' => 'subscription'], function () {


          Route::post('/list', 'SubscriptionController@get_subscription_list');
          Route::post('/buy_subscription', 'SubscriptionController@buy_subscription');
          Route::post('/details', 'SubscriptionController@subscription_details');
          Route::post('/cancel', 'SubscriptionController@cancel_stripe_subscription');

        });

        // Group
        Route::group(['prefix' => 'group'], function () {
          Route::post('/add_update', 'GroupController@add_update_group');
          Route::post('/get_group_detail', 'GroupController@get_group_detail');
          Route::post('/get_group_members', 'GroupController@get_group_members');
          Route::post('/list', 'GroupController@list_of_groups');
          Route::post('/delete', 'GroupController@delete_group');
          Route::post('/accept_reject_invitation', 'GroupController@accept_reject_invitation');
          Route::post('/leave', 'GroupController@leave_group');
        });

    });

});
