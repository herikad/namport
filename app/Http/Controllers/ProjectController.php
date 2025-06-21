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
                ->get()
                ->toArray();

            $data['client_teams'] = ClientContacts::where('profile_status_term', config('custom.profile_status_term.completed'))
                ->where('is_active', 1)
                ->where('status_term', config('custom.status_term.active'))
                ->select('client_contacts_id as id', 'display_name as name')
                ->orderBy('display_name')
                ->get()
                ->toArray();

            $agents = MstMiaAgent::where('is_active', 1)
                ->orderBy('mia_agent_id')
                ->get(['mia_agent_id as id', 'nameofagent as name', 'agentpersonafile as image'])
                ->toArray();

            $data['mia_agents'] = $agents;

            $data['send_notificaiton_term'] = config('custom.send_notificaiton_term');

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
                        $team                        = new ProjectTeam;
                        $team->project_id            = $project->project_id;
                        $team->client_id             = $project->client_id;
                        $team->customer_id           = $project->customer_id;
                        $team->association_id        = $our_team_id;
                        $team->association_type_term = config('custom.association_type_term.our_team');
                        $team->designation           = $our_team_designation[$key] ?? null;
                        $team->project_role_term     = $our_team_designation[$key] ?? null;
                        $team->assigned_at           = date('Y-m-d H:i:s');
                        $team->assigned_by           = auth()->user()->association_id;
                        $team->save();
                    }
                }

                if (isset($request->client_team) && ! empty($request->client_team)) {
                    foreach ($request->client_team as $key => $client_team_id) {
                        $client_team_designation     = isset($request->client_designation) ? $request->client_designation : [];
                        $team                        = new ProjectTeam;
                        $team->project_id            = $project->project_id;
                        $team->client_id             = $project->client_id;
                        $team->customer_id           = $project->customer_id;
                        $team->association_id        = $client_team_id;
                        $team->association_type_term = config('custom.association_type_term.client_contact');
                        $team->designation           = $client_team_designation[$key] ?? null;
                        $team->project_role_term     = $client_team_designation[$key] ?? null;
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
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }

    public function project_json_list(Request $request){

        //  $projects = Project::with([
        //                 'ourTeam.employee',
        //                 'ourClient.clientContact',
        //                 'miaAgent.miaAgent',
        //             ])->get();
        // dd($projects);
        // try {
            $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;

            $limit = (int)$request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
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
            $response = array(
                "draw" => (int)$request->input('draw'),
                "recordsTotal" => (int)$recordsTotal,
                "recordsFiltered" => (int)$recordsFiltered,
                "data" => $appointments->getCollection()
            );

            return response()->json($response, 200);
        // } catch (\Exception $e) {
        //     Log::info("department_json_list_error ". print_r($e->getMessage(), true));
        // }

    }

}
