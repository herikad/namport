<?php

namespace App\Helpers;

use App\Models\AppSetting;
use App\Models\BookingSlot;
use App\Models\Club;
use App\Models\ClubUserSubscription;
use App\Models\Notification;
use App\Models\NotificationUsers;
use App\Models\PackagePrice;
use App\Models\PersonalAccessToken;
use App\Models\SocComment;
use App\Models\SocPost;
use App\Models\Subscription;
use App\Models\Term;
use App\Models\TermCategory;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UsrRights;
use App\Models\UsrRoleright;
use App\Models\UsrUserrole;
use App\Repositories\CommonRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Config;
use DateInterval;
use DateTime;
use DateTimeZone;
use DB;
use File;
use Illuminate\Support\Facades\Mail;
use Log;
use Storage;

class Helpers
{
    public static function appClasses()
    {

        $data = config('custom.custom');

        // default data array
        $DefaultData = [
            'myLayout' => 'vertical',
            'myTheme' => 'theme-default',
            'myStyle' => 'light',
            'myRTLSupport' => true,
            'myRTLMode' => true,
            'hasCustomizer' => true,
            'showDropdownOnHover' => true,
            'displayCustomizer' => true,
            'menuFixed' => true,
            'menuCollapsed' => false,
            'navbarFixed' => true,
            'footerFixed' => false,
            'customizerControls' => [
                'rtl',
                'style',
                'layoutType',
                'showDropdownOnHover',
                'layoutNavbarFixed',
                'layoutFooterFixed',
                'themes',
            ],
            //   'defaultLanguage'=>'en',
        ];

        // if any key missing of array from custom.php file it will be merge and set a default value from dataDefault array and store in data variable
        $data = array_merge($DefaultData, $data);

        // All options available in the template
        $allOptions = [
            'myLayout' => ['vertical', 'horizontal', 'blank'],
            'menuCollapsed' => [true, false],
            'hasCustomizer' => [true, false],
            'showDropdownOnHover' => [true, false],
            'displayCustomizer' => [true, false],
            'myStyle' => ['light', 'dark'],
            'myTheme' => ['theme-default', 'theme-bordered', 'theme-semi-dark'],
            'myRTLSupport' => [true, false],
            'myRTLMode' => [true, false],
            'menuFixed' => [true, false],
            'navbarFixed' => [true, false],
            'footerFixed' => [true, false],
            'customizerControls' => [],
            // 'defaultLanguage'=>array('en'=>'en','fr'=>'fr','de'=>'de','pt'=>'pt'),
        ];

        //if myLayout value empty or not match with default options in custom.php config file then set a default value
        foreach ($allOptions as $key => $value) {
            if (array_key_exists($key, $DefaultData)) {
                if (gettype($DefaultData[$key]) == gettype($data[$key])) {
                    // data key should be string
                    if (is_string($data[$key])) {
                        // data key should not be empty
                        if (isset($data[$key]) && $data[$key] !== null) {
                            // data key should not be exist inside allOptions array's sub array
                            if (!array_key_exists($data[$key], $value)) {
                                // ensure that passed value should be match with any of allOptions array value
                                $result = array_search($data[$key], $value, 'strict');
                                if (empty($result) && $result !== 0) {
                                    $data[$key] = $DefaultData[$key];
                                }
                            }
                        } else {
                            // if data key not set or
                            $data[$key] = $DefaultData[$key];
                        }
                    }
                } else {
                    $data[$key] = $DefaultData[$key];
                }
            }
        }
        //layout classes
        $layoutClasses = [
            'layout' => $data['myLayout'],
            'theme' => $data['myTheme'],
            'style' => $data['myStyle'],
            'rtlSupport' => $data['myRTLSupport'],
            'rtlMode' => $data['myRTLMode'],
            'textDirection' => $data['myRTLMode'],
            'menuCollapsed' => $data['menuCollapsed'],
            'hasCustomizer' => $data['hasCustomizer'],
            'showDropdownOnHover' => $data['showDropdownOnHover'],
            'displayCustomizer' => $data['displayCustomizer'],
            'menuFixed' => $data['menuFixed'],
            'navbarFixed' => $data['navbarFixed'],
            'footerFixed' => $data['footerFixed'],
            'customizerControls' => $data['customizerControls'],
        ];

        // sidebar Collapsed
        if ($layoutClasses['menuCollapsed'] == true) {
            $layoutClasses['menuCollapsed'] = 'layout-menu-collapsed';
        }

        // Menu Fixed
        if ($layoutClasses['menuFixed'] == true) {
            $layoutClasses['menuFixed'] = 'layout-menu-fixed';
        }

        // Navbar Fixed
        if ($layoutClasses['navbarFixed'] == true) {
            $layoutClasses['navbarFixed'] = 'layout-navbar-fixed';
        }

        // Footer Fixed
        if ($layoutClasses['footerFixed'] == true) {
            $layoutClasses['footerFixed'] = 'layout-footer-fixed';
        }

        // RTL Supported template
        if ($layoutClasses['rtlSupport'] == true) {
            $layoutClasses['rtlSupport'] = '/rtl';
        }

        // RTL Layout/Mode
        if ($layoutClasses['rtlMode'] == true) {
            $layoutClasses['rtlMode'] = 'rtl';
            $layoutClasses['textDirection'] = 'rtl';
        } else {
            $layoutClasses['rtlMode'] = 'ltr';
            $layoutClasses['textDirection'] = 'ltr';
        }

        // Show DropdownOnHover for Horizontal Menu
        if ($layoutClasses['showDropdownOnHover'] == true) {
            $layoutClasses['showDropdownOnHover'] = 'true';
        } else {
            $layoutClasses['showDropdownOnHover'] = 'false';
        }

        // To hide/show display customizer UI, not js
        if ($layoutClasses['displayCustomizer'] == true) {
            $layoutClasses['displayCustomizer'] = 'true';
        } else {
            $layoutClasses['displayCustomizer'] = 'false';
        }

        return $layoutClasses;
    }

