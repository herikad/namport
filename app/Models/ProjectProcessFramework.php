<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProcessFramework extends Model
{
    use HasFactory;
    protected $table = 'mst_project_process_framework';
    protected $primaryKey = 'proj_process_framework_id';
}
