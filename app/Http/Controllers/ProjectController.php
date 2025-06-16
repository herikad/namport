<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MstClient;
use App\Models\Employee;
use App\Models\ProcessFramwork;
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

          
          return view('pages.project.index');
        } catch (\Exception $e) {
            Log::info("project_index_error ". print_r($e->getMessage(), true));
        }
    }

    public function create(Request $request){
        try {

         // render code here

        } catch (\Exception $e) {
            Log::info("project_index_error ". print_r($e->getMessage(), true));
        }
    }
}
