<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAgent extends Model
{
    use HasFactory;
    protected $table = 'prj_projectagents';
    protected $primaryKey = 'project_agentid';
}
