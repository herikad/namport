<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MstClient;
use App\Models\Employee;
use App\Models\ProcessFramwork;
use App\Models\ProjectProcessFramework;
use App\Models\ProcessMapping;
use App\Models\Project;
use App\Models\ProjectAgent;
use App\Models\ProjectSprint;
use App\Models\ProjectTeam;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Http\Request;
use Log;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(Request $request){
        try {

            $client_id = 1;
            $data['client_id']         = $client_id;
            $data['billing_terms']     = config('custom.billing_term');
            $data['status']            = config('custom.profile_status_term');
            $data['project_managers']  = ['1' => 'Mayur', '2' => 'Herika', '3' => 'kishan'];
            $data['level_category']    = ['APQC' => 'APQC', 'BPMN' => 'BPMN', 'SIX_SIGMA' => 'SIX SIGMA', 'ISO_9001' => 'ISO 9001'];

            $data['frameworkData']     = ProcessFramwork::where('is_active', 1)
                                                        ->where('client_id', $client_id)
                                                        ->orderBy('process_framework_term')
                                                        ->orderBy('levelno')
                                                        ->get()
                                                        ->groupBy('process_framework_term')
                                                        ->map(function ($group) {
                                                            return $group->map(function ($item) {
                                                                return [
                                                                    'levelno' => $item->levelno,
                                                                    'levelname' => $item->levelname,
                                                                    'process_framework_id' => $item->process_framework_id,
                                                                    'shortdescription' => $item->shortdescription,
                                                                ];
                                                            });
                                                        })
                                                        ->toArray();
            $data['our_teams']    =  [
                ['id' => 1, 'name' => 'Black, Marvin'],
                ['id' => 2, 'name' => 'Henry, Arthur'],
                ['id' => 3, 'name' => 'Cooper, Kristin'],
                ['id' => 4, 'name' => 'Smith, John']
            ];

            $data['client_teams'] = [
                ['id' => 11, 'name' => 'David Miller'],
                ['id' => 12, 'name' => 'Sarah Johnson'],
                ['id' => 13, 'name' => 'Emily Clark']
            ];
            $data['mia_agents'] = $miaAgents = [
                ['id' => 1, 'name' => 'Ruva', 'image' => 'https://ui-avatars.com/api/?name=Ruva&background=0D8ABC&color=fff'],
                ['id' => 2, 'name' => 'Tana', 'image' => 'https://ui-avatars.com/api/?name=Tana&background=0D8ABC&color=fff'],
                ['id' => 3, 'name' => 'Kayo', 'image' => 'https://ui-avatars.com/api/?name=Kayo&background=0D8ABC&color=fff'],
                ['id' => 4, 'name' => 'Neli', 'image' => 'https://ui-avatars.com/api/?name=Neli&background=0D8ABC&color=fff'],
                ['id' => 5, 'name' => 'Oshi', 'image' => 'https://ui-avatars.com/api/?name=Oshi&background=0D8ABC&color=fff'],
                ['id' => 6, 'name' => 'Loma', 'image' => 'https://ui-avatars.com/api/?name=Loma&background=0D8ABC&color=fff'],
                ['id' => 7, 'name' => 'Tuli', 'image' => 'https://ui-avatars.com/api/?name=Tuli&background=0D8ABC&color=fff'],
                ['id' => 8, 'name' => 'Zali', 'image' => 'https://ui-avatars.com/api/?name=Zali&background=0D8ABC&color=fff'],
                ['id' => 9, 'name' => 'Zato', 'image' => 'https://ui-avatars.com/api/?name=Zato&background=0D8ABC&color=fff'],
                ['id' => 10, 'name' => 'Simo', 'image' => 'https://ui-avatars.com/api/?name=Simo&background=0D8ABC&color=fff'],
                ['id' => 11, 'name' => 'Meka', 'image' => 'https://ui-avatars.com/api/?name=Meka&background=0D8ABC&color=fff'],
                ['id' => 12, 'name' => 'Peni', 'image' => 'https://ui-avatars.com/api/?name=Peni&background=0D8ABC&color=fff'],
            ];

            $data['send_notificaiton_term'] = config('custom.send_notificaiton_term');



          
          return view('pages.project.index', $data);
        } catch (\Exception $e) {
            Log::info("project_index_error ". print_r($e->getMessage(), true));
        }
    }

    public function store(Request $request){

        try {

            $rules = ['projectname' => 'required','client_id' => 'required','projectmanager_id' => 'required'];

            $validator = Validator::make($request->all() , $rules);

            if ($validator->fails())
            {
                return response()->json([
                    'status' => 0,
                    'success' => false,
                    'message' => implode(',', $validator->messages()->all())
                ], 200);

            } else {

                $project = new Project;
                $project->projectname       = $request->projectname;
                $project->client_id         = $request->client_id;
                $project->projectmanager_id = $request->projectmanager_id;
                $project->billingtype_term  = $request->billingtype_term;
                $project->status_term       = $request->status_term;
                $project->startdate         = $request->startdate;
                $project->enddate           = $request->enddate;
                $project->sendnotification_term = $request->sendnotification_term;
                $project->allowcustomertasks = isset($request->allowcustomertasks) && $request->allowcustomertasks == 'on' ? 1 : 0;
                $project->allowcustomertoedittask = isset($request->allowcustomertoedittask) && $request->allowcustomertoedittask == 'on' ? 1 : 0;
                $project->allowcustomertocommentonprojecttask = isset($request->allowcustomertocommentonprojecttask) && $request->allowcustomertocommentonprojecttask == 'on' ? 1 : 0;
                $project->allowcustomertouploadattachmentontask = isset($request->allowcustomertouploadattachmentontask) && $request->allowcustomertouploadattachmentontask == 'on' ? 1 : 0;
                $project->allowcustomertoviewloggedhrs = isset($request->allowcustomertoviewloggedhrs) && $request->allowcustomertoviewloggedhrs == 'on' ? 1 : 0;
                $project->allowcustomertouploadfile = isset($request->allowcustomertouploadfile) && $request->allowcustomertouploadfile == 'on' ? 1 : 0;
                $project->allowcustomertoviewteams = isset($request->allowcustomertoviewteams) && $request->allowcustomertoviewteams == 'on' ? 1 : 0;
                $project->save();

                if (isset($request->logo_image) && $request->hasFile('logo_image')) {
                    $file            = $request->file('logo_image');
                    $image_name      = 'project_logo_'. time() . "." . $file->getClientOriginalExtension();
                    $destinationPath = ('images/project/logo');

                    \Helper::upload_file($request->file('logo_image'), $image_name, $destinationPath);

                    Project::where("project_id", $project->project_id)->update([
                        'logo_image' => $image_name,
                    ]);
                }

                if (isset($request->banner_image) && $request->hasFile('banner_image')) {
                    $file               = $request->file('banner_image');
                    $banner_image_name  = 'project_banner_'. time() . "." . $file->getClientOriginalExtension();
                    $destinationPath    = ('images/project/banner');

                    \Helper::upload_file($request->file('banner_image'), $banner_image_name, $destinationPath);

                    Project::where("project_id", $project->project_id)->update([
                        'banner_image' => $banner_image_name,
                    ]);
                }

                if (isset($request->level_selected_tab) && $project) {

                    $levels  = ProcessFramwork::where('is_active', 1)
                                                ->where('client_id', $request->client_id)
                                                ->where('process_framework_term', $request->level_selected_tab)
                                                ->orderBy('levelno')
                                                ->get();

                    if (isset($levels) && !empty($levels)) {
                        foreach ($levels as $key => $level) {
                            $level_store = new ProjectProcessFramework;
                            $level_store->project_framework_id = $project->project_id;
                            $level_store->ref_process_framework_id = $level->process_framework_id;
                            $level_store->client_id = $level->client_id;
                            $level_store->customer_id = $level->customer_id;
                            $level_store->level_no = $level->levelno;
                            $level_store->level_name = $level->levelname;
                            $level_store->level_detail = $level->shortdescription;
                            $level_store->save();
                        }
                    }                   
                }

                if (isset($request->our_team) && !empty($request->our_team)) {
                    foreach ($request->our_team as $key => $our_team_id) {
                        $our_team_designation = isset($request->our_team_designation) ? $request->our_team_designation : [];
                        $team = new ProjectTeam;
                        $team->project_id = $project->project_id;
                        $team->client_id = $project->client_id;
                        $team->association_id = $our_team_id;
                        $team->association_type_term = 'our_team';
                        $team->designation = $our_team_designation[$key] ?? null;
                        $team->project_role_term = $our_team_designation[$key] ?? null;
                        $team->assigned_at = date('Y-m-d H:i:s');
                        $team->assigned_by = auth()->user()->user_id;
                        $team->save();
                    }
                }

                if (isset($request->client_team) && !empty($request->client_team)) {
                    foreach ($request->client_team as $key => $client_team_id) {
                        $client_team_designation = isset($request->client_designation) ? $request->client_designation : [];
                        $team = new ProjectTeam;
                        $team->project_id = $project->project_id;
                        $team->client_id = $project->client_id;
                        $team->association_id = $client_team_id;
                        $team->association_type_term = 'client_team';
                        $team->designation = $client_team_designation[$key] ?? null;
                        $team->project_role_term = $client_team_designation[$key] ?? null;
                        $team->assigned_at = date('Y-m-d H:i:s');
                        $team->assigned_by = auth()->user()->user_id;
                        $team->save();
                    }
                }

                if (isset($request->mia_agents) && !empty($request->mia_agents)) {
                    foreach ($request->mia_agents as $key => $mia_agent_id) {
                        $team = new ProjectTeam;
                        $team->project_id = $project->project_id;
                        $team->client_id = $project->client_id;
                        $team->association_id = $mia_agent_id;
                        $team->association_type_term = 'MIA_agnet';
                        $team->project_role_term = 'MIA_AGENT';
                        $team->assigned_at = date('Y-m-d H:i:s');
                        $team->assigned_by = auth()->user()->user_id;
                        $team->save();
                    }
                }

                return response()->json([
                    'status' => 1,
                    'success' => true,
                    'message' => 'Project created has been successfully',
                    'redirect_url' => route('project.index')
                ], 200);

            }

        } catch (\Exception $e) {
            Log::info("project_store_error ". print_r($e->getMessage(), true));
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }
}
