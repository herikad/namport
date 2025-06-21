<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTeam extends Model
{
    use HasFactory;
    protected $table = 'prj_projectteam';
    protected $primaryKey = 'teamid';
    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'association_id')
                    ->select('employee_id', 'display_name', 'profilepicture');
    }

    public function clientContact()
    {
        return $this->belongsTo(ClientContacts::class, 'association_id')
                    ->select('client_contacts_id', 'first_name', 'last_name','profile_pic');
    }

    public function miaAgent()
    {
        return $this->belongsTo(MstMiaAgent::class, 'association_id')
                    ->select('mia_agent_id', 'nameofagent', 'agentpersonafile');
    }
}
