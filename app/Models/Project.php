<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'prj_project';
    protected $primaryKey = 'project_id';

    public function projectTeam()
    {
        return $this->hasMany(ProjectTeam::class, 'project_id'); 
    }


    public function ourTeam()
    {
         return $this->hasMany(ProjectTeam::class, 'project_id')
            ->where('association_type_term', config('custom.association_type_term.our_team'))
            ->with('employee');
    }

    public function ourClient()
    {
         return $this->hasMany(ProjectTeam::class, 'project_id')
            ->where('association_type_term', config('custom.association_type_term.client_contact'))
            ->with('clientContact');
    }

    public function miaAgent()
    {
        return $this->hasMany(ProjectTeam::class, 'project_id')
        ->where('association_type_term', 'mia_agent')
        ->with(['miaAgent' => function ($q) {
            $q->select('mia_agent_id', 'nameofagent', 'agentpersonafile');
        }]);
    }

}
