<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstMiaAgent extends Model
{
    use HasFactory;
    protected $table = 'mst_miaagents';
    protected $primaryKey = 'mia_agent_id';
}
