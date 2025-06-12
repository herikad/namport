<?php

namespace App\Repositories;

use App\Models\Member;

use Log;
use DB;

class MemberRepository
{
    public static function member_json_list($request)
    {
        try {

            $auth_user = auth()->user();
            $page_index = (int) $request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

            $limit = (int) $request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
            $columnIndex = $request->input('order')[0]['column']; // Column index
            $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

            $main_query = Member::select('member.member_id', 'member.member_name', 'member.email', 'member.country_code', 'member.phone', 'member.profile_pic', 'member.is_active','member.member_type','member.created_at','member.pickleball_level_term','member.avg_rating','member.gender_type_term','member.deleted_at')
                                    ->from('pb_mst_members as member')
                                    // ->where('member.is_deleted',0)
                                    ->orderBy($columnName, $columnSortOrder);

            if (empty($request->input('search.value'))) {
                $list = $main_query->paginate($limit, ['*'], 'page', $page_index);
                $recordsTotal = $list->total();
                $recordsFiltered = $list->total();
            } else {
                $search = $request->input('search.value');
                // $search_query = $main_query->where('member.member_name', 'LIKE', "%{$search}%")
                //     ->orWhere('member.email', 'LIKE', "%{$search}%")
                //     ->orWhere('member.phone', 'LIKE', "%{$search}%");

                $search_query = $main_query->where(function ($query) use ($search) {
                    $query->where('member.member_name', 'LIKE', "%{$search}%")
                            ->orWhere('member.email', 'LIKE', "%{$search}%")
                            ->orWhere('member.phone', 'LIKE', "%{$search}%");
                });

                $list = $search_query->paginate($limit, ['*'], 'page', $page_index);
                $recordsTotal = $list->total();
                $recordsFiltered = $list->total();
            }

            $response = array(
                "draw" => (int) $request->input('draw'),
                "recordsTotal" => (int) $recordsTotal,
                "recordsFiltered" => (int) $recordsFiltered,
                "data" => $list->getCollection(),
            );
            return response()->json($response, 200);
        } catch (\Exception $e) {
            Log::info("memberepository:member_json_list" . print_r($e->getMessage(), true));
            $response = array(
                "draw" => (int) $request->input('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => (object) [],
                'error' => $e->getMessage()
            );

            return response()->json($response, 200);
        }
    }

    public static function view_member_details($member_id = "")
    {
        try {
            $membertview = Member::from('pb_mst_members as member')
                                  ->select('member.*','usr.stripe_connect_account_status')
                                  ->join('pb_users as usr', function ($join) {
                                      $join->on('usr.association_id', 'member.member_id');
                                  })->where('member.member_id',$member_id)->first();

            return $membertview;

        } catch (\Throwable $th) {
            Log::info('view page error on membercontroller' . print_r($th->getMessage(), true));
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

}
