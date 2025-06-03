<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberConnections;
use App\Models\TraOrdersDetails;
use App\Models\TraOrders;
use App\Models\SocPost;
use App\Repositories\MemberRepository;
use App\Repositories\CommonRepository;
use App\Repositories\UserRepository;
use App\Models\Term;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class MemberController extends Controller
{
    public function index()
    {
        try {

            return view('pages.members.index');

        } catch (\Throwable $th) {
            Log::info('MemberController index ' . print_r($th->getMessage(), true));
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function member_json_list(Request $request)
    {
        try {
            return MemberRepository::member_json_list($request);
        } catch (Exception $e) {
            Log::info("member_json_list error" . print_r($e->getMessage(), true));
            return redirect()->back()->with("message", trans('pages.something_wrong'));
        }
    }

    public function status_update(Request $request)
    {
        try {
            $status_update = Member::where('member_id', $request->member_id)->first();

            if ($status_update) {

                $status_update->is_active = $request->is_active;
                $status_update->save();

                return response()->json(['status' => 1, 'message' => trans('pages.crud_messages.is_active_update', ['attr' => 'Member']), 'data' => $request->is_active], 200);

            } else {

                return response()->json(['status' => 0, "message" => trans('pages.crud_messages.no_data', ['attr' => 'Member'])], 200);
            }
        } catch (Exception $e) {

            Log::info("status_update error " . print_r($e->getMessage(), true));

            return redirect()->back()->with(["success" => 0, "message" => trans('pages.something_wrong')]);

        }
    }

    public function view($member_id = "")
    {
      try{

        $auth_user = auth()->user();

        $member_info = MemberRepository::view_member_details($member_id);

        $member_data = array('member_id'=>$member_id,'user_type'=>config('custom.association_type_term.member'));

        // dd($active_sub->subscription_name);
        return view('pages.members.view', compact('member_info','auth_user'));

      }catch (Exception $e) {

            Log::info("view error  " . print_r($e->getMessage(), true));

            return redirect()->back()->with(["success" => 0, "message" => trans('pages.something_wrong')]);

        }
    }

    public function order_json_list(Request $request)
    {

        try {
            $page_index = (int) $request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

            $limit = (int) $request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
            $columnIndex = $request->input('order')[0]['column']; // Column index
            $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

            $main_query = TraOrders::select('order.order_id','order.total_amount','order.payment_status','order.discount_amount','order.final_total_amount','order.order_number','order.is_active','order.association_id','member.member_name','order.created_at','order.order_status','order.subtotal_amount')
                ->from('pb_tra_orders as order')
                ->leftjoin('pb_mst_members as member','member.member_id','=','order.association_id')
                ->where('order.is_active', 1)
                ->orderBy($columnName, $columnSortOrder);

                if ($request->member_id) {

                  $main_query->where('order.association_id', $request->member_id);
                }

            $data_list_for_count = $main_query->get();

            $recordsTotal = count($data_list_for_count);

            $recordsFiltered = $recordsTotal;

            if (empty($request->input('search.value'))) {
                $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);
            } else {
                $search = $request->input('search.value');
                $search_query = $main_query->where('order.total_amount', 'LIKE', "%{$search}%")
                    ->orWhere('order.subtotal_amount', 'LIKE', "%{$search}%")
                    ->orWhere('order.discount_amount', 'LIKE', "%{$search}%")
                    ->orWhere('order.final_total_amount', 'LIKE', "%{$search}%");

                $appointments = $search_query->paginate($limit, ['*'], 'page', $page_index);
                $search_list_for_count = $search_query->get();
                $recordsFiltered = count($search_list_for_count);
            }

            $response = array(
                "draw" => (int) $request->input('draw'),
                "recordsTotal" => (int) $recordsTotal,
                "recordsFiltered" => (int) $recordsFiltered,
                "data" => $appointments->getCollection(),
            );
            return response()->json($response, 200);
        } catch (\Exception $e) {
            Log::info("order_json_list" . print_r($e->getMessage(), true));
            $response = array(
                "draw" => (int) $request->input('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => (object)[],
                'error' => $e->getMessage()
            );

            return response()->json($response, 200);
        }
    }

    public function order_detail_list_json(Request $request)
    {

        try {

            $order_detail = TraOrdersDetails::select('member.member_name','member.profile_pic as member_img','club.profile_pic as club_image','order.order_id','order.order_detail_id','order.sub_order_id','order.item_type','order.total_amount','order.discount_amount','order.subtotal_amount','order.order_item_status','club.club_name','court.court_title','order.club_id','order.court_id')
                                        ->from('pb_tra_order_details as order')
                                        ->leftjoin('pb_mst_clubs as club','club.club_id','=','order.club_id')
                                        ->leftjoin('pb_mst_courts as court','court.court_id','=','order.court_id')
                                        ->leftjoin('pb_mst_members as member','member.member_id','=','order.trainer_id')
                                        ->where('order.order_id', $request->order_id)
                                        ->orderBy('order.order_detail_id', 'desc')
                                        ->get();
            $order_detail_arr = [];
            foreach ($order_detail as $key => $value) {
                $order_detail_arr[$value->sub_order_id][] = $value;
            }

            $view = view('pages.members.order_detail_view',compact('order_detail_arr'))->render();
            return response()->json(['status' => 1, 'view' => $view]);

        } catch (\Exception $e) {
            Log::info("order_json_list" . print_r($e->getMessage(), true));
            $response = array(
                "draw" => (int) $request->input('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => (object)[],
                'error' => $e->getMessage()
            );

            return response()->json($response, 200);
        }
    }

    public function get_member_post_list(Request $request)
    {
        try {

                $post_result = CommonRepository::get_post_list($request);

                $page = $request->page;

                $list = isset($post_result['list']) ? $post_result['list'] : [];
                $totalPages = isset($post_result['totalPages']) ? $post_result['totalPages'] : 0;

                $returnHTML = view('pages.members.member_post', compact('list','page'))->render();
                return response()->json(array('status' => 1, 'html'=> $returnHTML,'totalPages' => $totalPages) ,200);

        } catch (\Exception $e) {

            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
        }
    }

    public function get_member_followers_list(Request $request)
    {
        try {

                $page = $request->page;
                $followers = UserRepository::get_followers_members($request);

                $list = isset($followers['list']) ? $followers['list'] : [];
                $totalPages = isset($followers['totalPages']) ? $followers['totalPages'] : 0;

                $returnHTML = view('pages.members.followers', compact('list','page'))->render();
                return response()->json(array('status' => 1, 'html'=> $returnHTML ,'totalPages' => $totalPages ), 200);

        } catch (\Exception $e) {

            Log::info("get_member_followers_list   " . print_r($e->getMessage(), true));
            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
        }

    }

    public function get_member_following_list(Request $request)
    {
        try {
                $page = $request->page;
                $following = UserRepository::get_following_members($request);

                $list = isset($following['list']) ? $following['list'] : [];
                $totalPages = isset($following['totalPages']) ? $following['totalPages'] : 0;

                $returnHTML = view('pages.members.following', compact('list','page'))->render();
                return response()->json(array('status' => 1, 'html'=> $returnHTML,'totalPages' => $totalPages ), 200);

        } catch (\Exception $e) {

            Log::info("get_member_following_list  " . print_r($e->getMessage(), true));
            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
        }

    }

    public function get_post_detail(Request $request)
    {
        try {
                $validation_rules = [
                    'post_id' => 'required',
                    'member_id' => 'required',
                ];

                $validator = Validator::make($request->all(), $validation_rules);

                if ($validator->fails()) {

                    return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()), "data" => (object) []]);

                } else {

                    $post = CommonRepository::get_post_detail($request,$request->post_id, $request->member_id);

                    $returnHTML = view('pages.members.post_detail', compact('post'))->render();
                    return response()->json(array('status' => 1, 'html'=> $returnHTML ), 200);
                }

        } catch (\Exception $e) {

            Log::info("get_post_detail  " . print_r($e->getMessage(), true));
            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
        }

    }

    public function get_liked_member_list(Request $request)
    {
        try {
                $validation_rules = [
                    'post_id' => 'required',
                    'member_id' => 'required',
                ];

                $validator = Validator::make($request->all(), $validation_rules);

                if ($validator->fails()) {

                    return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()), "data" => (object) []]);

                } else {

                    $response = MemberRepository::get_liked_users($request, $request->member_id);

                    $page = $request->page;

                    $list = [];
                    $totalPages = 1;

                    if(isset($response['data'])){
                        $list = isset($response['data']['list']) ? $response['data']['list'] : [];
                        $totalPages = isset($response['data']['totalPages']) ? $response['data']['totalPages'] : 1;
                    }

                    $returnHTML = view('pages.members.liked_members_list', compact('list','page'))->render();
                    return response()->json(array('status' => 1, 'html'=> $returnHTML,'totalPages' => $totalPages ), 200);
                }

        } catch (\Exception $e) {

            Log::info("get_liked_member_list  " . print_r($e->getMessage(), true));
            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
        }

    }

    public function get_commented_member_list(Request $request)
    {
        try {
                $validation_rules = [
                    'post_id' => 'required',
                    'member_id' => 'required',
                ];

                $validator = Validator::make($request->all(), $validation_rules);

                if ($validator->fails()) {

                    return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()), "data" => (object) []]);

                } else {

                    $response = MemberRepository::get_comments($request, $request->post_id);

                    $page = $request->page;

                    $list = [];
                    $totalPages = 1;

                    if(isset($response['data'])){
                        $list = isset($response['data']['list']) ? $response['data']['list'] : [];
                        $totalPages = isset($response['data']['totalPages']) ? $response['data']['totalPages'] : 1;
                    }

                    $returnHTML = view('pages.members.commented_members', compact('list','page'))->render();
                    return response()->json(array('status' => 1, 'html'=> $returnHTML,'totalPages' => $totalPages ), 200);
                }

        } catch (\Exception $e) {

            Log::info("get_commented_member_list  " . print_r($e->getMessage(), true));
            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
        }

    }

    public function get_interested_member_list(Request $request)
    {
        try {
                $validation_rules = [
                    'post_id' => 'required',
                    'member_id' => 'required',
                ];

                $validator = Validator::make($request->all(), $validation_rules);

                if ($validator->fails()) {

                    return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()), "data" => (object) []]);

                } else {

                    $response = MemberRepository::interested_user_list($request, $request->member_id);

                    $page = $request->page;

                    $list = [];
                    $totalPages = 1;

                    if(isset($response['data'])){
                        $list = isset($response['data']['list']) ? $response['data']['list'] : [];
                        $totalPages = isset($response['data']['totalPages']) ? $response['data']['totalPages'] : 1;
                    }

                    $returnHTML = view('pages.members.interested_users', compact('list','page'))->render();
                    return response()->json(array('status' => 1, 'html'=> $returnHTML,'totalPages' => $totalPages ), 200);
                }

        } catch (\Exception $e) {

            Log::info("get_commented_member_list  " . print_r($e->getMessage(), true));
            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
        }

    }

    public function get_member_subscription_list(Request $request)
    {
        try {
                $validation_rules = [
                    'member_id' => 'required',
                ];

                $validator = Validator::make($request->all(), $validation_rules);

                if ($validator->fails()) {

                    return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()), "data" => (object) []]);

                } else {

                    $response = UserRepository::get_user_subscription_list($request);


                    return response()->json($response);
                }

        } catch (\Exception $e) {

            Log::info("get_commented_member_list  " . print_r($e->getMessage(), true));
            return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
        }

    }

}
