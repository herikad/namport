<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Validator;
use App\Models\User;
use App\Models\Club;
use App\Models\AssignClub;
use App\Models\Member;
use App\Models\TraOrdersDetails;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    try {

        $auth_user = auth()->user();

        $total_member = 0;

        $month_start_date = date("Y-m-d", strtotime("first day of previous month"));      // get utc date of month start date
        $month_end_date =  date("Y-m-d", strtotime("last day of previous month"));        // get utc date of month's last date

        $total_member_query = DB::table('pb_mst_members as member');
                                // ->where('member.is_active', 1)
                                // ->where('member.is_deleted', 0);
        $total_member = $total_member_query->count();

        $count_arr = [
          'total_member' => $total_member,
        ];

        return view('pages.dashboard.admin.index',compact('count_arr','auth_user'));


    } catch (Exception $exception) {

        Log::info("index_error ". print_r($exception->getMessage(), true));

        return redirect()->back()->with( "message" , trans('pages.something_wrong'));
    }
  }

  public function get_analytics_chart_details(Request $request)
  {
      try {

          $validation_rules = [

              'year' => 'required',

          ];

          $validator = Validator::make($request->all(), $validation_rules);

          if($validator->fails()) {

              return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()) ]);

          }else{

              $year = $request->year;

              $months =['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep','Oct' ,'Nov' ,'Dec'] ;

              $result =[
                  'records' => [],
                  'total' => 0,
                  'max_value' => 0,
              ];

              foreach ($months as $key => $month) {

                  $month_key = $key + 1 ;

                  $count = Member::whereMonth('created_at' ,sprintf("%02d", $month_key) )
                                    ->whereYear('created_at' , $year)
                                    // ->where('is_active' ,1)
                                    // ->where('is_deleted' ,0)
                                    ->count();

                  $result['records'][$month] = $count;
                  $result['total'] += $count;
                  $result['max_value'] = $result['max_value']  > $count ? $result['max_value'] : $count ;

              }

              $returnHTML = view('pages.dashboard.admin.analytics-chart')->render();

              return response()->json(array('status' => 1, 'html'=> $returnHTML ,'records' => $result['records'] ,'total' => $result['total'] ,'max_value' => $result['max_value']), 200);

          }

      } catch (\Exception $e) {

          Log::info("index_error ". print_r($e->getMessage(), true));
          return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
      }
  }

  public function get_assigned_club(Request $request)
  {
    try {

      $auth_user = auth()->user();
      $page_index = (int) $request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

      $limit = (int) $request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
      $columnIndex = $request->input('order')[0]['column']; // Column index
      $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
      $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

      $main_query = AssignClub::select('club.club_id', 'club.club_name','club.address','club.approval_status','manager.club_manager_name')
                          ->from('pb_assign_clubs as assign')
                          ->leftjoin('pb_mst_clubs as club','club.club_id','=','assign.club_id')
                          ->leftjoin('pb_mst_club_managers as manager','manager.club_manager_id','=','club.association_id')
                          ->where('club.is_delete', 0)
                          ->where('assign.association_id', $auth_user->association_id)
                          ->where('assign.association_type_term',config('custom.association_type_term.club_user'))
                          ->orderBy($columnName, $columnSortOrder);

      $data_list_for_count = $main_query->get();
      // group by and direct count not working
      $recordsTotal = count($data_list_for_count);

      $recordsFiltered = $recordsTotal;

      if (empty($request->input('search.value'))) {
          $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);
      } else {
            $search = $request->input('search.value');
            // $search_query = $main_query->where('club.club_name', 'LIKE', "%{$search}%")
            //     ->orWhere('club.phone_no', 'LIKE', "%{$search}%")
            //     ->orWhere('club.zipcode', 'LIKE', "%{$search}%")
            //     ->orWhere('club.address', 'LIKE', "%{$search}%")
            //     ->orWhere('club.city', 'LIKE', "%{$search}%")
            //     ->orWhere('club.state', 'LIKE', "%{$search}%");

            $search_query = $main_query->where(function ($query) use ($search) {
                $query->where('club.phone_no', 'LIKE', "%{$search}%")
                        ->orWhere('club.zipcode', 'LIKE', "%{$search}%")
                        ->orWhere('club.club_name', 'LIKE', "%{$search}%")
                        ->orWhere('club.address', 'LIKE', "%{$search}%")
                        ->orWhere('club.city', 'LIKE', "%{$search}%")
                        ->orWhere('club.state', 'LIKE', "%{$search}%");
            });

          $appointments = $search_query->paginate($limit, ['*'], 'page', $page_index);
          $search_list_for_count = $search_query->get(); // group by and direct count not working
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
        Log::info("get_assigned_club   " . print_r($e->getMessage(), true));
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

  public function get_analytics_order_chart_details(Request $request)
  {
      try {

          $validation_rules = [

              'year' => 'required',

          ];

          $validator = Validator::make($request->all(), $validation_rules);

          if($validator->fails()) {

              return response()->json(['status' => 0, 'message' => implode(',', $validator->messages()->all()) ]);

          }else{

              $year = $request->year ;

              $months =['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep','Oct' ,'Nov' ,'Dec'] ;

              $result =[
                  'records' => [],
                  'total' => 0,
                  'max_value' => 0,
              ];

              foreach ($months as $key => $month) {

                  $month_key = $key + 1 ;

                  $count = TraOrdersDetails::whereMonth('created_at' ,sprintf("%02d", $month_key) )
                                            ->whereYear('created_at' , $year)
                                            ->where('order_item_status', '!=',config('custom.order_item_status.payment_requested'))
                                            ->groupBy('sub_order_id')
                                            ->get()
                                            ->count();

                  $result['records'][$month] = $count;
                  $result['total'] += $count;
                  $result['max_value'] = $result['max_value']  > $count ? $result['max_value'] : $count ;

              }

              $returnHTML = view('pages.dashboard.admin.order-chart')->render();

              return response()->json(array('status' => 1, 'html'=> $returnHTML ,'records' => $result['records'] ,'total' => $result['total'] ,'max_value' => $result['max_value']), 200);

          }

      } catch (\Exception $e) {

          Log::info("index_error ". print_r($e->getMessage(), true));
          return response()->json(['status' => 0, 'message' => trans('pages.something_wrong'), 'error' => $e->getMessage()]);
      }
  }

}
