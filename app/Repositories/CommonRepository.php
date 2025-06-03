<?php

namespace App\Repositories;

use App\Models\BookingSlot;
use App\Models\Club;
use App\Models\Court;
use App\Models\Favourite;
use App\Models\GroupMembers;
use App\Models\Interest;
use App\Models\InterestedUser;
use App\Models\Member;
use App\Models\MemberConnections;
use App\Models\Notification;
use App\Models\SocPost;
use App\Models\SocPostImage;
use App\Models\Term;
use App\Models\TraActivity;
use App\Models\TraActivityDtls;
use App\Models\TraCart;
use App\Models\TraCartItem;
use App\Models\TraOrdersDetails;
use App\Models\User;
use App\Models\TraBookingPlayer;
use App\Models\TraRating;
use App\Models\TimeZone;
use App\Models\TraMemberUpdates;
use App\Models\TermCategory;
use App\Models\AppSetting;
use Twilio\Rest\Client;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;

class CommonRepository
{

    public static function get_term()
    {

        try {
            $attribute = Term::from('term as t')
                ->where('tc.category_name', 'attribute')
                ->where('t.is_active', 1)
                ->select('t.term_id', 't.term_name', 't.term_details', 't.term_code')
                ->leftjoin('pb_term_category as tc', 'tc.term_category_id', '=', 't.term_category_id')
                ->get();

            if (!empty($attribute)) {
                return $attribute;
            }

        } catch (\Throwable $th) {
            Log::info("Commonrepository get_term " . print_r($th->getMessage(), true));
            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function check_user_exists($request, $update_user_id = '')
    {

        $result = false;
        $message = '';
        try {

            if (isset($request->email) && !empty($request->email)) {

                $is_email_exists = User::where([
                    'email' => $request->email,
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]);

                if (!empty($update_user_id)) {
                    $is_email_exists = $is_email_exists->where('user_id', '!=', $update_user_id);
                }

                $is_email_exists = $is_email_exists->first();

                if ($is_email_exists) {

                    $result = true;
                    $message = trans('pages.user_already_exists', ['attr' => 'email']);

                }

            }

            if (isset($request->phone) && !empty($request->phone)) {

                $is_phone_exists = User::where([
                    'country_code' => $request->country_code,
                    'phone' => $request->phone,
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]);

                if (!empty($update_user_id)) {
                    $is_phone_exists = $is_phone_exists->where('user_id', '!=', $update_user_id);
                }


                $is_phone_exists = $is_phone_exists->first();

                if ($is_phone_exists) {

                    $result = true;
                    $message = trans('pages.user_already_exists', ['attr' => 'phone']);
                }

            }

            if (isset($request->nickname) && !empty($request->nickname)) {

                $is_nickname_exists = User::where([
                    'nickname' => $request->nickname,
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]);

                if (!empty($update_user_id)) {
                    $is_nickname_exists = $is_nickname_exists->where('user_id', '!=', $update_user_id);
                }

                $is_nickname_exists = $is_nickname_exists->first();

                if ($is_nickname_exists) {

                    $result = true;
                    $message = trans('pages.user_already_exists', ['attr' => 'username']);

                }

            }

            return ['is_exists' => $result, 'message' => $message];

        } catch (\Throwable $th) {

            Log::info("error check_user_exists " . print_r($th->getMessage(), true));

            return ['is_exists' => $result, 'message' => trans('pages.something_wrong')];

        }

    }

    public static function get_user_by_username($request)
    {

        $user = false;

        try {

            if (isset($request->email) && !empty($request->email)) {

                $user = User::where([
                    'email' => $request->email,
                    'is_active' => 1,
                    'is_deleted' => 0,
                ])
                    ->first();

            }

            if (isset($request->phone) && !empty($request->phone)) {

                $user = User::where([
                    'phone' => $request->phone,
                    'is_active' => 1,
                    'is_deleted' => 0,
                ])
                    ->first();

            }

            return $user;

        } catch (\Throwable $th) {

            Log::info("error get_user_by_username " . print_r($th->getMessage(), true));

            return $user;

        }

    }

    public static function send_sms_by_twillo($message, $recipients)
    {

        try {

              // $account_sid = env("TWILIO_SID");
              // $twillo_auth_token = env("TWILIO_AUTH_TOKEN");
              // $twilio_number = env("TWILIO_NUMBER");
              // $client = new Client($account_sid, $twillo_auth_token);
              // $sms_result = $client->messages->create($recipients,['from' => $twilio_number, 'body' => $message] );

            return true;

        } catch (\Exception $e) {

            Log::info("send_sms_by_twillo error" . $e->getMessage());

            return false;

        }

    }

    public static function get_all_notifications($request)
    {

        $result = [
            'list' => [],
            'recordsTotal' => 0,
            'totalPages' => 0,
        ];

        try {

            $auth_user = \Helper::get_authorize_user();

            $limit = $request->has('limit') ? $request->limit : config('custom.default_records_limit');
            $page_index = $request->has('page') ? $request->page : 1;

            $list = Notification::select('not.notification_id', 'not.notification_type', 'not.notification_title', 'not.notification_description', 'nu.data', 'not.icon', 'not.created_at', 'nu.is_read', 'nu.notify_id','nu.screen_id','nu.screen_type','nu.booking_invitation_status','nu.group_invitation_status')
                ->from('pb_notifications as not')
                ->leftjoin('pb_notification_users as nu', 'nu.notification_id', '=', 'not.notification_id')
                ->where('nu.association_id', $auth_user->association_id)
                ->where('nu.association_type_term', $auth_user->association_type_term)
                ->where('not.is_active', 1)
                ->orderBy('not.notification_id', 'desc')
                ->paginate($limit, ['*'], 'page', $page_index);

            $result['list'] = $list->getCollection();

            if(count($result['list'])){

                foreach ($result['list'] as $key => $value) {
                    $result['list'][$key]['is_read'] = \Helper::convertIntToInt($value->is_read);
                }

            }

            return [
                'list' => $result['list'] ? $result['list'] : [],
                'recordsTotal' => (int) $list->total(),
                'totalPages' => $list->lastPage(),
            ];

            // $result['total'] = $list->total();

            // return $result;

        } catch (\Exception $e) {

            Log::info("error get_all_notifications " . print_r($e->getMessage(), true));
            return $e->getMessage();
        }

    }

    public static function mark_as_read_notifications(Request $request)
    {
        $result = false;

        try{

            $auth_user = \Helper::get_authorize_user();

            $main_query = Notification::from('pb_notifications as not')->where('not.is_active', 1)->where('nu.is_read', 0);

            if(isset($request->read_all) && $request->read_all == 1){

                $main_query = $main_query->leftJoin('pb_notification_users as nu', function($join) use($auth_user) {
                                          $join->on('nu.notification_id', '=', 'not.notification_id')
                                            ->where('nu.association_id', $auth_user->association_id ? $auth_user->association_id : $auth_user->user_id)
                                            ->where('nu.association_type_term', $auth_user->association_type_term);
                                        })
                                        ->update([
                                          'nu.is_read' => 1
                                        ]);

                $result = true;

            }else{

                if(isset($request->notify_ids) && is_array($request->notify_ids)){

                    $main_query = $main_query->leftJoin('pb_notification_users as nu', function($join) use($request) {
                                              $join->on('nu.notification_id', '=', 'not.notification_id')
                                                  ->whereIn('nu.notify_id', $request->notify_ids);
                                              })
                                              ->update([
                                                'nu.is_read' => 1
                                              ]);

                    $result = true;

                }

            }

            return $result;

        }catch(\Exception $e) {

            Log::info("mark_as_read_notifications error ". print_r($e->getMessage(), true));

            return $result;

        }

    }

    public static function add_to_favorite($request)
    {
        $result = 0;

        try {

            $auth_user = \Helper::get_authorize_user();

            if (!$auth_user) {

                return response()->json(['status' => 0, "message" => trans('auth.username_failed')], 200);

            }

            if ($auth_user) {

                if ($request->is_favourite) {

                    $check_exist = Favourite::where([
                        'module_id' => $request->module_id,
                        'module_type_term' => $request->module_type_term,
                        'association_id' => $auth_user->association_id,
                        'association_type_term' => $auth_user->association_type_term,
                        'is_active' => 1,
                    ])->first();

                    if (!$check_exist) {

                        $fav = new Favourite;
                        $fav->association_id = $auth_user->association_id;
                        $fav->association_type_term = $auth_user->association_type_term;
                        $fav->module_id = $request->module_id;
                        $fav->module_type_term = $request->module_type_term;
                        $fav->is_active = $request->is_favourite;
                        $fav->save();
                        $result = 1;

                    } else {
                        $result = 1;
                    }

                } else {

                    $fav = Favourite::where([
                        'association_id' => $auth_user->association_id,
                        'association_type_term' => $auth_user->association_type_term,
                        'module_id' => $request->module_id,
                        'module_type_term' => $request->module_type_term,
                    ])->delete();

                    $result = 1;
                }

                return $result;
            }

        } catch (\Exception $e) {
            Log::info("CommonRepository add_to_favorite" . print_r($e->getMessage(), true));
            return $result;
        }

    }

    public static function create_member_connection($request, $source_id, $requested_id, $follow, $connection_type)
    {
        $result = 0;

        try {

            $connection = MemberConnections::where([
                'first_member_id' => $source_id,
                'requested_member_id' => $requested_id,
                'is_active' => 1,
                'status_term' => config('custom.status_term.active'),
                'is_following' => 1,
            ]);

            $member = Member::where(['member_id' => $source_id, 'is_active' => 1])->first();

            if ($connection_type == 'receiver') {

                $total_followers = ($follow == 1) ? $member->total_followers += 1 : $member->total_followers -= 1;
                $total_connection = ($follow == 1) ? $member->total_connection += 1 : $member->total_connection -= 1;

            } else if ($connection_type == 'sender') {

                $total_followings = ($follow == 1) ? $member->total_followings += 1 : $member->total_followings -= 1;
                $total_connection = ($follow == 1) ? $member->total_connection += 1 : $member->total_connection -= 1;

            }

            if (!$connection->first() && $follow == 1) {

                $user_id = ($connection_type == 'receiver') ? $requested_id : $source_id;

                $connection_fir = new MemberConnections();
                $connection_fir->first_member_id = $source_id;
                $connection_fir->requested_member_id = $requested_id;
                $connection_fir->is_following = 1;
                $connection_fir->requested_on = date('Y-m-d H:i:s');
                $connection_fir->accepted_on = date('Y-m-d H:i:s');
                $connection_fir->status_term = config('custom.status_term.active');
                $connection_fir->created_by = $user_id;
                $connection_fir->save();

                //Update member Total Connection
                if ($member) {

                    if ($connection_type == 'receiver') {
                        $member->total_followers = $total_followers;
                    } else if ($connection_type == 'sender') {
                        $member->total_followings = $total_followings;
                    }
                    $member->save();
                    $member->total_connection = $total_connection;
                    $member->save();
                    $result = ['status' => 1, 'message' => 'Followed successfully.', 'data' => (object) []];

                }
            } else if ($follow == 1) {
                $result = ['status' => 0, 'message' => 'Already Followed.', 'data' => (object) []];
            } else {
                $connection->delete();
                if ($connection_type == 'receiver') {
                    $member->total_followers = $total_followers;
                } else if ($connection_type == 'sender') {
                    $member->total_followings = $total_followings;
                }
                $member->save();
                $member->total_connection = $total_connection;
                $member->save();
                $result = ['status' => 1, 'message' => 'Unfollowed successfully.', 'data' => (object) []];
            }

            return $result;

        } catch (\Exception $e) {

            Log::info("CommonRepository create_member_connection" . print_r($e->getMessage(), true));
            return $result;

        }
    }

    public static function save_activity($request)
    {
        $result = false;

        try {

            $auth_user = \Helper::get_authorize_user($request);

            if (!$auth_user) {
                return response()->json(['status' => 0, "message" => trans('auth.username_failed')], 200);
            }

            if (isset($request)) {
                if ($request->activity_type == config('custom.activity_type.club_status_activity')) {

                    $activity_type_term = config('custom.activity_type.club_status_activity');
                    $activity_type = config('custom.club_status_activity');

                } else if ($request->activity_type == config('custom.activity_type.add_people_activity')) {

                    $activity_type_term = config('custom.activity_type.add_people_activity');
                    $activity_type = config('custom.add_people_activity');

                } else if ($request->activity_type == config('custom.activity_type.add_interest_activity')) {

                    $activity_type_term = config('custom.activity_type.add_interest_activity');

                }

                $activity = new TraActivity();
                $activity->activity_type_term = $activity_type_term;
                $activity->association_id = $auth_user->association_id;
                $activity->association_type_term = config('custom.association_type_term.member');
                $activity->club_id = $request->club_id;
                $activity->save();

                if ($activity) {

                    if (isset($activity_type)) {
                        foreach ($activity_type as $key => $value) {
                            $act_dtls = new TraActivityDtls();
                            $act_dtls->activity_id = $activity->activity_id;
                            $act_dtls->field_name = $key;
                            $act_dtls->field_value = $request[$key];
                            $act_dtls->save();
                            $result = true;

                            if ($request->activity_type == config('custom.activity_type.add_people_activity')) {

                                $club = Club::where('club_id', $request->club_id)->update(['approx_people_count' => $request[$key]]);

                            }
                        }

                    }

                    if ($request->activity_type == config('custom.activity_type.add_interest_activity')) {

                        // Interest
                        $interest = new Interest();
                        $interest->club_id = $request->club_id;
                        $interest->visibility_type_term = config('custom.visibility_type_term.public');
                        $interest->association_id = $auth_user->association_id;
                        $interest->association_type_term = config('custom.association_type_term.member');
                        $interest->interest_datetime = $request->interest_datetime;
                        $interest->comment = $request->comment;
                        $interest->created_by = $auth_user->association_id;
                        $interest->save();

                        $result = true;

                        if ($interest) {

                            $activity->ref_association_id = $interest->interest_id;
                            $activity->ref_association_type_term = config('custom.db_table.pb_interests');
                            $activity->save();

                            $member_ids = $request->interested_members;

                            if (is_array($member_ids) && count($member_ids)) {

                                array_push($member_ids, $auth_user->association_id);

                                foreach ($member_ids as $key => $member_id) {
                                    if (!empty($member_id)) {

                                        $interested_user = new InterestedUser();
                                        $interested_user->interest_id = $interest->interest_id;
                                        $interested_user->created_by = $auth_user->association_id;
                                        $user = User::select('association_id', 'association_type_term')->where('association_id', $member_id)->first();
                                        $interested_user->association_id = $user ? $user->association_id : '';
                                        $interested_user->association_type_term = $user ? $user->association_type_term : '';
                                        $interested_user->save();
                                        $result = true;

                                        if($user->association_id != $auth_user->association_id){

                                            // send notification to selected players, you are invited
                                            $receiver_user = User::select('user_id', 'association_id', 'association_type_term','timezone_id')
                                                                  ->where('association_id', $user->association_id)
                                                                  ->where('association_type_term', $user->association_type_term)
                                                                  ->get();

                                            Log::info("save_activity receiver_user " . print_r($receiver_user, true));

                                            if(count($receiver_user)){

                                                $notification_obj = [
                                                    'type' => config('custom.notification_types.create_interest'),
                                                    'title' => trans('pages.notifications.create_interest_title'),
                                                    'description' => trans('pages.notifications.create_interest_desc'),
                                                    'data' => [
                                                        'interest_id' => $interest->interest_id,
                                                        'default_image' => asset('notification/default.png'),
                                                        'sender_association_id' => $auth_user->association_id,
                                                        'sender_username' => $auth_user->display_name,
                                                        'sender_association_type' => $auth_user->association_type_term,
                                                        'sender_profile_pic' => $auth_user->profile_pic,
                                                    ],
                                                    'screen_id' => "0",
                                                    'screen_type' => config('custom.notification.screen_type.notification_list'),
                                                    'users' => $receiver_user->toArray(),
                                                ];

                                                \Helper::save_notification($notification_obj);

                                            }

                                        }

                                    }
                                }

                            } else {

                                // as per kishan bug, added this
                                $interest_user = new InterestedUser();
                                $interest_user->interest_id = $interest->interest_id;
                                $interest_user->association_id = $auth_user->association_id;
                                $interest_user->association_type_term = $auth_user->association_type_term;
                                $interest_user->created_by = $auth_user->association_id;
                                $interest_user->save();
                                $result = true;

                            }

                        }

                        // Post
                        $soc_post = new SocPost();
                        $soc_post->post_type_term = config('custom.post_type_term.public');
                        $soc_post->type_of_post = config('custom.type_of_post.interest_activity');
                        $soc_post->association_id = $auth_user->association_id;
                        $soc_post->association_type_term = config('custom.association_type_term.member');
                        $soc_post->comment = $request->comment;
                        $soc_post->created_by = $auth_user->association_id;
                        $soc_post->last_action_on = date('Y-m-d H:i:s');
                        $soc_post->ref_interest_id = $interest ? $interest->interest_id : '';
                        $soc_post->ref_association_id = $interest ? $interest->interest_id : '';
                        $soc_post->ref_association_type_term = config('custom.db_table.pb_interests');
                        $soc_post->save();
                        $result = true;

                        if ($soc_post) {

                            // Update ref_post_id
                            $soc_post->ref_interest_id = $interest->interest_id;
                            $soc_post->save();

                            $activity->ref_post_id = $soc_post->post_id;
                            $activity->save();

                            $interest->ref_post_id = $soc_post->post_id;
                            $interest->save();

                            InterestedUser::where('interest_id', $interest->interest_id)->update([
                                'ref_post_id' => $soc_post->post_id,
                            ]);
                            // End update ref_post_id

                            // Post Image
                            $club = Club::select('profile_pic')->where('club_id', $request->club_id)->first();

                            if ($club->profile_pic) {
                                $soc_post_img = new SocPostImage();
                                $soc_post_img->post_id = $soc_post->post_id;
                                $soc_post_img->save();
                                $result = true;

                                $image = explode('/', $club->profile_pic);

                                // Current path of the profile picture
                                $currentImagePath = $club->profile_pic;

                                // Determine the new folder's path and filename
                                $newFolder = 'user/post';
                                $newFilename = 'POST_' . $soc_post_img->post_image_id . '_' . time() . '.' . pathinfo($image[2], PATHINFO_EXTENSION);
                                $newImagePath = \Helper::copy_upload_file($currentImagePath, $newFilename, $newFilename, $newFolder);

                                if (!empty($newImagePath)) {
                                    $soc_post_img->image_path = $newImagePath;
                                    $soc_post_img->file_name = $newFilename;
                                    $soc_post_img->image_type_term = File::mimeType($club->profile_pic);
                                    $soc_post_img->order_no = $soc_post_img->post_image_id;
                                    $soc_post_img->save();
                                    $result = true;

                                }

                            }
                        }

                    }

                }

                return $result;
            }

            return $result;

        } catch (\Exception $e) {

            Log::info("CommonRepository save_activity error" . $e->getMessage());
            return false;

        }
    }

    public static function my_connections($request)
    {
        try {

            $auth_user = \Helper::get_authorize_user();

            if (!$auth_user) {
                return response()->json(['status' => 0, "message" => trans('auth.username_failed')], 200);
            }

            $limit = $request->has('limit') ? $request->limit : config('custom.default_records_limit');

            $page_index = $request->has('page') ? $request->page : 1;

            $column_name = isset($request->order_by_name) ? $request->order_by_name : 'member_conn.member_connection_id';

            $column_sort_order = isset($request->order_by_type) ? $request->order_by_type : 'desc';

            $main_query = MemberConnections::from('pb_mst_member_connections as member_conn')
                                            ->select('member.member_name', 'member.profile_pic', 'member.member_id', 'member.member_type')
                                            ->join('pb_mst_members as member', function ($join) {
                                                $join->on('member_conn.requested_member_id', 'member.member_id')
                                                    ->where('member.is_active', 1)
                                                    ->where('member.is_deleted', 0);
                                            })
                                            ->where([
                                                'member_conn.first_member_id' => $auth_user->association_id,
                                                'member_conn.is_active' => 1,
                                                'member_conn.status_term' => config('custom.status_term.active'),
                                                'member_conn.is_following' => 1,
                                            ])->orderBy($column_name, $column_sort_order);

            if (isset($request->search) && !empty($request->search)) {

                $search = $request->search;

                $main_query = $main_query->where(function ($query) use ($search) {
                                            $query->where('member.member_name', 'LIKE', "%{$search}%");
                                          });

            }

            $data_list_for_count = $main_query->get();

            $recordsTotal = count($data_list_for_count);

            $data = $main_query->paginate($limit, ['*'], 'page', $page_index);

            $totalPages = $data->lastPage();

            $data = [
                'list' => $data->getCollection(),
                'recordsTotal' => (int) $recordsTotal,
                'totalPages' => $totalPages,
            ];
            return $data;

        } catch (\Exception $e) {

            Log::info('CommonRepository my_connections error' . print_r($e->getMessage(), true));

            $response = array(
                'status' => 0,
                'message' => trans('pages.something_wrong'),
                'error' => $e->getMessage(),
                "data" => (object) []
            );

            return response()->json($response);
        }
    }

    public static function get_post_list($request)
    {

        // 1. Jene group request accept kari hoy aene dashboard ma e group ni post avi joi
        // 2. Jene group request accept nathi kari ya to reject kari che to ene dashboard ma e group sivay ni public post aavi joi (jo e member ena friend list ma hoy)
        // 3. In dashboard New user ne all post public/group
        // 4. Dashboard => Social Groups => On click Groups => e group ni j post aavi joi
        // 5. X user bhale maro friend hoy but me group request accept nathi kari to e group ni post mara dashboard ma dekhavi na joi
        // 6. In dashboard feeds api all public/Group post dekhavi joi (if accept request + Friend)
        // 7. Dashboard ma friend ni public post tyare j avse, jo e member ne follow karto hoy
        // 8. Dashboard ma friend ni group post dekhavi joi, pachi bhale e member ne follow karto na hoy, but ene group request accept kari hoy

        try {

            $limit = $request->has('limit') ? $request->limit : config('custom.default_records_limit');

            $page_index = $request->has('page') ? $request->page : 1;

            $column_name = isset($request->order_by_name) ? $request->order_by_name : 'post.post_id';

            $column_sort_order = isset($request->order_by_type) ? $request->order_by_type : 'desc';


            if(isset($request->member_id))
            {

              $member_id = $request->member_id;

            }else{

              $member_id = $request->auth_user->association_id;

            }

            $group_posts = SocPost::from('pb_soc_posts as post')
                                ->select('post.post_id as post_id','post.post_type_term','post.type_of_post','interest.interest_id', 'member.member_id', 'member.member_name', 'member.profile_pic', 'post.comment', 'post.total_likes', 'post.total_comment', 'post.created_at','post.group_id','post.ref_association_id','post.ref_association_type_term',
                                      DB::raw('(SELECT COUNT(interest_user.interest_user_id) FROM pb_interested_users interest_user WHERE interest_user.ref_post_id = post.post_id AND interest_user.association_type_term = "' . config('custom.association_type_term.member') . '") AS total_interested_user'),
                                      DB::raw('(SELECT CASE WHEN COUNT(interest_user.interest_user_id) > 0 THEN 1 ELSE 0 END FROM pb_interested_users interest_user WHERE interest_user.association_id = "' . $member_id  . '" AND interest_user.ref_post_id = post.post_id AND interest_user.association_type_term = "' . config('custom.association_type_term.member') . '") AS is_interested'),
                                      DB::raw('(SELECT CASE WHEN COUNT(soc_likes.like_id) > 0 THEN 1 ELSE 0 END FROM pb_soc_likes soc_likes WHERE soc_likes.member_id = ' . $member_id . ' AND soc_likes.association_id = post.post_id AND soc_likes.association_type_term = "' . config('custom.association_type_term.post') . '") AS is_like')
                                  )
                                  // ->select('post.post_id as post_id','post.post_type_term','post.type_of_post','interest.interest_id', 'member.member_id', 'member.member_name', 'member.profile_pic', 'post.comment', 'post.total_likes', 'post.total_comment', 'post.created_at','post.group_id','post.ref_association_id','post.ref_association_type_term',
                                  //           DB::raw('0 as total_interested_user'),DB::raw('0 as is_interested'), DB::raw('0 as is_like'))
                                  ->join('pb_mst_members as member', function ($join) {
                                      $join->on('post.association_id', 'member.member_id')
                                          ->where('member.is_active', 1)
                                          ->where('member.is_deleted', 0);
                                  })
                                  ->leftJoin('pb_report_post as report_post', function ($join) use($member_id) {
                                      $join->on('post.post_id', '=', 'report_post.post_id')
                                          ->where('report_post.association_id', '=', $member_id)
                                          ->where('report_post.association_type_term', '=', config('custom.association_type_term.member'))
                                          ->where('report_post.is_active', '=', 1);
                                  })
                                  ->whereIn('post.group_id',function($query) use($member_id) {
                                      $query->select('gm.group_id')->from('pb_group_members as gm')
                                            ->where('gm.association_id', $member_id)
                                            ->where('gm.association_type_term', config('custom.association_type_term.member'))
                                            ->where('gm.action_status', config('custom.group_action_status.accepted'));
                                  })
                                  ->leftjoin('pb_interests as interest', 'interest.ref_post_id', '=', 'post.post_id')
                                  ->where('post.association_type_term', config('custom.association_type_term.member'))
                                  ->whereNull('report_post.report_post_id')
                                  ->where('post.is_active', 1)
                                  ->where('post.post_type_term', config('custom.post_type_term.group'));

            $main_query = SocPost::from('pb_soc_posts as post')
                                  ->select('post.post_id as post_id','post.post_type_term','post.type_of_post','interest.interest_id', 'member.member_id', 'member.member_name', 'member.profile_pic', 'post.comment', 'post.total_likes', 'post.total_comment', 'post.created_at','post.group_id','post.ref_association_id','post.ref_association_type_term',
                                      DB::raw('(SELECT COUNT(interest_user.interest_user_id) FROM pb_interested_users interest_user WHERE interest_user.ref_post_id = post.post_id AND interest_user.association_type_term = "' . config('custom.association_type_term.member') . '") AS total_interested_user'),
                                      DB::raw('(SELECT CASE WHEN COUNT(interest_user.interest_user_id) > 0 THEN 1 ELSE 0 END FROM pb_interested_users interest_user WHERE interest_user.association_id = "' . $member_id  . '" AND interest_user.ref_post_id = post.post_id AND interest_user.association_type_term = "' . config('custom.association_type_term.member') . '") AS is_interested'),
                                      DB::raw('(SELECT CASE WHEN COUNT(soc_likes.like_id) > 0 THEN 1 ELSE 0 END FROM pb_soc_likes soc_likes WHERE soc_likes.member_id = ' . $member_id . ' AND soc_likes.association_id = post.post_id AND soc_likes.association_type_term = "' . config('custom.association_type_term.post') . '") AS is_like')
                                  )
                                  ->join('pb_mst_members as member', function ($join) {
                                      $join->on('post.association_id', 'member.member_id')
                                          ->where('member.is_active', 1)
                                          ->where('member.is_deleted', 0);
                                  })
                                  ->leftJoin('pb_report_post as report_post', function ($join) use($member_id) {
                                      $join->on('post.post_id', '=', 'report_post.post_id')
                                          ->where('report_post.association_id', '=', $member_id)
                                          ->where('report_post.association_type_term', '=', config('custom.association_type_term.member'))
                                          ->where('report_post.is_active', '=', 1);
                                  })
                                  ->leftjoin('pb_interests as interest', 'interest.ref_post_id', '=', 'post.post_id')
                                  ->where('post.association_type_term', config('custom.association_type_term.member'))
                                  ->whereNull('report_post.report_post_id')
                                  ->where('post.is_active', 1);
                                  // ->orderBy($column_name, $column_sort_order);

            if($request->group_id){
                $main_query->where('post.group_id', $request->group_id);
            }else{

                if ($request->post_list_type == config('custom.post_list_type.all_user')) {

                  $my_connections = MemberConnections::from('pb_mst_member_connections as member_conn')
                      ->select('member_conn.requested_member_id', 'member.member_name', 'member.profile_pic')
                      ->join('pb_mst_members as member', function ($join) {
                          $join->on('member_conn.requested_member_id', 'member.member_id')
                              ->where('member.is_active', 1)
                              ->where('member.is_deleted', 0);
                      })
                      ->where([
                          'member_conn.first_member_id' => $member_id,
                          'member_conn.is_active' => 1,
                          'member_conn.status_term' => config('custom.status_term.active'),
                          'member_conn.is_following' => 1,
                      ])->get();

                  $my_friends = array_column($my_connections->toArray(), 'requested_member_id');

                  $auth_user_id = [$member_id];

                  $my_friends = array_merge($my_friends, $auth_user_id);

                  $main_query = $main_query->whereIn('post.association_id', $my_friends);

                  // if there is no post in dashboard than show all the member posts
                  if($main_query->get()->isEmpty()){

                      $main_query = self::get_all_member_posts($request, $member_id, config('custom.type_of_post.normal'),$column_name, $column_sort_order);
                  }else{

                    // if(!$request->group_id){
                      // Friends post = not including group post, only public post

                      // $main_query = $main_query->where('post.post_type_term','!=' ,config('custom.post_type_term.group'));
                    // }

                  }

                }else{

                    $main_query = $main_query->where('post.association_id', $member_id);

                }

                $main_query->union($group_posts);

            }

            $main_query->orderBy('post_id', 'desc');

            $data = $main_query->paginate($limit, ['*'], 'page', $page_index);

            $list = $data->getCollection();

            foreach ($list as $key => $value) {

                $value->media_files = SocPostImage::select('post_image_id','image_path','image_type_term')
                    ->where('post_id', $value->post_id)
                    ->where('is_active', 1)
                    ->whereNotNull('image_path')
                    ->get();

                if(isset($value->type_of_post) && $value->type_of_post == config('custom.type_of_post.upload_score') ){

                    $request->match_id = $value->ref_association_id;

                    $value->score_details = self::get_match_score_results($request);
                }

                if(isset($value->type_of_post) && $value->type_of_post == config('custom.type_of_post.interest_activity') ){

                    $interested_users = self::get_interest_details($value->ref_association_id);

                    $value->interested_users = [];

                    if($interested_users){
                        $value->interested_users = $interested_users->interedted_members;
                    }

                    $interest = Interest::where('interest_id', $value->ref_association_id)->first();

                    $value->club_details = NULL;

                    if($interest){
                        $value->club_details = self::get_club_detail($interest->club_id);
                    }
                }
            }

            $totalPages = $data->lastPage();

            $data = [
                'list' => $list,
                'recordsTotal' => (int) $data->total(),
                'totalPages' => $totalPages,
            ];

            return $data;

        } catch (\Exception $e) {

            Log::info('CommonRepository get_post_list error' . print_r($e->getMessage(), true));

            $response = array(
                'status' => 0,
                'message' => trans('pages.something_wrong'),
                'error' => $e->getMessage(),
                "data" => (object) []
            );

            return $response;

        }
    }

    public static function get_interest_details($interest_id)
    {
        try {
            $data = Interest::select('int.visibility_type_term', 'int.interest_datetime', 'member.member_name', 'member.profile_pic', 'int.comment', 'club.club_name', 'club.profile_pic as club_image')
                ->from('pb_interests as int')
                ->leftJoin('pb_interested_users as int_usr', 'int_usr.interest_id', '=', 'int.interest_id')
                ->Join('pb_mst_members as member', 'member.member_id', '=', 'int.association_id')
                ->leftJoin('pb_mst_clubs as club', 'club.club_id', '=', 'int.ref_post_id')
                ->where(['int.interest_id' => $interest_id]) // Notice the use of 'int.interest_id'
                ->first();

            if ($data) {
                $data->interedted_members = InterestedUser::select('member.member_id','member.nickname', 'member.member_type', 'member.member_name', 'member.profile_pic')
                    ->from('pb_interested_users as int_usr')
                    ->join('pb_mst_members as member', 'member.member_id', '=', 'int_usr.association_id')
                    ->where(['int_usr.interest_id' => $interest_id])
                    ->get();
            }

            return $data;
        } catch (\Exception $e) {

            Log::info('CommonRepository get_interest_details error' . print_r($e->getMessage(), true));

            $response = array(
                'status' => 0,
                'message' => trans('pages.something_wrong'),
                'error' => $e->getMessage(),
                "data" => (object) []
            );

            return response()->json($response);
        }
    }

    public static function cart_listing($request, $list = '')
    {

        $cart_list = [
            'list' => [],
            'subtotal' => \Helper::convertIntToDecimal(0),
        ];
        try {

            $subtotal = 0.00;
            $discount_amount = 0.00;

            if (count($list)) {
                foreach ($list as $key => $value) {

                    $activity_dtls_array = TraCartItem::where('cart_id', $value->cart_id)->get();

                    $list[$key]['cart_status'] = $value->cart_status;
                    $list[$key]['items'] = [];

                    if (count($activity_dtls_array)) {

                        $detailsArray = [];

                        foreach ($activity_dtls_array as $detail) {

                            $slot = BookingSlot::select('slot_type','start_time', 'end_time', 'total_minutes')->where('booking_slot_id', $detail->booking_slot_id)->first();

                            $cart_item_status = $detail->cart_item_status;

                            if ($detail->item_type == config('custom.cart_item_type.club_booking')) {

                                $club = Club::select('club_name', 'profile_pic')->where('club_id', $detail->club_id)->first();
                                $court = Court::select('court_title', 'game_type_term')->where('court_id', $detail->court_id)->first();

                                // $item_type = $detail->item_type;
                                $court_title = $court ? $court->court_title : NULL;
                                $club_name = $club ? $club->club_name : NULL;
                                $profile_pic = $club ? $club->profile_pic : NULL;
                                $game_type_term = $court ? $court->game_type_term : NULL;
                                $price = $detail->total_amount;

                                $timezone = CommonRepository::get_club_timezone($detail->club_id);

                                $detailArray = [
                                    'item_type' => $detail->item_type,
                                    'cart_item_status' => $cart_item_status,
                                    'start_time' => $detail->start_time,
                                    'end_time' => $detail->end_time,
                                    'duration' => $detail->duration,
                                    'club_image' => $profile_pic,
                                    'court_title' => $court_title,
                                    'club_name' => $club_name,
                                    'game_type_term' => $game_type_term,
                                    'total_amount' => $price,
                                    'timezone' => $timezone,
                                ];

                                $detailsArray[] = $detailArray;

                            } else if ($detail->item_type == config('custom.cart_item_type.trainer_booking')) {

                                $member = Member::select('member_name', 'profile_pic')
                                    ->where('member_id', $detail->trainer_id)
                                    ->first();

                                $timezone = CommonRepository::get_member_timezone($detail->trainer_id);

                                $detailArray = [
                                    'item_type' => $detail->item_type,
                                    'cart_item_status' => $cart_item_status,
                                    'start_time' => $slot->start_time,
                                    'end_time' => $slot->end_time,
                                    'duration' => $slot->total_minutes,
                                    'trainer_name' => $member ? $member->member_name : NULL,
                                    'trainer_profile' => $member ? $member->profile_pic : NULL,
                                    'total_amount' => $detail->subtotal_amount,
                                    'timezone' => $timezone,
                                ];

                                $detailsArray[] = $detailArray;

                            }

                        }

                        $list[$key]['items'] = $detailsArray;
                    }

                    $subtotal += floatval($value->grand_total_amount);
                    $discount_amount += floatval($value->discount_amount);

                }

                return array(
                    'list' => $list,
                    'subtotal' => \Helper::convertIntToDecimal($subtotal),
                    'discount_amount' => \Helper::convertIntToDecimal($discount_amount),
                );
            }

            return $cart_list;

        } catch (\Exception $e) {

            Log::info('error CommonRepository cart_listing ' . print_r($e->getMessage(), true));

            return $cart_list;

        }

    }

    // update cart status if timeslot start_time is expired
    public static function update_cart_status($result)
    {
        try {

            $currentDateTime = now();

            if (count($result)) {

                foreach ($result as $key => $value) {

                    $cart_items = TraCartItem::from('pb_tra_cart_items as cart_item')
                                              ->where('cart_id', $value->cart_id);

                    $q = $cart_items->get();

                    $cart_items = $cart_items->join('pb_booking_slots as booking_slots', function ($join) use ($currentDateTime) {
                                                $join->on('booking_slots.booking_slot_id', 'cart_item.booking_slot_id')
                                                      ->where('booking_slots.is_active', 1)
                                                      ->where('booking_slots.start_time', '<=', $currentDateTime);
                                            })->update([
                                                'cart_item.cart_item_status' => config('custom.cart_item_status.not_available')
                                            ]);

                    // if all item expired then update main cart_status as expired
                    $allExpired = $q->every(function ($item) {
                        return $item->cart_item_status === config('custom.cart_item_status.not_available');
                    });

                    if ($allExpired) {
                        TraCart::where('cart_id', $value->cart_id)->update([
                            'cart_status' => config('custom.cart_status.not_available')
                        ]);
                    }

                }

            }

        } catch (\Exception $e) {

            Log::info('update_cart_status error ' . print_r($e->getMessage(), true));

        }

    }

    public static function order_list($request, $list = '')
    {

        $cart_list = [];
        try {

            $subtotal = 0.00;

            if (count($list)) {
                foreach ($list as $key => $value) {

                    $group_by_sub_order_ids = TraOrdersDetails::where('order_id', $value->order_id)
                        ->groupBy('sub_order_id')
                        ->pluck('sub_order_id');

                    if (count($group_by_sub_order_ids)) {

                        $list[$key]['items'] = [];

                        foreach ($group_by_sub_order_ids as $sub_order_value) {

                            $activity_dtls_array = TraOrdersDetails::where('order_id', $value->order_id)
                                ->where('sub_order_id', $sub_order_value)
                                ->get();

                            $detailsArray = [];
                            if (count($activity_dtls_array)) {

                                foreach ($activity_dtls_array as $detail) {

                                    $slot = BookingSlot::select('start_time', 'end_time', 'total_minutes')->where('booking_slot_id', $detail->booking_slot_id)->first();

                                    if ($detail->item_type == config('custom.cart_item_type.club_booking')) {

                                        $club = Club::select('club_name', 'profile_pic')->where('club_id', $detail->club_id)->first();
                                        $court = Court::select('court_title', 'game_type_term')->where('court_id', $detail->court_id)->first();

                                        $item_type = $detail->item_type;
                                        $court_title = $court->court_title;
                                        $club_name = $club->club_name;
                                        $profile_pic = $club->profile_pic;
                                        $game_type_term = $court->game_type_term;
                                        $price = $detail->total_amount;
                                        $start_time = $slot->start_time;
                                        $end_time = $slot->end_time;
                                        $total_minutes = $slot->total_minutes;

                                        $detailArray = [
                                            'item_type' => $item_type,
                                            'start_time' => $start_time,
                                            'end_time' => $end_time,
                                            'duration' => $total_minutes,
                                            'club_image' => $profile_pic,
                                            'court_title' => $court_title,
                                            'club_name' => $club_name,
                                            'game_type_term' => $game_type_term,
                                        ];

                                        $detailsArray[] = $detailArray;

                                    } else if ($detail->item_type == config('custom.cart_item_type.trainer_booking')) {

                                        $member = Member::select('member_name', 'profile_pic')->where('member_id', $detail->trainer_id)->first();

                                        $detailArray = [
                                            'item_type' => $detail->item_type,
                                            'trainer_name' => $member->member_name,
                                            'trainer_profile' => $member->profile_pic,
                                        ];

                                        $detailsArray[] = $detailArray;

                                    }

                                }

                            }
                            if (count($detailsArray)) {

                                // dd($list[$key]['items']);
                                // array_push($list[$key]['items'], $detailsArray);
                                $list[$key]['items'] = $detailsArray;

                            }

                        }
                    }

                }

                return array('list' => $list);
            }

            return $cart_list;

        } catch (\Exception $e) {

            Log::info('error CommonRepository order_list  ' . print_r($e->getMessage(), true));

            return $cart_list;

        }

    }

    public static function get_booking_slot_detail($booking_slot_id)
    {
        try{

            $slot = BookingSlot::select('booking_slot_id','club_id','court_id','trainer_id','slot_type','category_type','start_time','end_time','price','is_available_for_booking','total_minutes','maximum_people')
                                ->where('booking_slot_id', $booking_slot_id)
                                ->first();

            return $slot;

        } catch(\Exception $e){
            Log::info('error CommonRepository get_booking_slot_detail  ' . print_r($e->getMessage(), true));
            return null;
        }
    }

    public static function get_order_details($booking_slot_id)
    {
        try{

            $order_dtl = TraOrdersDetails::select('order_detail_id','order_id','cart_id','sub_order_id','item_type','slot_type','club_id','court_id','booking_slot_id','trainer_id','total_amount','discount_amount','subtotal_amount','order_item_status','payout_amount')
                                ->where('booking_slot_id', $booking_slot_id)
                                ->first();

            return $order_dtl;

        } catch(\Exception $e){
            Log::info('error CommonRepository get_order_details  ' . print_r($e->getMessage(), true));
            return null;
        }
    }

    public static function get_club_detail($club_id)
    {
        try{

            $club = Club::select('club_id','club_name','club_email','profile_pic','phone_no','avg_rating','latitude','longitude','country_code','website','timezone_term','club_contact_name','location')->where('club_id', $club_id)->first();
            return $club;

        } catch(\Exception $e){

            Log::info('get_club_detail error ' . print_r($e->getMessage(), true));
            return null;

        }

    }

    public static function get_court_detail($court_id)
    {
        try{
                $court = Court::select('court_title', 'game_type_term')->where('court_id', $court_id)->first();
                return $court;
        } catch(\Exception $e){
            Log::info('error CommonRepository get_court_detail  ' . print_r($e->getMessage(), true));
            return null;
        }
    }

    public static function get_member_detail($member_id)
    {
        try{
                $member = Member::select('member_name', 'profile_pic','member_id','avg_rating','trainer_avg_rating','distance','phone','country_code','email','address1','address2','city','state','country','zipcode','rate',DB::raw("CONCAT_WS(', ', address1, address2, city, state, country, zipcode) as full_address"))
                                  ->where('member_id', $member_id)->first();
                return $member;

        } catch(\Exception $e){
            Log::info('error CommonRepository get_member_detail  ' . print_r($e->getMessage(), true));
            return null;
        }
    }

    public static function get_trainer_club($member_id)
    {
        try{

                $club = DB::table('pb_mst_clubs as club')
                              ->select('club.club_id', 'club.club_name','club.profile_pic as club_image','court.court_title','court.game_type_term','club.country_code','club.phone_no','club.website','club.longitude','club.latitude')
                              ->leftjoin('pb_mst_courts as court','court.club_id','=','club.club_id')
                              ->where('club.association_id', $member_id)
                              ->where('club.association_type_term', config('custom.association_type_term.member'))
                              ->first();

                return $club;

        } catch(\Exception $e){
            Log::info('error CommonRepository get_member_detail  ' . print_r($e->getMessage(), true));
            return null;
        }
    }

    public static function get_timezone_term($timezone_id)
    {
        try{

            $timezone = TimeZone::select('label')->where('timezone_id', $timezone_id)->first();
            return $timezone;

        } catch(\Exception $e){

            Log::info('get_timezone_term error ' . print_r($e->getMessage(), true));
            return null;

        }

    }

    public static function get_timezone_id($timezone_term)
    {
        try{

            Log::info('get_timezone_id timezone_term --> '. print_r($timezone_term, true));

            $timezone = TimeZone::select('timezone_id')->where('label', $timezone_term)->first();
            return $timezone;

        } catch(\Exception $e){

            Log::info('get_timezone_term error ' . print_r($e->getMessage(), true));
            return null;

        }

    }

    public static function booking_connections($request)
    {
        try {

            $auth_user = \Helper::get_authorize_user();

            if (!$auth_user) {
                return response()->json(['status' => 0, "message" => trans('auth.username_failed')], 200);
            }

            $limit = $request->has('limit') ? $request->limit : config('custom.default_records_limit');

            $page_index = $request->has('page') ? $request->page : 1;

            $column_name = isset($request->order_by_name) ? $request->order_by_name : 'member.member_id';

            $column_sort_order = isset($request->order_by_type) ? $request->order_by_type : 'desc';

            $q = TraBookingPlayer::from('pb_tra_booking_players as booking_players')
                                        ->select('booking_players.association_id')
                                        ->where('booking_players.is_active', 1);

            if($request->sub_order_id){

                $q->where('booking_players.sub_order_id', $request->sub_order_id);
            }

            $players = $q->get();

            $data = '';
            $recordsTotal = 0;
            $totalPages = 0;

            if($players){

              $memberIds = array_column($players->toArray(), 'association_id');

              $memberIds = array_filter($memberIds, function ($value) {
                  return $value !== null;
              });

              $main_query = MemberConnections::from('pb_mst_member_connections as member_conn')
                                            ->select('member.member_name', 'member.profile_pic', 'member.member_id', 'member.member_type')
                                            ->join('pb_mst_members as member', function ($join) use($memberIds) {
                                                $join->on('member_conn.requested_member_id', 'member.member_id')
                                                    ->whereNotIn('member.member_id', $memberIds)
                                                    ->where('member.is_active', 1)
                                                    ->where('member.is_deleted', 0);
                                            })
                                            ->where([
                                                'member_conn.first_member_id' => $auth_user->association_id,
                                                'member_conn.is_active' => 1,
                                                'member_conn.status_term' => config('custom.status_term.active'),
                                                'member_conn.is_following' => 1,
                                            ])
                                            ->orderBy($column_name, $column_sort_order);

              if (isset($request->search) && !empty($request->search)) {

                  $search = $request->search;

                  $main_query = $main_query->where(function ($query) use ($search) {
                                              $query->where('member.member_name', 'LIKE', "%{$search}%");
                                            });

              }

              $data_list_for_count = $main_query->get();

              $recordsTotal = count($data_list_for_count);

              $data = $main_query->paginate($limit, ['*'], 'page', $page_index);

              $totalPages = $data->lastPage();

            }

            $data = [
                'list' => $data->getCollection(),
                'recordsTotal' => (int) $recordsTotal,
                'totalPages' => $totalPages,
            ];
            return $data;

        } catch (\Exception $e) {

            Log::info('CommonRepository booking_connections error' . print_r($e->getMessage(), true));

            $response = array(
                'status' => 0,
                'message' => trans('pages.something_wrong'),
                'error' => $e->getMessage(),
                "data" => (object) []
            );

            return response()->json($response);
        }
    }

    public static function get_match_score_results($request,$sub_order_id = '')
    {
        $recent_results = false;

        try {

            $main_query = DB::table('pb_tra_match as tra_match')
                          ->select('tra_match.match_id', 'tra_match.datetime', 'tra_match.match_date', 'tra_match.court_type_term', 'tra_match.match_type_term', 'tra_match.start_time', 'tra_match.end_time', 'tra_match.duration', 'tra_match.team_1_id', 'tra_match.team_2_id', 'tra_match.team_1_score', 'tra_match.team_2_score');

            if($request->club_id){

                $main_query->join('pb_tra_order_details as order_detail', 'tra_match.order_id', '=', 'order_detail.order_id')
                            ->where('order_detail.club_id', $request->club_id)
                            ->groupBy('order_detail.order_id');

            }

            if(!empty($sub_order_id)){
                $main_query->where('tra_match.sub_order_id', $sub_order_id);
            }

            if(isset($request->match_id) && !empty($request->match_id)){
                $main_query->where('tra_match.match_id', $request->match_id);
            }

            $matches = $main_query->get();

            if(count($matches)){

                $recent_results=[];

                foreach ($matches as $match) {

                    $team1 = DB::table('pb_mst_team')
                                  ->select('title')
                                  ->where('team_id', $match->team_1_id)
                                  ->first();

                    $team2 = DB::table('pb_mst_team')
                                  ->select('title')
                                  ->where('team_id', $match->team_2_id)
                                  ->first();

                    $team1Players = DB::table('pb_tra_match_players as mp')
                                  ->join('pb_mst_members as m', 'mp.member_id', '=', 'm.member_id')
                                  ->select('m.member_id as player_id', 'm.member_name as player_name', 'm.profile_pic as player_profile', 'm.avg_rating')
                                  // ->where('mp.is_default', 0)
                                  ->where('mp.match_id', $match->match_id)
                                  ->where('mp.team_id', $match->team_1_id)
                                  ->get();

                    $team2Players = DB::table('pb_tra_match_players as mp')
                                  ->join('pb_mst_members as m', 'mp.member_id', '=', 'm.member_id')
                                  ->select('m.member_id as player_id', 'm.member_name as player_name', 'm.profile_pic as player_profile', 'm.avg_rating')
                                  // ->where('mp.is_default', 0)
                                  ->where('mp.match_id', $match->match_id)
                                  ->where('mp.team_id', $match->team_2_id)
                                  ->get();

                    $score1 = [
                        'team_name' => isset($team1) ? $team1->title : '',
                        'players' => $team1Players->toArray(),
                        'score' => json_decode($match->team_1_score),
                        'datetime' => $match->datetime,
                        'match_date' => $match->match_date,
                        'start_time' => $match->start_time,
                        'end_time' => $match->end_time,
                    ];

                    $score2 = [
                        'team_name' => isset($team2) ? $team2->title : '',
                        'players' => $team2Players->toArray(),
                        'score' => json_decode($match->team_2_score),
                        'datetime' => $match->datetime,
                        'match_date' => $match->match_date,
                        'start_time' => $match->start_time,
                        'end_time' => $match->end_time,
                    ];

                    $recent_results[] = [
                        'match_id' => $match->match_id,
                        'datetime' => $match->datetime,
                        'court_type_term' => $match->court_type_term,
                        'match_type_term' => $match->match_type_term,
                        'duration' => $match->duration,
                        'score' => [$score1, $score2],
                    ];

                }

                if($request->sub_order_id && count($recent_results) > 0){
                    return $recent_results[0];
                }

                if($request->match_id){
                    return $recent_results[0];
                }

                return $recent_results;
            }

        } catch (\Throwable $th) {

            Log::info('CommonRepository get_match_score_results error ' . print_r($th->getMessage(), true));
            return $recent_results;
        }
    }

    public static function get_order_rating($order_detail_id)
    {
        try{

            $rating = TraRating::select('overall_rating')->where('order_detail_id', $order_detail_id)->first();
            return $rating;

        } catch(\Exception $e){
            Log::info('CommonRepository get_order_rating error ' . print_r($e->getMessage(), true));
            return null;
        }
    }

    public static function get_all_member_posts($request, $member_id, $type_of_post, $column_name, $column_sort_order)
    {

        try {

            $main_query = SocPost::from('pb_soc_posts as post')
                                  ->select('post.post_id as post_id','post.post_type_term','post.type_of_post','interest.interest_id', 'member.member_id', 'member.member_name', 'member.profile_pic', 'post.comment', 'post.total_likes', 'post.total_comment', 'post.created_at','post.group_id','post.ref_association_id','post.ref_association_type_term',
                                      DB::raw('(SELECT COUNT(interest_user.interest_user_id) FROM pb_interested_users interest_user WHERE interest_user.ref_post_id = post.post_id AND interest_user.association_type_term = "' . config('custom.association_type_term.member') . '") AS total_interested_user'),
                                      DB::raw('(SELECT CASE WHEN COUNT(interest_user.interest_user_id) > 0 THEN 1 ELSE 0 END FROM pb_interested_users interest_user WHERE interest_user.association_id = "' . $member_id  . '" AND interest_user.ref_post_id = post.post_id AND interest_user.association_type_term = "' . config('custom.association_type_term.member') . '") AS is_interested'),
                                      DB::raw('(SELECT CASE WHEN COUNT(soc_likes.like_id) > 0 THEN 1 ELSE 0 END FROM pb_soc_likes soc_likes WHERE soc_likes.member_id = ' . $member_id . ' AND soc_likes.association_id = post.post_id AND soc_likes.association_type_term = "' . config('custom.association_type_term.post') . '") AS is_like')
                                  )
                                  ->join('pb_mst_members as member', function ($join) {
                                      $join->on('post.association_id', 'member.member_id')
                                          ->where('member.is_active', 1);
                                  })
                                  ->leftJoin('pb_report_post as report_post', function ($join) use($member_id) {
                                      $join->on('post.post_id', '=', 'report_post.post_id')
                                          ->where('report_post.association_id', '=', $member_id)
                                          ->where('report_post.association_type_term', '=', config('custom.association_type_term.member'))
                                          ->where('report_post.is_active', '=', 1);
                                  })
                                  ->leftjoin('pb_interests as interest', 'interest.ref_post_id', '=', 'post.post_id')
                                  ->where('post.association_type_term', config('custom.association_type_term.member'))
                                  ->where('post.type_of_post', $type_of_post)
                                  ->whereNull('report_post.report_post_id')
                                  ->where('post.is_active', 1);

            return $main_query;

        } catch(\Exception $e){
            Log::info('get_all_member_posts error ' . print_r($e->getMessage(), true));
            return [];
        }
    }

    // If buy subscription then update already added cart item price based on app settings configuration
    public static function update_cart_item_price($user_data)
    {

        try {

            $app_settings = AppSetting::first();

            $carts = TraCart::from('pb_tra_cart as cart')
                              ->where([
                                  'cart.association_id' => $user_data['member_id'],
                                  'cart.association_type_term' => $user_data['user_type'],
                                  'cart.cart_type' => config('custom.cart_type.normal'),
                                  'cart.is_active' => 1,
                              ])->get();

            if(count($carts)){

                foreach ($carts as $k1 => $cart) {

                    $cart_item = TraCartItem::from('pb_tra_cart_items as cart_item')
                                              ->where('cart_item.cart_id', $cart->cart_id)
                                              ->get();

                    $sum_of_subtotal_amount = 0;

                    if($cart_item){

                        foreach ($cart_item as $k2 => $cart_itm) {

                            // subscriber/non-subscriber price
                            $subs_price = UserRepository::subscriber_user_price($cart_itm->total_amount, $app_settings->discount_type, $app_settings->discount_value);

                            $total_amount = 0;
                            $subtotal_amount = 0;

                            if($subs_price['status'] == 1){

                                $total_amount = $subs_price['data']['price'];

                                $subtotal_amount = $total_amount - $cart_itm->discount_amount;

                            }

                            TraCartItem::from('pb_tra_cart_items as cart_item')
                                                      ->where('cart_item.cart_item_id', $cart_itm->cart_item_id)
                                                      ->update([
                                                          'total_amount' => $total_amount,
                                                          'subtotal_amount' => $subtotal_amount,
                                                      ]);

                            $updated_cart = TraCartItem::from('pb_tra_cart_items as cart_item')
                                                      ->where('cart_item.cart_item_id', $cart_itm->cart_item_id)
                                                      ->first();

                            $sum_of_subtotal_amount = $sum_of_subtotal_amount + $updated_cart->subtotal_amount;

                        }

                    }

                    if($sum_of_subtotal_amount > 0){

                        $grand_total_amount = $sum_of_subtotal_amount - $cart->discount_amount;

                        TraCart::from('pb_tra_cart as cart')
                                  ->where('cart.cart_id', $cart->cart_id)
                                  ->update([
                                      'subtotal_amount' => $sum_of_subtotal_amount,
                                      'grand_total_amount' => $grand_total_amount
                                  ]);

                    }

                }

                return response()->json(['status' => 1, "message" => trans('pages.action_success'), 'data' => (object)[] ]);

            } else {

                return response()->json(['status' => 0, "message" => trans('pages.crud_messages.no_data', ['attr' => 'Cart']), 'data' => (object)[] ]);

            }

        } catch (\Throwable $th) {

          Log::info("update_cart_item_price error ". $th->getMessage());

        }
    }

    public static function is_disabled_cart_items($value)
    {

        $result = [
            'is_disabled' => 0,
        ];

        try{

            //code for cart timer limit

            $is_disabled = 0;

            $app_settings = \Helper::get_app_setting();

            $current_datetime = date('Y-m-d H:i:s');

            if($app_settings && $app_settings->cart_timer_limit != NULL){

                $cart_timer_limit = \Helper::convertSecondsToMinutes($app_settings->cart_timer_limit);

                $startDateTime = Carbon::parse($current_datetime);

                // $updatedDateTime = $startDateTime->addMinutes($cart_timer_limit);

                $future_utc_dt = $startDateTime->format('Y-m-d H:i:s');

                $cart_items = TraCartItem::from('pb_tra_cart_items as cart_item')
                                          ->select('cart_item.cart_item_id', 'cart_item.cart_id', 'cart.cart_type','cart_item.booking_slot_id','cart.timer_start_time', \DB::raw('TIMESTAMPDIFF(MINUTE, cart.timer_start_time, "' . $future_utc_dt . '") AS time_difference_minutes'))
                                          ->leftJoin('pb_tra_cart as cart', function ($join) {
                                              $join->on('cart.cart_id', 'cart_item.cart_id')
                                                  ->where('cart.cart_type', config('custom.cart_type.normal'))
                                                  ->where('cart.is_active', 1)
                                                  ->whereNotNull('cart.timer_start_time');
                                          })
                                          ->where('cart_item.booking_slot_id', $value->booking_slot_id)
                                          ->having('time_difference_minutes', '<', $cart_timer_limit)
                                          ->count();

                if($cart_items > 0){

                    $is_disabled = 1;

                }

            }

            $result = [
                'is_disabled' => $is_disabled,
            ];

            return $result;

        } catch(\Exception $e){

            Log::info('cart_timer_limit error ' . print_r($e->getMessage(), true));
            return $result;

        }
    }

    public static function get_post_detail($request,$post_id,$member_id = '')
    {
        $result = null;
        try{
            $post = SocPost::from('pb_soc_posts as post')
                                ->select('post.post_id','post.type_of_post','interest.interest_id', 'member.member_id', 'member.member_name', 'member.profile_pic', 'post.comment', 'post.total_likes', 'post.total_comment', 'post.post_id', 'post.created_at','post.ref_association_id',
                                    DB::raw('(SELECT COUNT(interest_user.interest_user_id) FROM pb_interested_users interest_user WHERE interest_user.ref_post_id = post.post_id AND interest_user.association_type_term = "' . config('custom.association_type_term.member') . '") AS total_interested_user'),
                                    DB::raw('(SELECT CASE WHEN COUNT(interest_user.interest_user_id) > 0 THEN 1 ELSE 0 END FROM pb_interested_users interest_user WHERE interest_user.association_id = "' . $member_id  . '" AND interest_user.ref_post_id = post.post_id AND interest_user.association_type_term = "' . config('custom.association_type_term.member') . '") AS is_interested'),
                                    DB::raw('(SELECT CASE WHEN COUNT(soc_likes.like_id) > 0 THEN 1 ELSE 0 END FROM pb_soc_likes soc_likes WHERE soc_likes.member_id = ' . $member_id . ' AND soc_likes.association_id = post.post_id AND soc_likes.association_type_term = "' . config('custom.association_type_term.post') . '") AS is_like')
                                )
                                ->join('pb_mst_members as member', function ($join) {
                                    $join->on('post.association_id', 'member.member_id')
                                        ->where('member.is_active', 1)
                                        ->where('member.is_deleted', 0);
                                })
                                ->leftJoin('pb_report_post as report_post', function ($join) use($member_id) {
                                    $join->on('post.post_id', '=', 'report_post.post_id')
                                        ->where('report_post.association_id', '=', $member_id)
                                        ->where('report_post.association_type_term', '=', config('custom.association_type_term.member'))
                                        ->where('report_post.is_active', '=', 1);
                                })
                                ->leftjoin('pb_interests as interest', 'interest.ref_post_id', '=', 'post.post_id')
                                ->where('post.association_type_term', config('custom.association_type_term.member'))
                                ->whereNull('report_post.report_post_id')
                                ->where('post.post_id', $post_id)
                                ->where('post.is_active', 1)->first();

            if($post){

                $post->media_files = SocPostImage::select('post_image_id','image_path','image_type_term')
                                                    ->where('post_id', $post->post_id)
                                                    ->where('is_active', 1)
                                                    ->whereNotNull('image_path')
                                                    ->get();

                if(isset($post->type_of_post) && $post->type_of_post == config('custom.type_of_post.upload_score') ){

                    $request->match_id = $post->ref_association_id;

                    $post->score_details = self::get_match_score_results($request);
                }

                if(isset($post->type_of_post) && $post->type_of_post == config('custom.type_of_post.interest_activity') ){

                    $interested_users = self::get_interest_details($post->ref_association_id);

                    $post->interested_users = [];

                    if($interested_users){
                        $post->interested_users = $interested_users->interedted_members;
                    }

                    $interest = Interest::where('interest_id', $post->ref_association_id)->first();

                    $post->club_details = NULL;

                    if($interest){
                        $post->club_details = self::get_club_detail($interest->club_id);
                    }

                }

                $result = $post;

                return $result;
            }else{
                return $result;
            }
        }
        catch(\Exception $e){

            Log::info('get_post_detail error ' . print_r($e->getMessage(), true));
            return $result;
        }
    }

    public static function notify_to_devices($dataPayload = [])
    {

        try {

          // In local : php artisan queue:work
          // Set POST variables
          $url = 'https://5osegwb3supwrykqb6xrzgbqdq0fdbdr.lambda-url.us-east-1.on.aws';

          Log::info('SendNotification ==== ');

          $headers = array(
              'Content-Type: application/json'
          );

          // Open connection
          $ch = curl_init();

          // Set the url, number of POST vars, POST data
          curl_setopt($ch, CURLOPT_URL, $url);

          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
          // Disabling SSL Certificate support temporarly
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataPayload));
          //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->dataPayload,JSON_UNESCAPED_SLASHES));

          \Log::info("Notif dataPayload   ===   ". print_r($dataPayload, true));

          // Execute post
          $result = curl_exec($ch);

          \Log::info("Notif Result === ". print_r($result, true));

          if ($result === FALSE) {
              \Log::error('Curl failed: ' . curl_error($ch));
          }

          // Close connection
          curl_close($ch);
          // echo $result;

        } catch (\Exception $e) {
            \Log::info("send_notification_to_device error ". print_r($e->getMessage(), true));
        }

    }