    public static function updatePageConfig($pageConfigs)
    {
        $demo = 'custom';
        if (isset($pageConfigs)) {
            if (count($pageConfigs) > 0) {
                foreach ($pageConfigs as $config => $val) {
                    Config::set('custom.' . $demo . '.' . $config, $val);
                }
            }
        }
    }

    public static function send_dynamic_mail($mail_id, $view_body, $email, $subject, $files = [], $images_tag_url = [])
    {
        try {

            Log::info("email yes helper ");

            $mail_layout = view('email_templates.common_mail_template')->render();
            $view = str_replace('[CONTENT_REPLACE]', $view_body, $mail_layout);
            // em1
            dispatch(new \App\Jobs\SendDynamicEmail($mail_id, $view, $email, $subject, $files, $images_tag_url));

        } catch (\Exception $e) {

            Log::info("send_dynamic_mail" . $e->getMessage());

        }

    }

    // send mail

    // public static function send_mail($view, $email = [], $subject, $data, $files = []){
    public static function send_mail($view, $subject, $data, $email = [], $files = [])
    {

        try {

            Log::info("email yes ");

            if (is_array($email) && count($email) > 0) {

                foreach ($email as $key => $value) {

                    Log::info("email value " . print_r($email, true));

                    try {

                        if (!empty($value)) {

                            Log::info("email in mail send");

                            Mail::send($view, ["data" => $data], function ($message) use ($value, $subject, $files) {
                                $message->to($value)
                                    ->subject($subject);

                                if (count($files) > 0) {
                                    foreach ($files as $file) {
                                        $message->attach($file);
                                    }
                                }

                            });
                        }

                        Log::info("email done ");

                    } catch (\Throwable $th) {
                        Log::info("email error " . $th->getMessage());
                    }

                }
            } else {
                Log::info("email array data is empty");
            }

        } catch (Exception $exception) {
            Log::info("error send_mail " . $exception->getMessage());
        }

    }

