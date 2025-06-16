<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessMapping extends Model
{
    use HasFactory;
    protected $table = 'prj_processmaping';
    protected $primaryKey = 'process_id';
}