    public static function update_member_location($member, $latitude, $longitude)
    {
        try {

            Member::where('member_id', $member->member_id)->update([
                'current_latitude' => $latitude,
                'current_longitude' => $longitude,
            ]);
            // em1
            dispatch(new \App\Jobs\UpdateMemberLocation($member, $latitude, $longitude));

        } catch (\Exception $e) {

            Log::info("update_member_location error " . $e->getMessage());

        }

    }

    public static function update_current_approx_people_count($club_id)
    {
        try {

            $member_count = TraMemberUpdates::where('ref_association_id', $club_id)
                                            ->where('ref_association_type_term', config('custom.module_type_term.club'))
                                            ->where('is_active', 1)
                                            ->count();

            Log::info('member_count '. print_r($member_count, true));

            Club::where('club_id', $club_id)
                  ->update([
                    'current_approx_people_count' => $member_count
                  ]);

        } catch (\Exception $e) {

            Log::info("update_approx_people_count error " . $e->getMessage());

        }

    }

    public static function update_member_timezone($timezone, $association_id, $association_type_term)
    {
        try
        {
            if(!empty($timezone)){

                $member_tz = self::get_timezone_id($timezone);

                if($member_tz){

                    User::where('association_id', $association_id)
                          ->where('association_type_term', $association_type_term)
                          ->update(['timezone_id' => $member_tz->timezone_id ]);

                    $response_array = array('status' => 1, 'message' => trans('pages.saved_success'), 'data' => (object)[] );

                    return response()->json($response_array, 200);

                }else{

                    // for this type of data : Asia/Calcutta & Asia/Kolkata
                    Log::info('timezone not found --> '. print_r($timezone, true));
                    Log::info('Member id --> '. print_r($association_id, true));
                    Log::info('association_type_term --> '. print_r($association_type_term, true));

                    return response()->json(['status' => 0, "message" => trans('pages.crud_messages.no_data', ['attr' => 'Timezone']), 'data' => (object)[] ]);

                }

            }else{
                return response()->json(['status' => 0, "message" => trans('pages.crud_messages.no_data', ['attr' => 'Timezone']), 'data' => (object)[] ]);
            }

        } catch (Exception $e) {

            Log::info('update_member_timezone error ' . print_r($e->getMessage(), true));

            $response = array(
                'status' => 0,
                'message' => "Something went wrong",
                'error' => $e->getMessage(),
                "data" => (object) [],
            );

            return response()->json($response);
        }
    }

