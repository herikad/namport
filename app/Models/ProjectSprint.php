<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSprint extends Model
{
    use HasFactory;
    protected $table = 'prj_projectsprints';
    protected $primaryKey = 'sprintid';
}
