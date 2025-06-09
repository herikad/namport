<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;


class DepartmentController extends Controller
{
   public function index(Request $request){
        return view('pages.department.index');
   }

   public function department_json_list(Request $request){
    // try {
        $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;
      
        $limit = (int)$request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
        $columnIndex = $request->input('order')[0]['column']; // Column index
        $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

        $main_query =  Department::from('mst_department as department')
                                ->join('mst_client as client', 'department.client_id', '=','client.client_id')
                                ->select(
                                    'department.department_id',
                                    'department.department_name',
                                    'department.department_code',
                                    'department.is_active',
                                    'department.created_at',
                                    'department.client_id',
                                    'client.company_name'
                                );

                                 
        $main_query =  $main_query->orderBy($columnName, $columnSortOrder);
    
        $data_list_for_count = $main_query->get();  // group by and direct count not working
        $recordsTotal = count($data_list_for_count);

        $recordsFiltered = $recordsTotal;
    
        if(empty($request->input('search.value'))){
    
            $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);
    
        }else {
    
            $search = $request->input('search.value');
    
            $search_query = $main_query->where(function ($query) use ($search) {
                $query->where('department.department_code', 'LIKE', "%{$search}%")
                    ->orWhere('department.department_name', 'LIKE', "%{$search}%")
                    ->orWhere('department.short_description', 'LIKE', "%{$search}%")
                    ->orWhere('client.company_name', 'LIKE', "%{$search}%");
            });
    
            $appointments = $search_query->paginate($limit, ['*'], 'page', $page_index);
    
            $search_list_for_count = $search_query->get();  // group by and direct count not working
    
            $recordsFiltered = count($search_list_for_count);
    
        }
    
        $response = array(
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => (int)$recordsTotal,
            "recordsFiltered" => (int)$recordsFiltered,
            "data" => $appointments->getCollection()
        );
    
        return response()->json($response, 200);
    // } catch (\Throwable $th) {
    //     //throw $th;
    // }
   }

    public function status_update(Request $request)
    {
        try {
            $status_update = Department::where('department_id', $request->department_id)->first();

            if ($status_update) {

                $status_update->is_active = $request->is_active;
                $status_update->save();

                return response()->json(['status' => 1, 'message' => trans('pages.crud_messages.is_active_update', ['attr' => 'Department']), 'data' => $request->is_active], 200);

            } else {

                return response()->json(['status' => 0, "message" => trans('pages.crud_messages.no_data', ['attr' => 'Department'])], 200);
            }
        } catch (Exception $e) {

            Log::info("status_update error " . print_r($e->getMessage(), true));

            return redirect()->back()->with(["success" => 0, "message" => trans('pages.something_wrong')]);

        }
    }
}