    public static function get_admin_commission_amount($calc_price, $booking_slot_price)
    {
        // booking_slot_price = already discounted price
        // calc_price = based on subscriber or non-subscriber user
        $response = array('status' => 0, 'message' => trans('pages.something_wrong'), 'data' => (object)[] );

        try
        {

            $auth_user = \Helper::get_authorize_user();

            if (!$auth_user) {
                return response()->json(['status' => 0, "message" => trans('auth.username_failed'), "data" => (object)[]], 200);
            }

            if(!empty($calc_price) && !empty($booking_slot_price)){

                $admin_amount = $calc_price - $booking_slot_price;

                $data = ['admin_amount' => $admin_amount ];

                $response = array('status' => 1, 'message' => trans('pages.saved_success'), 'data' => $data );

            }else{
                $response = array('status' => 0, "message" => trans('pages.crud_messages.no_data', ['attr' => 'Data']), 'data' => (object)[] );
            }

            return $response;

        } catch (Exception $e) {

            Log::info('get_admin_commission_amount error ' . print_r($e->getMessage(), true));

            return $response;
        }
    }

    public static function get_order_payout_amount($subtotal_amount, $admin_amount)
    {

        $response = array('status' => 0, 'message' => trans('pages.something_wrong'), 'data' => (object)[] );

        try
        {

            if(isset($subtotal_amount) && isset($admin_amount)){

                $payout_amount = $subtotal_amount - $admin_amount;

                $data = ['payout_amount' => $payout_amount ];

                $response = array('status' => 1, 'message' => trans('pages.saved_success'), 'data' => $data );

            }else{
                $response = array('status' => 0, "message" => trans('pages.crud_messages.no_data', ['attr' => 'Data']), 'data' => (object)[] );
            }

            return $response;

        } catch (Exception $e) {

            Log::info('get_order_payout_amount error ' . print_r($e->getMessage(), true));

            return $response;
        }
    }