    public static function user_rights_by_modual()
    {
        $response_array = [['status' => 0, 'message' => trans('pages.something_wrong')]];

        try {
            $user_details = auth()->user();

            // first get all user role ids.
            $role_ids = UsrUserrole::select('role_id')
                ->where('is_active', 1)
                ->where('user_id', $user_details->user_id)
                ->get()
                ->toArray();

            //  that role ids have all rights get
            $all_right_ids = UsrRoleright::select('usr_right.right_id')
                ->from('pb_usr_roleright as usr_right')
                ->where('usr_right.is_active', 1)
                ->where('usr_right.is_view', 1)
                ->whereIn('usr_right.role_id', $role_ids)
                ->get()
                ->toArray();

            // after get only main menu like set-up, dashboard
            $modules = UsrRights::select(
                'mst_right.module_id',
                'mst_module.module_name as right_name',
                'mst_module.sidebar_url',
                'mst_module.sidebar_name',
                'mst_module.sidebar_slug',
                'mst_module.sidebar_class',
                'mst_right.is_dashboard'
            )
                ->from('pb_usr_rights as mst_right')
                ->where('mst_right.is_active', 1)

                ->join('pb_usr_roleright', function ($q) use ($all_right_ids) {
                    $q->on('pb_usr_roleright.right_id', '=', 'mst_right.right_id')->whereIn(
                        'pb_usr_roleright.right_id',
                        $all_right_ids
                    );
                })

                ->join('pb_usr_module as mst_module', function ($q) {
                    $q->on('mst_module.module_id', '=', 'mst_right.module_id')->where('mst_module.is_active', 1);
                })
                ->orderBy('mst_module.order_no', 'asc')
                ->groupBy('mst_right.module_id')
                ->get()
                ->toArray();

            if (count($modules)) {
                // running for loop because i get menu and that menu's right
                foreach ($modules as $key => $value) {
                    $menu = [];

                    // check rights becasue some menu are in sub menu so.
                    $menu_right_ids = UsrRoleright::select(
                        DB::raw(
                            '(CASE WHEN mst_right.parent_form_id = 0 THEN mst_right.right_id ELSE mst_right.parent_form_id END) AS right_id'
                        )
                    )
                        ->from('pb_usr_roleright')
                        ->where('pb_usr_roleright.is_active', 1)
                        ->whereIn('pb_usr_roleright.role_id', $role_ids)
                        ->join('pb_usr_rights as mst_right', function ($q) use ($value) {
                            $q->on('mst_right.right_id', '=', 'pb_usr_roleright.right_id')
                                ->where('mst_right.module_id', $value['module_id'])
                                ->where('mst_right.is_active', 1);
                        })
                        ->get()
                        ->toArray();

                    // get all menus
                    $menu = UsrRights::select(
                        'mst_right.right_id',
                        'mst_right.form_name as right_name',
                        'mst_right.form_details',
                        'mst_right.parent_form_id',
                        'mst_right.sidebar_url',
                        'mst_right.sidebar_name',
                        'mst_right.sidebar_slug',
                        'mst_right.sidebar_class',
                        'mst_right.is_dashboard'
                    )
                        ->from('pb_usr_rights as mst_right')
                        ->where('mst_right.is_active', 1)
                        ->whereIn('mst_right.right_id', $menu_right_ids)
                        ->groupBy('mst_right.right_id')
                        ->orderBy('mst_right.order_no', 'asc')
                        ->get()
                        ->toArray();

                    if (count($menu)) {
                        // again running for loop because that menu wise rights get
                        foreach ($menu as $key1 => $value1) {
                            $menu[$key1]['sub_menu'] = UsrRights::select(
                                'mst_right.right_id',
                                'mst_right.form_name as right_name',
                                'mst_right.form_details',
                                'mst_right.parent_form_id',
                                'mst_right.sidebar_url',
                                'mst_right.sidebar_name',
                                'mst_right.sidebar_slug',
                                'mst_right.sidebar_class',
                                'mst_right.is_dashboard'
                            )
                                ->from('pb_usr_rights as mst_right')
                                ->where('mst_right.is_active', 1)
                                ->where('mst_right.parent_form_id', $value1['right_id'])
                                ->join('pb_usr_roleright as usr_right', function ($q) use ($all_right_ids) {
                                    $q->on('usr_right.right_id', '=', 'mst_right.right_id')->whereIn(
                                        'usr_right.right_id',
                                        $all_right_ids
                                    );
                                })
                                ->groupBy('mst_right.right_id')
                                ->get()
                                ->toArray();
                        }
                    }
                    $modules[$key]['menu'] = $menu;
                }
            }

            return $modules;
        } catch (\Throwable $e) {
            Log::info('get_user_rights error ' . $e->getMessage());
            return $response_array['error'] = $e->getMessage();
        }
    }

