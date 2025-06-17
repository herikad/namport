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
        dd($request->all());
        try {

         // render code here

        } catch (\Exception $e) {
            Log::info("project_index_error ". print_r($e->getMessage(), true));
        }
    }
}
