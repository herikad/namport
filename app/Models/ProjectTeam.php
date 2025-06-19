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
}
