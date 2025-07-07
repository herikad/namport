<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientContacts;
use App\Models\Employee;
use App\Models\MstMiaAgent;
use App\Models\MstProcessFramework;
use App\Models\Project;
use App\Models\ProjectProcessFramework;
use App\Models\ProjectTeam;
use App\Models\ProcessMapping;
use App\Models\MettingSchedules;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Log;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        try {

            $client_id                = config('custom.default_client_id');
            $client                   = \Helper::getClientCustomer($client_id);
            $data['customer_id']      = $client ? $client->customer_id : null;
            $data['client_id']        = $client_id;
            $data['billing_terms']    = config('custom.billing_term');
            $data['status']           = config('custom.profile_status_term');
            $data['project_managers'] = Employee::where('is_active', 1)
                ->where('is_admin', 0)
            // ->where('is_non_staffmember', 1) // Uncomment if needed
                ->orderBy('display_name')
                ->pluck('display_name', 'employee_id')
                ->toArray();

            $data['level_category'] = ['APQC' => 'APQC', 'BPMN' => 'BPMN', 'SIX_SIGMA' => 'SIX SIGMA', 'ISO_9001' => 'ISO 9001'];

            $data['frameworkData'] = MstProcessFramework::where('is_active', 1)
                ->where('client_id', $client_id)
                ->orderBy('process_framework_term')
                ->orderBy('levelno')
                ->get()
                ->groupBy('process_framework_term')
                ->map(function ($group) {
                    return $group->map(function ($item) {
                        return [
                            'levelno'              => $item->levelno,
                            'levelname'            => $item->levelname,
                            'process_framework_id' => $item->process_framework_id,
                            'shortdescription'     => $item->shortdescription,
                        ];
                    });
                })->toArray();

            $data['our_teams'] = Employee::where('is_active', 1)
                ->where('is_admin', 0)
            // ->where('is_non_staffmember', 1) // Uncomment if you want to filter non-staff members
                ->select('employee_id as id', 'display_name as name')
                ->orderBy('display_name')
                ->where('customer_id', $data['customer_id'])
                ->get()
                ->toArray();

            $data['client_teams'] = ClientContacts::where('profile_status_term', config('custom.profile_status_term.completed'))
                ->where('is_active', 1)
                ->where('status_term', config('custom.status_term.active'))
                ->select('client_contacts_id as id', 'display_name as name')
                ->orderBy('display_name')
                ->where('customer_id', $data['customer_id'])
                ->get()
                ->toArray();

            $agents = MstMiaAgent::where('is_active', 1)
                ->where('customer_id', $data['customer_id'])
                ->orderBy('mia_agent_id')
                ->get(['mia_agent_id as id', 'nameofagent as name', 'agentpersonafile as image'])
                ->toArray();

            $data['mia_agents'] = $agents;

            $data['send_notificaiton_term'] = config('custom.send_notificaiton_term');

            $data['designation_term'] = config('custom.designation_term');

            return view('pages.project.index', $data);
        } catch (\Exception $e) {
            Log::info("project_index_error " . print_r($e->getMessage(), true));
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $rules = ['projectname' => 'required', 'client_id' => 'required', 'projectmanager_id' => 'required'];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 0,
                    'success' => false,
                    'message' => implode(',', $validator->messages()->all()),
                ], 200);

            } else {

                $project                                        = new Project;
                $project->projectname                           = $request->projectname;
                $project->customer_id                           = $request->customer_id;
                $project->client_id                             = $request->client_id;
                $project->projectmanager_id                     = $request->projectmanager_id;
                $project->billingtype_term                      = $request->billingtype_term;
                $project->status_term                           = $request->status_term;
                $project->startdate                             = $request->startdate;
                $project->enddate                               = $request->enddate;
                $project->shortdetail                           = $request->shortdetail;
                $project->sendnotification_term                 = $request->sendnotification_term;
                $project->allowcustomertasks                    = isset($request->allowcustomertasks) && $request->allowcustomertasks == 'on' ? 1 : 0;
                $project->allowcustomertoedittask               = isset($request->allowcustomertoedittask) && $request->allowcustomertoedittask == 'on' ? 1 : 0;
                $project->allowcustomertocommentonprojecttask   = isset($request->allowcustomertocommentonprojecttask) && $request->allowcustomertocommentonprojecttask == 'on' ? 1 : 0;
                $project->allowcustomertouploadattachmentontask = isset($request->allowcustomertouploadattachmentontask) && $request->allowcustomertouploadattachmentontask == 'on' ? 1 : 0;
                $project->allowcustomertoviewloggedhrs          = isset($request->allowcustomertoviewloggedhrs) && $request->allowcustomertoviewloggedhrs == 'on' ? 1 : 0;
                $project->allowcustomertouploadfile             = isset($request->allowcustomertouploadfile) && $request->allowcustomertouploadfile == 'on' ? 1 : 0;
                $project->allowcustomertoviewteams              = isset($request->allowcustomertoviewteams) && $request->allowcustomertoviewteams == 'on' ? 1 : 0;
                $project->created_by                            = auth()->user()->association_id;
                $project->save();

                if (isset($request->logo_image) && $request->hasFile('logo_image')) {
                    $file            = $request->file('logo_image');
                    $image_name      = 'project_logo_' . time() . "." . $file->getClientOriginalExtension();
                    $destinationPath = ('images/project/logo');

                    \Helper::upload_file($request->file('logo_image'), $image_name, $destinationPath);

                    Project::where("project_id", $project->project_id)->update([
                        'logo_image' => $image_name,
                    ]);
                }

                if (isset($request->banner_image) && $request->hasFile('banner_image')) {
                    $file              = $request->file('banner_image');
                    $banner_image_name = 'project_banner_' . time() . "." . $file->getClientOriginalExtension();
                    $destinationPath   = ('images/project/banner');

                    \Helper::upload_file($request->file('banner_image'), $banner_image_name, $destinationPath);

                    Project::where("project_id", $project->project_id)->update([
                        'banner_image' => $banner_image_name,
                    ]);
                }

                if (isset($request->level_selected_tab) && $project) {

                    $levels = MstProcessFramework::where('is_active', 1)
                        ->where('client_id', $request->client_id)
                        ->where('process_framework_term', $request->level_selected_tab)
                        ->orderBy('levelno')
                        ->get();

                    if (isset($levels) && ! empty($levels)) {
                        foreach ($levels as $key => $level) {
                            $level_store                           = new ProjectProcessFramework;
                            $level_store->project_id               = $project->project_id;
                            $level_store->project_framework_id     = $level->process_framework_id;
                            $level_store->ref_process_framework_id = $level->ref_process_framework_id;
                            $level_store->client_id                = $level->client_id;
                            $level_store->customer_id              = $level->customer_id;
                            $level_store->level_no                 = $level->levelno;
                            $level_store->level_name               = $level->levelname;
                            $level_store->level_detail             = $level->shortdescription;
                            $level_store->created_by               = auth()->user()->association_id;
                            $level_store->save();
                        }
                    }
                }

                if (isset($request->our_team) && ! empty($request->our_team)) {
                    foreach ($request->our_team as $key => $our_team_id) {
                        $our_team_designation        = isset($request->our_team_designation) ? $request->our_team_designation : [];
                        $our_project_role_term       = isset($request->our_project_role_term) ? $request->our_project_role_term : [];
                        $team                        = new ProjectTeam;
                        $team->project_id            = $project->project_id;
                        $team->client_id             = $project->client_id;
                        $team->customer_id           = $project->customer_id;
                        $team->association_id        = $our_team_id;
                        $team->association_type_term = config('custom.association_type_term.our_team');
                        $team->designation           = $our_team_designation[$key] ?? null;
                        $team->project_role_term     = $our_project_role_term[$key] ?? null;
                        $team->assigned_at           = date('Y-m-d H:i:s');
                        $team->assigned_by           = auth()->user()->association_id;
                        $team->save();
                    }
                }

                if (isset($request->client_team) && ! empty($request->client_team)) {
                    foreach ($request->client_team as $key => $client_team_id) {
                        $client_team_designation     = isset($request->client_designation) ? $request->client_designation : [];
                        $client_project_role_term    = isset($request->client_project_role_term) ? $request->client_project_role_term : [];

                        $team                        = new ProjectTeam;
                        $team->project_id            = $project->project_id;
                        $team->client_id             = $project->client_id;
                        $team->customer_id           = $project->customer_id;
                        $team->association_id        = $client_team_id;
                        $team->association_type_term = config('custom.association_type_term.client_contact');
                        $team->designation           = $client_team_designation[$key] ?? null;
                        $team->project_role_term     = $client_project_role_term[$key] ?? null;
                        $team->assigned_at           = date('Y-m-d H:i:s');
                        $team->assigned_by           = auth()->user()->association_id;
                        $team->save();
                    }
                }

                if (isset($request->mia_agents) && ! empty($request->mia_agents)) {
                    foreach ($request->mia_agents as $key => $mia_agent_id) {
                        $team                        = new ProjectTeam;
                        $team->project_id            = $project->project_id;
                        $team->client_id             = $project->client_id;
                        $team->customer_id           = $project->customer_id;
                        $team->association_id        = $mia_agent_id;
                        $team->association_type_term = config('custom.association_type_term.mia_agent');
                        $team->project_role_term     = 'MIA_AGENT';
                        $team->assigned_at           = date('Y-m-d H:i:s');
                        $team->assigned_by           = auth()->user()->association_id;
                        $team->save();
                    }
                }

                DB::commit();

                return response()->json([
                    'status'       => 1,
                    'success'      => true,
                    'message'      => 'Project created has been successfully',
                    'redirect_url' => route('project.index'),
                ], 200);

            }

        } catch (\Exception $e) {

            DB::rollBack();

            Log::info("project_store_error " . print_r($e->getMessage(), true));
            return redirect()->route('project.index')->with('error', 'Something Went Wrong!');
        }
    }

    public function project_json_list(Request $request){

        try {
            $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

            $limit = (int)$request->input('length') > 0 ? $request->input('length') : 10;
            $columnIndex = $request->input('order')[0]['column']; // Column index
            $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
            $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

            $main_query =  Project::from('prj_project as project')
                                    ->join('mst_client as client', 'project.client_id', '=','client.client_id')
                                    ->select(
                                        'project.project_id',
                                        'project.customer_id',
                                        'project.projectname',
                                        'project.progress',
                                        'project.startdate',
                                        'project.enddate',
                                        'project.is_active',
                                        'project.created_at',
                                        'project.client_id',
                                        'project.status_term',
                                        'client.company_name',
                                    )->with([
                                        'ourTeam',
                                        'ourClient',
                                        'miaAgent',
                                    ]);

            $main_query =  $main_query->orderBy($columnName, $columnSortOrder);

            $data_list_for_count = $main_query->get();  // group by and direct count not working
           
            $recordsTotal = count($data_list_for_count);

            $recordsFiltered = $recordsTotal;

            if(empty($request->input('search.value'))){

                $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);

            }else {

                $search = $request->input('search.value');

                $search_query = $main_query->where(function ($query) use ($search) {
                    $query->where('project.projectname', 'LIKE', "%{$search}%")
                        ->orWhere('client.company_name', 'LIKE', "%{$search}%");
                });

                $appointments = $search_query->paginate($limit, ['*'], 'page', $page_index);

                $search_list_for_count = $search_query->get();  // group by and direct count not working

                $recordsFiltered = count($search_list_for_count);

            }

            foreach ($appointments as $key => $project) {
                $project->enc_project_id = \Helper::enc($project->project_id);
            }

            $response = array(
                "draw" => (int)$request->input('draw'),
                "recordsTotal" => (int)$recordsTotal,
                "recordsFiltered" => (int)$recordsFiltered,
                "data" => $appointments->getCollection()
            );

            return response()->json($response, 200);
        } catch (\Exception $e) {
            Log::info("project_json_list_error ". print_r($e->getMessage(), true));
            return redirect()->route('project.index')->with('error', 'Something Went Wrong!');
        }

    }

    public function edit($id){

        try {

             $project_id = \Helper::dnc($id);

             $data['project_details'] = Project::where('project_id', $project_id)->first();

             if (isset($data['project_details'])) {
                $data['static_tabs'] = [
                    ['key' => 'overview', 'label' => 'Overview'],
                    ['key' => 'activities', 'label' => 'Activities'],
                    ['key' => 'repository', 'label' => 'Repository'],
                    ['key' => 'calendar', 'label' => 'Calendar', 'note' => 'Complete Step 2 to enable'],
                ];
                $data['levels'] = ProjectProcessFramework::where('project_id',$project_id)->orderBy('level_no')->select('level_no','level_name')->get();

                return view('pages.project.edit', $data);
             }

        } catch (\Exception $e) {
            Log::info("project_edit_list_error ". print_r($e->getMessage(), true));
            return redirect()->route('project.index')->with('error', 'Something Went Wrong!');
        }

    }

    public function load_static_tab($project_id,$tab)
    {
        $data = [];
        $project_id = \Helper::dnc($project_id);
        if (!in_array($tab, ['overview', 'activities', 'repository', 'calendar'])) {
            abort(404);
        }
        if ($tab == 'overview') {
            $data['project_details'] = Project::where('project_id', $project_id)->first();
            $customer_id = $data['project_details']->customer_id;

            $data['project_manager'] = Employee::where('employee_id', $data['project_details']->projectmanager_id)->first();
            $departments             = ProjectTeam::from('prj_projectteam as team')
                                                    ->join('mst_client_contacts as client', 'client.client_contacts_id', '=', 'team.association_id')
                                                    ->join('mst_department as dept', 'dept.department_id', '=', 'client.department')
                                                    ->where('team.project_id', $project_id)
                                                    ->where('team.association_type_term', config('custom.association_type_term.client_contact'))
                                                    ->distinct()
                                                    ->pluck('dept.department_name') 
                                                    ->unique()
                                                    ->values();
            $data['departments']    = $departments->implode(', ');

            $data['our_teams']      = Employee::where('is_active', 1)
                                                ->where('is_admin', 0)
                                                // ->where('is_non_staffmember', 1) // Uncomment if you want to filter non-staff members
                                                ->select('employee_id as id', 'display_name as name', 'profilepicture')
                                                ->orderBy('display_name')
                                                ->where('customer_id', $customer_id)
                                                ->get();

            $data['exsting_teams'] = ProjectTeam::where('project_id', $project_id)
                                                ->get()
                                                ->groupBy('association_type_term')
                                                ->map(function ($items) {
                                                     return $items->map(function ($item) {
                                                        return [
                                                            'association_id'  => $item->association_id,
                                                            'designation'     => $item->designation,
                                                        ];
                                                    })->toArray();
                                                })
                                                ->toArray();

            $data['client_teams'] = ClientContacts::where('profile_status_term', config('custom.profile_status_term.completed'))
                                                    ->where('is_active', 1)
                                                    ->where('status_term', config('custom.status_term.active'))
                                                    ->select('client_contacts_id as id', 'display_name as name','profile_pic')
                                                    ->orderBy('display_name')
                                                    ->where('customer_id', $customer_id)
                                                    ->get();

            $data['agents'] = MstMiaAgent::where('is_active', 1)
                                    ->where('customer_id', $customer_id)
                                    ->orderBy('mia_agent_id')
                                    ->get(['mia_agent_id as id', 'nameofagent as name', 'agentpersonafile as image']);


        }
        return view("pages.project.tabs.$tab", $data);
    }

    public function load_dynamic_tab($project_id,$level_no)
    {
        $project_id = \Helper::dnc($project_id);
        $data = [];
        $data['project_id'] = $project_id;
        if ($level_no == '1') {

            $data['level'] = ProjectProcessFramework::where('level_no', $level_no)->where('project_id', $project_id)->first();
            $data['dynamicColumns'] =  ProjectProcessFramework::where('project_id', $project_id)->where('level_no','!=',$level_no)->select('level_no','level_name')->get()->toArray();

            $lastId = ProcessMapping::where('project_id', $project_id)->where('level1id','!=',NULL)->count();
            $data['process_id'] = $level_no .'.' . ($lastId ? ($lastId) : '');
            $data['stake_holders'] = ProjectTeam::where('project_id', $project_id)
                                                ->where('association_type_term', config('custom.association_type_term.client_contact'))
                                                 ->with('clientContact')
                                                 ->get();

            $data['employees'] = ProjectTeam::where('project_id', $project_id)
                                                ->where('association_type_term', config('custom.association_type_term.our_team'))
                                                 ->with('employee')
                                                 ->get();

            $data['mia_agents'] = ProjectTeam::where('project_id', $project_id)
                                            ->where('association_type_term', config('custom.association_type_term.mia_agent'))
                                            ->with('miaAgent')
                                            ->get();
            $data['status_term']   = config('custom.profile_status_term');
            $data['priority_term']   = config('custom.peiority_term');

            $data['workflows'] = [
                (object)['id' => 1, 'name' => 'Welcome Call'],
                (object)['id' => 2, 'name' => 'Abandoned Cart Follow-up'],
                (object)['id' => 3, 'name' => 'Feedback Collection'],
                (object)['id' => 4, 'name' => 'Reactivation Campaign'],
                (object)['id' => 5, 'name' => 'Order Confirmation'],
            ];


        }

        $view = "pages.project.tabs.level_$level_no";

        if (!view()->exists($view)) {
            return "<div class='alert alert-warning'>View for level $level_no not found.</div>";
        }

        return view($view,$data);
    }

    public function store_project_member(Request $request){
        $team                        = new ProjectTeam;
        $team->project_id            = $request->project_id;
        $team->client_id             = $request->client_id;
        $team->customer_id           = $request->customer_id;
        $team->association_id        = $request->member_id;
        $team->association_type_term = $request->association_type_term;
        $team->designation           = $request->designation;
        $team->project_role_term     = $request->association_type_term;
        $team->assigned_at           = date('Y-m-d H:i:s');
        $team->assigned_by           = auth()->user()->association_id;
        $team->save();

        if ($request->association_type_term == config('custom.association_type_term.our_team')) {
           $message = "Team member added successfully.";
        } else {
           $message = "Client added successfully.";
        }

        return response()->json([
            'status'       => 1,
            'success'      => true,
            'message'      => $message
        ], 200);
    }

    public function remove_project_member(Request $request){
        $record = ProjectTeam::where([
            'project_id'            => $request->project_id,
            'association_id'        => $request->association_id,
            'association_type_term' => $request->association_type_term,
        ])->first();

        if (!$record) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Record not found.'
            ], 404);
        }

        $record->delete();

        return response()->json([
            'status'       => 1,
            'success'      => true,
            'message'      => 'Member removed successfully.'
        ], 200);
    }

    public function level_1_store(Request $request){
     //    try {
        $project = Project::where('project_id', $request->project_id)->first();
        if ($project) {
            $process = new ProcessMapping;
            $process->project_id  = $request->project_id;
            $process->client_id  = $project->client_id;
            $process->customer_id  = $project->customer_id;
            $process->workflow_id  = $request->workflow_id;
            $process->level1id  = $request->level1id;
            $process->process_id  = $request->process_id;
            $process->processno  = $request->processno;
            $process->processname  = $request->processname;
            $process->processdetail  = $request->processdetail;
            $process->detail_description  = $request->detail_description;
            $process->employee_id  = $request->employee_id;
            $process->mia_id  = $request->mia_id;
            $process->status_term  = $request->status_term;
            $process->priority_term  = $request->priority_term;
            $process->teamids  = json_encode($request->teamids);
            $process->save();

            if (isset($request->attachments)) {
                $uploaded_paths = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $attachment) {
                        if ($attachment->isValid()) {
                            $unique_name = 'attachment_' . rand(100, 9999) . time() . '.' . $attachment->getClientOriginalExtension();

                            $path = \Helper::upload_file(
                                $attachment,
                                $unique_name,
                                'uploads/attachments' 
                            );

                            $uploaded_paths[] = $path; 
                        }
                    }
                }
                $process->attachments_id = json_encode($uploaded_paths);
                $process->save();
            }

            // if ($process) {
            //     $meeting_schedules = new MettingSchedules;
            //     $meeting_schedules->association_id = $process->id;
            //     $meeting_schedules->association_type_term = "prj_processmaping";
            //     $meeting_schedules->project_id  = $request->project_id;
            //     $meeting_schedules->client_id  = $project->client_id;
            //     $meeting_schedules->customer_id  = $project->customer_id;
            //     $meeting_schedules->mia_agent_id  = $request->mia_id;
            //     $meeting_schedules->save();
            // }

            return response()->json([
                'status' => 1,
                'success' => true,
                'message' => 'Level 1 store sucessfuly.'
            ], 200);

        }
        
        // } catch (\Throwable $th) {
        //     Log::info("level_1_error ". print_r($e->getMessage(), true));
            // return response()->json([
            //     'message' => 'something went wrong'
            // ], 200);
        // }
    }

    // public function level_json_data(Request $request){

    //         // $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

    //         // $limit = (int)$request->input('length') > 0 ? $request->input('length') : 10;
    //         // $columnIndex = $request->input('order')[0]['column']; // Column index
    //         // $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
    //         // $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc value

    //         $main_query =  ProcessMapping::from('prj_processmaping as processmaping')
    //                                 ->where('project_id', $request->project_id)
    //                                 ->join('mst_miaagents as mia', 'mia.mia_agent_id', '=','processmaping.mia_id')
    //                                 ->join('mst_employee as employee', 'employee.employee_id', '=','processmaping.employee_id');
    //         if ($request->level_no == '1') {
    //           $main_query =  $main_query->where('level1id', $request->proj_process_framework_id);
    //         }
    //          $main_query =  $main_query->select(
    //                                     'processmaping.process_id',
    //                                     'processmaping.processname',
    //                                     'mia.nameofagent',
    //                                     'mia.agentpersonafile',
    //                                     'employee.display_name as employee_name',
    //                                     'processmaping.priority_term',
    //                                     'processmaping.progress',
    //                                     'processmaping.level1id',
    //                                     'processmaping.level2id',
    //                                     'processmaping.level3id',
    //                                     'processmaping.level4id',
    //                                     'processmaping.level5id',
    //                                     'processmaping.level6id',
    //                                     'processmaping.level7id'
    //                                 );


    //         // $main_query =  $main_query->orderBy($columnName, $columnSortOrder);

    //         $data_list_for_count = $main_query->get();  // group by and direct count not working

    //         $recordsTotal = count($data_list_for_count);

    //         $recordsFiltered = $recordsTotal;

    //         if(empty($request->input('search.value'))){

    //             $appointments = $main_query->paginate($limit, ['*'], 'page', $page_index);

    //         }else {

    //             $search = $request->input('search.value');

    //             $search_query = $main_query->where(function ($query) use ($search) {
    //                 $query->where('department.department_code', 'LIKE', "%{$search}%")
    //                     ->orWhere('department.department_name', 'LIKE', "%{$search}%")
    //                     ->orWhere('department.short_description', 'LIKE', "%{$search}%")
    //                     ->orWhere('client.company_name', 'LIKE', "%{$search}%");
    //             });

    //             $appointments = $search_query->paginate($limit, ['*'], 'page', $page_index);

    //             $search_list_for_count = $search_query->get();  // group by and direct count not working

    //             $recordsFiltered = count($search_list_for_count);

    //         }

    //         // foreach ($appointments as $key => $value) {
                
    //         //     if($request->level_no == '1'){
    //         //         $levels = ProjectProcessFramework::where('project_id', $request->project_id)
    //         //                                         ->where('level_no','!=',$value->level1id)
    //         //                                         ->select('proj_process_framework_id','level_name','level_no')
    //         //                                         ->get()
    //         //                                         ->toArray();

    //         //         foreach ($levels as $level) {
    //         //             $key = (string) $level['proj_process_framework_id']; // cast to string
    //         //             $value->$key = $level['level_name'];
    //         //         }
    //         //     }
    //         // }
    //         // dd($appointments);

    //         $data_arr = [];

    //         foreach ($appointments as $appointment) {

    //             // Fixed fields
    //             $row['id'] = $appointment->process_id;
    //             $row['processname'] = $appointment->processname ?? '';
    //             $row['ceo'] = $appointment->teamids ?? '';
    //             $row['mia'] = $appointment->mia ?? '';

    //             // Dynamic levels (only if level_no == 1)
    //             if ($request->level_no == '1') {
    //                 $levels = ProjectProcessFramework::where('project_id', $request->project_id)
    //                     ->where('level_no', '!=', $appointment->level1id)
    //                     ->select('proj_process_framework_id', 'level_name', 'level_no')
    //                     ->get()
    //                     ->toArray();

    //                 foreach ($levels as $level) {
    //                     $levelKey = strtolower(str_replace(' ', '_', $level['level_name']));
    //                     $row[$levelKey] = 0;
    //                 }
    //             }

    //             $row['priority'] = $appointment->priority_term ?? '-';
    //             $row['progress'] = ($appointment->progress ?? '0') . '%';

    //             $row['action'] = '<a href="/edit/' . $appointment->process_id . '" class="btn btn-sm btn-primary">Edit</a>';

    //             $data_arr[] = $row;
    //         }

    //         return response()->json([
    //     $data_arr    ]);

    //         $response = array(
    //             "draw" => (int)$request->input('draw'),
    //             "recordsTotal" => (int)$recordsTotal,
    //             "recordsFiltered" => (int)$recordsFiltered,
    //             "data" => $data_arr
    //         );

    //         return response()->json($response, 200);
     

    // }

    public function level_json_data(Request $request)
    {
        $main_query = ProcessMapping::from('prj_processmaping as processmaping')
            ->where('project_id', $request->project_id)
            ->join('mst_miaagents as mia', 'mia.mia_agent_id', '=', 'processmaping.mia_id')
            ->join('mst_employee as employee', 'employee.employee_id', '=', 'processmaping.employee_id');

        if ($request->level_no == '1') {
            $main_query->where('level1id', $request->proj_process_framework_id);
        }

        // Get records
        $appointments = $main_query->select(
            'processmaping.process_id',
            'processmaping.processname',
            'mia.nameofagent',
            'mia.agentpersonafile',
            'employee.display_name as employee_name',
            'processmaping.priority_term',
            'processmaping.progress',
            'processmaping.level1id'
        )->get();

        // Get dynamic levels
        $dynamicLevels = [];
        if ($request->level_no == '1') {
            $dynamicLevels = ProjectProcessFramework::where('project_id', $request->project_id)
                ->where('level_no', '!=', $request->level_no)
                ->select('proj_process_framework_id', 'level_name', 'level_no')
                ->get()
                ->toArray();
        }

        // Build response array
        $data_arr = [];

        foreach ($appointments as $appointment) {
            $row = [];

            // Fixed columns
            $row['id'] = $appointment->process_id;
            $row['processname'] = $appointment->processname ?? '';
            $row['ceo'] = ''; // You can add real logic if needed
            $row['mia'] = $appointment->nameofagent ?? '';

            // Dynamic levels initialized to 0
            foreach ($dynamicLevels as $level) {
                $levelKey = strtolower(str_replace(' ', '_', $level['level_name']));
                $row[$levelKey] = 0;
            }

            // Additional fields
            $row['priority'] = $appointment->priority_term ?? '-';
            $row['progress'] = ($appointment->progress ?? '0') . '%';
            $row['action'] = '<a href="/edit/' . $appointment->process_id . '" class="btn btn-sm btn-primary">Edit</a>';

            $data_arr[] = $row;
        }

        return response()->json([
            'data' => $data_arr
        ]);
    }


}