    public static function upload_file($file = "", $file_name = "", $file_name_to_store = "", $file_uploaded_path = "", $is_base64_file = false)
    {
        $file_path = false;

        try {

            $default_storage = config('filesystems.default');

            $default_storage_driver = config('filesystems.disks.' . $default_storage . '.driver');

            if ($is_base64_file == false) {

                $file_name = $file->getClientOriginalName();

            }

            if ($is_base64_file == true) {

                $file_parts = explode(";base64,", $file);
                $base64_file_content = base64_decode($file_parts[1]);
            }

            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            $base_name = basename($file_name, "." . $file_extension);

            $file_name_to_store = $file_name_to_store . '.' . $file_extension;

            if ($default_storage == 'public' || $default_storage == 'local') {

                if (!File::isDirectory($file_uploaded_path)) {
                    File::makeDirectory($file_uploaded_path, 0777, true, true);
                }
            }

            $cloudResponse = false;

            if ($is_base64_file == false) {

                $cloudResponse = Storage::disk($default_storage_driver)->put($file_uploaded_path . '/' . $file_name_to_store, file_get_contents($file->getRealPath()), 'public');

            } else {

                $cloudResponse = Storage::disk($default_storage_driver)->put($file_uploaded_path . '/' . $file_name_to_store, $base64_file_content, 'public');

            }

            $file_path = $file_uploaded_path . '/' . $file_name_to_store;

            if ($cloudResponse) {

                if ($default_storage_driver != 'public') {

                    $cloudFileUrl = Storage::url($file_path);

                    $file_path = $cloudFileUrl;

                }

            }

            Log::info("file_path success " . $file_path);

            return $file_path;

        } catch (\Throwable $th) {
            Log::info("upload_file error " . print_r($th->getMessage(), true));
            return $file_path;
        }

    }

    public static function deleteFile($img)
    {

        try {

            if (isset($img) && !empty($img)) {

                $default_storage = config('filesystems.default');

                $default_storage_driver = config('filesystems.disks.' . $default_storage . '.driver');

                if (Storage::disk($default_storage)->exists($img)) {

                    Storage::disk($default_storage)->delete($img);
                    // File deleted successfully
                }

            }
            // if ($default_storage == 's3') {

            //     Storage::disk('s3')->delete($img);

            // } else {

            //     if (!empty($img) && File::exists($img)) {
            //         File::delete($img);
            //     }
            // }

        } catch (\Exception $e) {

            Log::info("deleteFile_image_error " . print_r($e->getMessage(), true));

        }
    }