    public static function update_order_club_court($order_id)
    {

        $response = array('status' => 0, 'message' => trans('pages.something_wrong'), 'data' => (object)[] );

        try
        {

            $auth_user = \Helper::get_authorize_user();

            if (!$auth_user) {
                return response()->json(['status' => 0, "message" => trans('auth.username_failed'), "data" => (object)[]], 200);
            }

            if(!empty($order_id) ){

                $trainer_bookings = TraOrdersDetails::where([
                                                        'order_id' => $order_id,
                                                        'item_type' => config('custom.cart_item_type.trainer_booking')
                                                    ])
                                                    ->groupBy('sub_order_id')
                                                    ->whereNull('club_id')
                                                    ->select('order_id', 'sub_order_id')
                                                    ->get();

                if(count($trainer_bookings)){

                    foreach ($trainer_bookings as $key => $value) {


                        $trainer_club_booking = TraOrdersDetails::where([
                                                                    'order_id' => $order_id,
                                                                    'sub_order_id' => $value->sub_order_id,
                                                                    'item_type' => config('custom.cart_item_type.club_booking')
                                                                ])->first();

                        if($trainer_club_booking){

                            TraOrdersDetails::where([
                                'order_id' => $order_id,
                                'sub_order_id' => $value->sub_order_id,
                                'item_type' => config('custom.cart_item_type.trainer_booking')
                            ])->update([
                                'club_id' => $trainer_club_booking->club_id,
                                'court_id' => $trainer_club_booking->court_id
                            ]);

                        }

                    }
                }

                $response = array('status' => 1, 'message' => trans('pages.saved_success'), 'data' => (object)[] );

            }else{
                $response = array('status' => 0, "message" => trans('pages.crud_messages.no_data', ['attr' => 'Data']), 'data' => (object)[] );
            }

            return $response;

        } catch (\Exception $e) {

            Log::info('get_order_club_court error ' . print_r($e->getMessage(), true));

            return $response;
        }
    }

