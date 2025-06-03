<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationUsers;
use App\Repositories\CommonRepository as CommonRepo;
use Log;
use Validator;
use Exception;

class NotificationController extends Controller
{
  public function get_all_notification(Request $request){

      $this->mark_as_read_all_notifications($request);

      return view('pages.notifications.index');

  }

  public function mark_as_read_all_notifications(Request $request)
  {
      try{

          $auth_user = auth()->user();

          $main_query = Notification::from('pb_notifications as not')->where('not.is_active', 1);

              $main_query = $main_query->leftJoin('pb_notification_users as nu', function($join) use($auth_user) {

                                              $join = $join->on('nu.notification_id', '=', 'not.notification_id');

                                              if($auth_user && $auth_user->association_id){

                                                  $join = $join->where('nu.association_id', $auth_user->association_id)
                                                                ->where('nu.association_type_term', $auth_user->association_type_term);
                                              }

                                              $join = $join->where('nu.is_read', 0);
                                          })->update([
                                              'nu.is_read' => 1,
                                              'nu.read_at' => date('Y-m-d H:i:s')
                                          ]);
          return true;

      }catch(Exception $e) {

          Log::info("error mark_as_read_notifications ". print_r($e->getMessage(), true));
          return $e->getMessage();
      }
  }

  public function get_unread_all_notifications(Request $request) {

    $result = [
        'list' => [],
        'total' => 0
    ];

    try{
        $auth_user = auth()->user();

        $main_query = Notification::select('not.notification_id', 'not.notification_type', 'not.notification_title', 'not.notification_description', 'not.data', 'not.created_at', 'nu.is_read', 'nu.notify_id')
                                    ->from('pb_notifications as not')
                                    ->leftJoin('pb_notification_users as nu', function($join) use($auth_user) {

                                        $join = $join->on('nu.notification_id', '=', 'not.notification_id');

                                        if($auth_user && $auth_user->association_id){

                                            $join = $join->where('nu.association_id', $auth_user->association_id)
                                                          ->where('nu.association_type_term', $auth_user->association_type_term);
                                        }

                                        $join = $join->where('nu.association_type_term', $auth_user->association_type_term);
                                    });

        $list =   $main_query->where('not.is_active', 1)
                                ->where('nu.is_read', 0)
                                ->orderBy('not.notification_id', 'desc')
                                ->get();

        $result['list'] = $list;
        $result['total'] = count($list);

        return $result;

    }catch(Exception $e) {

        Log::info("error getUnreadAllNotificationsJson ". print_r($e->getMessage(), true));
        // return $e->getMessage();
        return $result;
    }

  }

  public function selected_notification_read(Request $request){

    try {

      $read_notification = NotificationUsers::where('notify_id', $request->notify_id)->update(array('is_read' => 1));

    }catch(Exception $e) {

        Log::info("error selected_notification_read ". print_r($e->getMessage(), true));
        return $e->getMessage();
    }
  }

  public function get_all_notifications_json(Request $request){

    try{

        $notifications = CommonRepo::get_all_notifications($request);

        $response_array = array('status' => 1,  'message' => trans('pages.action_success'), 'data' => $notifications);

        return response()->json($response_array, 200);

    }catch(\Exception $e) {

        Log::info("error get_all_notifications ". print_r($e->getMessage(), true));
        return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);

    }

  }



}