    public static function get_all_country_data()
    {
        try {

            $country_data = DB::table('pb_countries')
                ->select('*')
                ->where('is_active', 1)
                ->get();

            return $country_data;

        } catch (\Exception $exception) {

            Log::info("get_all_country_data_error " . print_r($exception->getMessage(), true));

            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function get_city_list_by_name($request)
    {

        try {
            $cities = DB::table('pb_all_cities')
                ->select('*')
                ->where('city_name', 'like', '%' . $request->city . '%')
                ->get();

            return $cities;

        } catch (Exception $exception) {

            Log::info("get_city_list_by_name_error " . print_r($exception->getMessage(), true));

            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function get_state_list_of_city($city_id)
    {

        try {
            $states = DB::table('pb_states')
                ->select('id', 'name', 'country_id', 'gst_state_code')
                ->where('country_id', function ($query) use ($city_id) {
                    $query->select('in_state.country_id')
                        ->from('pb_all_cities as in_city')
                        ->leftJoin('pb_states as in_state', 'in_state.id', '=', 'in_city.state_code')
                        ->where('in_city.id', $city_id);
                })
                ->get();
            // return response()->json(['data' => $states], 200);
            return $states;
        } catch (Exception $exception) {

            Log::info("get_state_list_of_city_error " . print_r($exception->getMessage(), true));

            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function get_all_gst_state_code_data()
    {
        try {
            $gst_state_code_data = DB::table('pb_states')
                ->select('id', 'gst_state_code')
                ->whereNotNull('gst_state_code')
                ->get();

            if (!empty($gst_state_code_data)) {
                return $gst_state_code_data;
            }

        } catch (Exception $exception) {

            Log::info("get_all_gst_state_code_data_error " . print_r($exception->getMessage(), true));

            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function get_all_terms_by_category($term_category)
    {
        try {
            $term_list = Term::from('pb_term as term')
                ->select('term.term_id', 'term.term_name', 'term.term_code', 'term.term_details', 'term.term_details as label', 'term.term_name as value', 'category.category_name', 'category.term_category_id')
                ->leftJoin('pb_term_category as category', 'category.term_category_id', '=', 'term.term_category_id')
                ->where(['category.category_name' => $term_category, 'term.is_active' => 1])
                ->orderBy('term.sequence_no')
                ->get();

            return $term_list;
        } catch (Exception $exception) {

            Log::info("get_all_terms_by_category_error " . print_r($exception->getMessage(), true));

            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function get_term_name($id)
    {
        try {
            $term_name = Term::where('term_id', $id)->first('term_name');
            return isset($term_name) ? $term_name->term_name : null;
        } catch (Exception $exception) {

            Log::info("get_term_name_error" . print_r($exception->getMessage(), true));

            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function save_notification($obj)
    {
        try {
            $notification = new Notification();
            $notification->notification_type = isset($obj['type']) ? $obj['type'] : '';
            $notification->notification_title = isset($obj['title']) ? $obj['title'] : '';
            $notification->notification_description = isset($obj['description']) ? $obj['description'] : '';
            $notification->save();

            $user_association_ids = [];

            if ($notification) {
                if (isset($obj['users']) && is_array($obj['users'])) {

                    foreach ($obj['users'] as $key => $value) {
                        $notify = new NotificationUsers();
                        $notify->notification_id = $notification->notification_id;
                        $notify->association_id = isset($value['association_id']) ? $value['association_id'] : null;
                        $notify->association_type_term = isset($value['association_type_term']) ? $value['association_type_term'] : null;
                        $notify->email = isset($value['email']) ? $value['email'] : null;
                        $notify->phone = isset($value['phone']) ? $value['phone'] : null;
                        $notify->data = isset($obj['data']) ? json_encode($obj['data']) : null;
                        $notify->screen_id = isset($obj['screen_id']) ? $obj['screen_id'] : 0;
                        $notify->screen_type = isset($obj['screen_type']) ? $obj['screen_type'] : null;
                        $notify->booking_invitation_status = isset($obj['booking_invitation_status']) ? $obj['booking_invitation_status'] : null;
                        $notify->group_invitation_status = isset($obj['group_invitation_status']) ? $obj['group_invitation_status'] : null;
                        $notify->save();

                        if (isset($value['association_id'])) {
                            $user_association_ids[] = $value['association_id'];
                        }
                    }

                    Log::info('save_notification success');
                }
            }

            if (count($user_association_ids) > 0) {

                Log::info("user_association_ids " . print_r($user_association_ids, true));

                $tokens = self::getDeviceToken($user_association_ids);

                Log::info("tokens " . print_r($tokens, true));

                if (count($tokens) > 0) {

                    foreach ($tokens as $key => $device_token) {

                        Log::info("getDeviceToken tokens " . print_r($tokens, true));

                        $dataPayload = [
                            'token' => $device_token,
                            'event_name' => "send_message_to_token",
                            'title' => $notification->notification_title,
                            'body' => $notification->notification_description,
                            'data' => [
                                'screen_id' => isset($obj['screen_id']) ? (string) $obj['screen_id'] : null,
                                'screen_type' => isset($obj['screen_type']) ? $obj['screen_type'] : null,
                                'screen_data' => isset($obj['data']) ? json_encode($obj['data']) : null,
                            ],
                        ];
                        Log::info("getDeviceToken dataPayload " . print_r(isset($obj['data']) ? json_encode($obj['data']) : null, true));
                        // em1
                        CommonRepository::notify_to_devices($dataPayload);
                        // SendNotificationToDevice::dispatch($dataPayload);

                    }

                }

            }

        } catch (\Throwable $e) {
            Log::info('save_notification error ' . print_r($e->getMessage(), true));
        }
    }

    public static function getCurrentTimezone()
    {
        try {
            $current_timezone = new DateTimeZone(date_default_timezone_get());
            return $current_timezone->getName();
        } catch (\Throwable $e) {
            Log::info('error getCurrentTimezone ' . print_r($e->getMessage(), true));
        }

    }

    public static function update_user_details($association_id, $association_type_term, $update_array)
    {

        try {

            User::where('association_id', $association_id)
                ->where('association_type_term', $association_type_term)
                ->update($update_array);

        } catch (\Exception $e) {
            Log::info("update_user_account_status error " . $e->getMessage());
        }

    }

    public static function get_term_category($category_name)
    {
        try {

            $term_detail = TermCategory::where('category_name', $category_name)->first('category_details');
            return isset($term_detail) ? $term_detail->category_details : null;
        } catch (Exception $exception) {

            Log::info("get_term_category_error" . print_r($exception->getMessage(), true));

            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function get_all_terms_title($term_category)
    {
        $term_list = [];

        try {

            $term_list = Term::from('pb_term as term')
                ->select('term.term_id', 'term.term_name as value', 'term.ideal_value', 'term.term_code')
                ->join('pb_term_category as category', 'category.term_category_id', '=', 'term.term_category_id')
                ->where(['category.category_name' => $term_category, 'term.is_active' => 1])
                ->orderBy('term.sequence_no')
                ->get();

            return $term_list;

        } catch (\Exception $e) {

            Log::info("get_all_terms_title error " . print_r($e->getMessage(), true));

            return $term_list;

        }

    }

    public static function pageAction($page)
    {

        $user_details = auth()->user();

        $roles = UsrUserrole::where('user_id', $user_details->user_id)->get(['role_id'])->toArray();

        $currnt_route_right_id = UsrRights::where('form_name', $page)
            ->where('user_type_term', $user_details->user_type_term)
            ->first('right_id');

        if ($currnt_route_right_id) {

            $roles_right_id = UsrRoleright::from('pb_usr_roleright as urr')
                ->whereIn('urr.role_id', $roles)
                ->leftjoin('pb_usr_rights as ur', 'urr.right_id', '=', 'ur.right_id')
                ->where('urr.right_id', $currnt_route_right_id->right_id)
                ->select('ur.form_name', 'urr.right_id', 'urr.is_view', 'urr.is_create', 'urr.is_update', 'urr.is_delete', 'urr.is_export', 'urr.is_execute')
                ->first();
            // dd($roles_right_id);
            return $roles_right_id;
        } else {

        }
    }

    // Get authorize user

    public static function get_authorize_user()
    {

        $user = auth('sanctum')->user();

        // Log::info("get_authorize_user == ". print_r($user, true));

        return $user;

    }

    public static function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s')
    {
        $new_str = new DateTime($str, new DateTimeZone($userTimezone));
        $new_str->setTimeZone(new DateTimeZone('UTC'));
        return $new_str->format($format);
    }

    public static function convertUTCToAnotherZone($str, $userTimezone, $format = 'Y-m-d H:i:s')
    {
        $new_str = new DateTime($str, new DateTimeZone('UTC'));
        $new_str->setTimeZone(new DateTimeZone($userTimezone));
        return $new_str->format($format);
    }

    public static function full_address($club_id = "")
    {
        $club = Club::select(DB::raw("CONCAT(address, ',', city, ',', state, ',', country, '-', zipcode) AS full_address"))
            ->where('club_id', $club_id)
            ->first();

        return $club->full_address;
    }

    public static function copy_upload_file($file = "", $file_name = "", $file_name_to_store = "", $file_uploaded_path = "", $is_base64_file = false)
    {
        $file_path = false;
        try {
            $default_storage = config('filesystems.default');

            $default_storage_driver = config('filesystems.disks.' . $default_storage . '.driver');

            if ($is_base64_file == false) {

                $file_name = $file;

            }

            if ($is_base64_file == true) {

                $file_parts = explode(";base64,", $file);
                $base64_file_content = base64_decode($file_parts[1]);
            }

            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            $base_name = basename($file_name, "." . $file_extension);

            if ($default_storage == 'public' || $default_storage == 'local') {

                if (!File::isDirectory($file_uploaded_path)) {
                    File::makeDirectory($file_uploaded_path, 0777, true, true);
                }
            }

            if ($default_storage == 's3') {

                if ($is_base64_file == false) {
                    Storage::disk($default_storage_driver)->put(env('AWS_BUCKET_FOLDER') . $file_uploaded_path . '/' . $file_name_to_store, file_get_contents($file), 'public');
                } else {
                    Storage::disk($default_storage_driver)->put(env('AWS_BUCKET_FOLDER') . $file_uploaded_path . '/' . $file_name_to_store, $base64_file_content, 'public');
                }
            } else {

                if ($is_base64_file == false) {

                    Storage::disk($default_storage_driver)->put($file_uploaded_path . '/' . $file_name_to_store, file_get_contents($file), 'public');

                } else {

                    Storage::disk($default_storage_driver)->put($file_uploaded_path . '/' . $file_name_to_store, $base64_file_content, 'public');
                }
            }

            $file_path = $file_uploaded_path . '/' . $file_name_to_store;

            Log::info("file_path success " . $file_path);

            return $file_path;
        } catch (\Throwable $th) {
            Log::info("upload_file error " . print_r($th->getMessage(), true));
            return $file_path;
        }

    }
    public static function getTimezoneFromOffsetSlot($offset)
    {

        $timezoneList = timezone_identifiers_list();

        foreach ($timezoneList as $timezone) {
            $dateTime = new DateTime('now', new DateTimeZone($timezone));
            $currentOffset = $dateTime->format('P');

            if ($currentOffset === $offset) {
                return $timezone;
            }
        }

        return 'UTC'; // No matching timezone found
    }

    public static function getTimezoneFromOffset($offsetHours)
    {
        $offsetSeconds = $offsetHours * 3600; // Convert hours to seconds
        $matchingTimezones = array_filter(timezone_identifiers_list(), fn($timezone) => (new DateTimeZone($timezone))->getOffset(new DateTime()) === $offsetSeconds);
        return $matchingTimezones ? reset($matchingTimezones) : 'UTC';
    }

    public static function getTimezoneFromAbbreviation($abbreviation)
    {

        $result = 'UTC';

        try {
            // Get the timezone name from the abbreviation
            $timezoneName = timezone_name_from_abbr($abbreviation);

            if ($timezoneName === false) {
                return $result;
            }

            return $timezoneName;
        } catch (\Exception $e) {
            Log::info('getTimezoneFromAbbreviation error' . print_r($e->getMessage(), true));
            return $result;
        }
    }

    public static function convertIntToDecimal($value)
    {

        if (is_numeric($value)) {
            // return str_replace(",", "", number_format($value, 2));
            return (float) str_replace(",", "", number_format($value, 2));
        } else {
            return (float) str_replace(",", "", number_format(0, 2));
        }

    }

    public static function convertIntToFloat($value)
    {

        if (is_numeric($value)) {
            return (float) str_replace(",", "", number_format($value, 2));
        } else {
            return (float) str_replace(",", "", number_format(0, 2));
        }

    }

    public static function convertIntToInt($value)
    {

        if (is_numeric($value)) {
            return (int) str_replace(",", "", number_format($value, 2));
        } else {
            return (int) str_replace(",", "", number_format(0, 2));
        }

    }

    public static function showOrderStatusBadge($status)
    {

        $badge = '';
        if ($status == config('custom.order_item_status.booked')) {
            $badge = '<span class="align-middle badge rounded-pill bg-success">' . $status . '</span>';
        } else if ($status == config('custom.order_item_status.completed')) {
            $badge = '<span class="align-middle badge rounded-pill bg-success">' . $status . '</span>';
        } else if ($status == config('custom.order_item_status.cancelled')) {
            $badge = '<span class="align-middle badge rounded-pill bg-danger">' . $status . '</span>';
        } else if ($status == config('custom.order_item_status.confirmed')) {
            $badge = '<span class="align-middle badge rounded-pill bg-info">' . $status . '</span>';
        } else if ($status == config('custom.order_item_status.pending')) {
            $badge = '<span class="align-middle badge rounded-pill bg-warning">' . $status . '</span>';
        } else if ($status == config('custom.action_status.pending')) {
            $badge = '<span class="align-middle badge rounded-pill bg-warning">' . $status . '</span>';
        } else if ($status == config('custom.action_status.accepted')) {
            $badge = '<span class="align-middle badge rounded-pill bg-success">' . $status . '</span>';
        } else if ($status == config('custom.approval_status.reject')) {
            $badge = '<span class="badge rounded-pill bg-label-danger">' . $status . '</span>';
        } else if ($status == config('custom.approval_status.accept')) {
            $badge = '<span class="badge rounded-pill bg-label-success">Approved</span>';
        } else if ($status == config('custom.action_status.rejected')) {
            $badge = '<span class="align-middle badge rounded-pill bg-danger">' . $status . '</span>';
        } else if ($status == config('custom.action_status.waiting')) {
            $badge = '<span class="align-middle badge rounded-pill bg-secondry">' . $status . '</span>';
        } else if ($status == config('custom.action_status.leave')) {
            $badge = '<span class="align-middle badge rounded-pill bg-info p-1">' . $status . '</span>';
        } else if ($status == config('custom.payout_status_term.in_progress')) {
            $badge = '<span class="align-middle badge rounded-pill bg-warning p-1">In Progress</span>';
        } else if ($status == config('custom.payout_status_term.succeeded')) {
            $badge = '<span class="align-middle badge rounded-pill bg-success p-1">' . $status . '</span>';
        } else if ($status == config('custom.payout_status_term.failed')) {
            $badge = '<span class="align-middle badge rounded-pill bg-danger p-1">' . $status . '</span>';
        } else if ($status == config('custom.stripe.payout_status.canceled')) {
            $badge = '<span class="align-middle badge rounded-pill bg-danger p-1">' . $status . '</span>';
        } else if ($status == config('custom.stripe.payout_status.paid')) {
            $badge = '<span class="align-middle badge rounded-pill bg-success p-1">' . $status . '</span>';
        } else if ($status == config('custom.stripe.payout_status.in_transit')) {
            $badge = '<span class="align-middle badge rounded-pill bg-warning p-1">In Transit</span>';
        } else {
            $badge = $status;
        }

        return $badge;
    }

    public static function getCurrentTimeInTimezone($timezone)
    {
        $currentTime = new DateTime('now', new DateTimeZone($timezone));
        $formattedTime = $currentTime->format('Y-m-d H:i:s');
        return $formattedTime;
    }

    public static function convertUTCDateTimeToTZ($start_time, $end_time, $timezone)
    {
        $result = ['startDateTime' => '', 'endDateTime' => ''];

        try {

            $timezone = $timezone ? $timezone : 'UTC';

            $startDateTime = new \DateTime($start_time, new \DateTimeZone('UTC'));
            $startDateTime->setTimezone(new \DateTimeZone($timezone));

            $endDateTime = new \DateTime($end_time, new \DateTimeZone('UTC'));
            $endDateTime->setTimezone(new \DateTimeZone($timezone));

            $result = ['startDateTime' => $startDateTime, 'endDateTime' => $endDateTime];

            return $result;

        } catch (\Throwable $th) {

            Log::info('Helper convertUTCDateTimeToTZ error ' . print_r($th->getMessage(), true));
            return $result;

        }
    }

    public static function get_app_setting()
    {
        try {

            $app_setting = AppSetting::first();
            return $app_setting;

        } catch (\Exception $e) {

            Log::info('get_app_setting error ' . print_r($e->getMessage(), true));
            return null;

        }

    }

    public static function convertSecondsToHours($seconds)
    {
        try {

            if ($seconds > 0) {
                return $seconds / 3600; // 1 hour = 3600 seconds
            }

        } catch (\Exception $e) {

            Log::info('convertSecondsToHours error ' . print_r($e->getMessage(), true));
            return 0;

        }

    }

    public static function convertSecondsToMinutes($seconds)
    {
        try {

            if ($seconds > 0) {
                return $seconds / 60; // 1 minute = 60 seconds
            }

        } catch (\Exception $e) {

            Log::info('convertSecondsToHours error ' . print_r($e->getMessage(), true));
            return 0;

        }

    }

    // created by herika
    public static function convertUTCDateTimeToTimezone($s_date, $timezone)
    {
        $datetime = null;

        try {

            if (!empty($timezone)) {
                $datetime = new \DateTime($s_date, new \DateTimeZone('UTC'));
                $datetime->setTimezone(new \DateTimeZone($timezone));
            }

            return $datetime;

        } catch (\Throwable $th) {

            Log::info('convertUTCDateTimeToTimezone error ' . print_r($th->getMessage(), true));
            return $datetime;

        }
    }

    public static function convertLocalDateTimeToUTC($s_date, $timezone)
    {
        $datetime = null;

        try {

            if (!empty($timezone)) {
                $datetime = new \DateTime($s_date, new \DateTimeZone($timezone));
                $datetime->setTimezone(new \DateTimeZone('UTC'));
            }

            return $datetime->format('Y-m-d H:i:s');

        } catch (\Throwable $th) {

            Log::info('convertLocalDateTimeToUTC error ' . print_r($th->getMessage(), true));
            return $datetime;

        }
    }
}