    public static function get_ideal_value_by_term($category_name,$term_name)
    {
        try {

            $attribute = TermCategory::from('pb_term_category as tc')
                                ->leftjoin('pb_term as t', 't.term_category_id', '=', 'tc.term_category_id')
                                ->select('tc.category_name','t.term_id', 't.term_name', 't.term_details', 't.term_code','t.ideal_value')
                                ->where('tc.category_name', $category_name)
                                ->where('t.term_name', $term_name)
                                ->where('t.is_active', 1)
                                ->first();
            return $attribute;

        } catch (\Throwable $th) {
            Log::info("Commonrepository get_ideal_value_by_term " . print_r($th->getMessage(), true));
            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }

    }

    public static function get_member_timezone($member_id)
    {
        try{

            $user = User::select('timezone_id')->where('association_id', $member_id)
                          ->where('association_type_term', config('custom.association_type_term.member'))
                          ->first();

            $timezone = "UTC";

            if($user && !empty($user->timezone_id)){

                $tz = TimeZone::select('label')->where('timezone_id', $user->timezone_id)->first();
                $timezone = $tz->label;

            }

            return $timezone;

        } catch(\Exception $e){

            Log::info('get_timezone_term error ' . print_r($e->getMessage(), true));
            return null;

        }

    }

    public static function get_club_timezone($club_id)
    {
        try{

            $club = Club::select('timezone_term')->where('club_id', $club_id)->first();

            $timezone = "UTC";

            if($club && !empty($club->timezone_term)){

                $tz = TimeZone::select('label')->where('timezone_id', $club->timezone_term)->first();
                $timezone = $tz->label;

            }

            return $timezone;

        } catch(\Exception $e){

            Log::info('get_club_timezone error ' . print_r($e->getMessage(), true));
            return null;

        }

    }

