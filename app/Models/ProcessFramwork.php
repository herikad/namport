<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessFramwork extends Model
{
    use HasFactory;
    protected $table = 'prj_processframework';
    protected $primaryKey = 'process_framework_id';
}