    public static function get_schedule_dates_by_year($request)
    {

        $result = [
            'dates' => [],
        ];

        try {

            $auth_user = \Helper::get_authorize_user();

            if (!$auth_user) {
                return response()->json(['status' => 0, "message" => trans('auth.username_failed'), "data" => (object)[]], 200);
            }

            // my_appointments (auth user ne as a trainer booked karelo hoy koi end user e eva data avse)
            $my_appointments = TraOrdersDetails::select('order_dtls.sub_order_id','order_dtls.booking_slot_id','slot.start_time','slot.end_time')
                                          // ->select('order_dtls.order_id','order_dtls.sub_order_id','order_dtls.order_item_status','member.member_id','member.member_name','member.profile_pic','slot.start_time','slot.end_time')
                                          ->from('pb_tra_order_details as order_dtls')
                                          ->leftJoin('pb_tra_orders as order', 'order.order_id', '=', 'order_dtls.order_id')
                                          ->leftJoin('pb_booking_slots as slot', 'slot.booking_slot_id', '=', 'order_dtls.booking_slot_id')
                                          ->leftJoin('pb_mst_members as member', 'member.member_id', '=', 'order.association_id')
                                          ->where('order_dtls.trainer_id', $auth_user->association_id)
                                          ->where('order.is_active', 1)
                                          ->where( DB::raw('YEAR(slot.start_time)'), '=', $request->year)
                                          ->whereIn('order_dtls.order_item_status', [config('custom.order_item_status.booked'), config('custom.order_item_status.completed')])
                                          ->groupByRaw('DATE_FORMAT(slot.start_time, "%Y-%m-%d")');
                                          // ->groupBy('order_dtls.sub_order_id');

            // my orders (auth user e bookings karyu hoy:club or trainer both)
            $my_orders = TraOrdersDetails::select('order_dtls.sub_order_id','order_dtls.booking_slot_id','slot.start_time','slot.end_time')
                                          ->from('pb_tra_order_details as order_dtls')
                                          ->leftJoin('pb_tra_orders as order', 'order.order_id', '=', 'order_dtls.order_id')
                                          ->leftJoin('pb_booking_slots as slot', 'slot.booking_slot_id', '=', 'order_dtls.booking_slot_id')
                                          ->leftJoin('pb_mst_members as member', 'member.member_id', '=', 'order.association_id')
                                          ->where('order.association_id', $auth_user->association_id)
                                          ->where('order.association_type_term', $auth_user->association_type_term)
                                          ->where('order.is_active', 1)
                                          ->where( DB::raw('YEAR(slot.start_time)'), '=', $request->year)
                                          ->whereIn('order_dtls.order_item_status', [config('custom.order_item_status.booked'), config('custom.order_item_status.completed')])
                                          ->groupByRaw('DATE_FORMAT(slot.start_time, "%Y-%m-%d")');

            $main_query = $my_appointments->union($my_orders);

            $main_query->orderBy('start_time', 'desc');

            $response = [
                'dates' => $main_query->get(),
            ];

            return $response;

        } catch (\Exception $e) {

            Log::info('get_schedule_dates_by_year error ' . print_r($e->getMessage(), true));
            $result['error'] = $e->getMessage();
            return $result;

        }

    }

    public static function get_schedules_by_date($request)
    {
        try {

            $auth_user = \Helper::get_authorize_user();

            if (!$auth_user) {
                return response()->json(['status' => 0, "message" => trans('auth.username_failed'), "data" => (object)[]], 200);
            }

            // my_appointments (auth user ne as a trainer booked karelo hoy koi end user e eva data avse)
            $my_appointments = TraOrdersDetails::select('order_dtls.order_id','order_dtls.sub_order_id','order_dtls.order_item_status','slot.start_time','order_dtls.start_time')
                                          ->from('pb_tra_order_details as order_dtls')
                                          ->leftJoin('pb_tra_orders as order', 'order.order_id', '=', 'order_dtls.order_id')
                                          ->leftJoin('pb_booking_slots as slot', 'slot.booking_slot_id', '=', 'order_dtls.booking_slot_id')
                                          ->where('order_dtls.trainer_id', $auth_user->association_id)
                                          ->where('order.is_active', 1)
                                          ->whereIn('order_dtls.order_item_status', [config('custom.order_item_status.booked'), config('custom.order_item_status.completed')])
                                          ->groupBy('order_dtls.sub_order_id');

            // my orders (auth user e bookings karyu hoy:club or trainer both)
            $my_orders = TraOrdersDetails::select('order_dtls.order_id','order_dtls.sub_order_id','order_dtls.order_item_status','slot.start_time','order_dtls.start_time')
                                          ->from('pb_tra_order_details as order_dtls')
                                          ->leftJoin('pb_tra_orders as order', 'order.order_id', '=', 'order_dtls.order_id')
                                          ->leftJoin('pb_booking_slots as slot', 'slot.booking_slot_id', '=', 'order_dtls.booking_slot_id')
                                          ->where('order.association_id', $auth_user->association_id)
                                          ->where('order.association_type_term', $auth_user->association_type_term)
                                          ->where('order.is_active', 1)
                                          ->whereIn('order_dtls.order_item_status', [config('custom.order_item_status.booked'), config('custom.order_item_status.completed')])
                                          ->groupBy('order_dtls.sub_order_id');

            if($request->date){

                $start_time = $request->date;
                $end_date = date('Y-m-d', strtotime($request->date)). ' 23:59:59';

                $main_query = $my_appointments->select('order_dtls.order_id','order_dtls.sub_order_id','order_dtls.order_item_status','slot.start_time','order_dtls.start_time')
                                    ->whereBetween('order_dtls.start_time', [$start_time, $end_date])
                                    ->union(
                                        $my_orders
                                            ->select('order_dtls.order_id','order_dtls.sub_order_id','order_dtls.order_item_status','slot.start_time','order_dtls.start_time')
                                            ->whereBetween('order_dtls.start_time', [$start_time, $end_date])
                                    );

                $dataObj = $main_query->get();

                foreach ($dataObj as $key => $order)
                {
                    $items = [];

                    $order_items = TraOrdersDetails::where('order_id', $order->order_id)
                                                ->where('sub_order_id', $order->sub_order_id)
                                                ->get();

                    if (count($order_items)) {

                        foreach ($order_items as $list)
                        {

                            $slot = BookingSlot::select('booking_slot_id','slot_type','start_time', 'end_time', 'total_minutes')
                                          ->where('booking_slot_id', $list->booking_slot_id)
                                          ->first();

                            if($slot){

                                if ($list->item_type == config('custom.cart_item_type.club_booking')) {

                                    $club = Club::select('club_name', 'profile_pic')->where('club_id', $list->club_id)->first();
                                    $court = Court::select('court_title', 'game_type_term')->where('court_id', $list->court_id)->first();
                                    $order_rating = self::get_order_rating($list->order_detail_id);

                                    $club_tz = self::get_club_timezone($list->club_id);

                                    $items[] = [
                                        'booking_slot_id' => $list->booking_slot_id,
                                        'item_type' => $list->item_type,
                                        'start_time' => $list->start_time,
                                        'end_time' => $list->end_time,
                                        'duration' => $list->duration,
                                        'club_name' => $club ? $club->club_name : NULL,
                                        'club_image' => $club ? $club->profile_pic : NULL,
                                        'court_title' => $court ? $court->court_title : NULL,
                                        'game_type_term' => $court ? $court->game_type_term : NULL,
                                        'avg_ratings' => isset($order_rating) ? $order_rating->overall_rating : 0.0,
                                        'timezone' => $club_tz,
                                    ];

                                } else if ($list->item_type == config('custom.cart_item_type.trainer_booking')) {

                                    $member = Member::select('member_name', 'profile_pic')->where('member_id', $list->trainer_id)->first();

                                    $order_rating = self::get_order_rating($list->order_detail_id);

                                    $trainer_tz = self::get_member_timezone($list->trainer_id);

                                    $items[] = [
                                        'booking_slot_id' => $list->booking_slot_id,
                                        'item_type' => $list->item_type,
                                        'trainer_name' => $member ? $member->member_name : NULL,
                                        'trainer_profile' => $member ? $member->profile_pic : NULL,
                                        'start_time' => $slot->start_time,
                                        'end_time' => $slot->end_time,
                                        'duration' => $slot->total_minutes,
                                        'avg_ratings' => isset($order_rating) ? $order_rating->overall_rating : 0.0,
                                        'timezone' => $trainer_tz,
                                    ];

                                }
                            }

                        }

                    }

                    $dataObj[$key]->items = $items;

                    unset($dataObj[$key]->order_detail_id);
                    unset($dataObj[$key]->order_id);
                    unset($dataObj[$key]->booking_slot_id);
                    unset($dataObj[$key]->item_type);
                    unset($dataObj[$key]->club_id);
                    unset($dataObj[$key]->court_id);
                    unset($dataObj[$key]->start_time);
                    unset($dataObj[$key]->end_time);
                    unset($dataObj[$key]->end_time);
                    unset($dataObj[$key]->total_minutes);

                }
            }

            return $dataObj;

        } catch (\Exception $e) {

            Log::info('get_schedules_by_date error ' . print_r($e->getMessage(), true));
            $result['error'] = $e->getMessage();
            return $result;

        }

    }

    // For open play : drop-in
    public static function duration_wise_price_calc($pricePer30Min, $duration)
    {
      try {

          // Parse the duration to get hours, minutes, and seconds
          $durationParts = explode(':', $duration);

          // Calculate the total duration in minutes
          $totalMinutes = $durationParts[0] * 60 + $durationParts[1] + $durationParts[2] / 60;

          // Calculate the price based on the duration in 30-minute intervals
          $price = $pricePer30Min * ($totalMinutes / 30);

          return $price;

      } catch (\Throwable $th) {

        Log::info("duration_wise_price_calc error ". $th->getMessage());

      }
    }

}
