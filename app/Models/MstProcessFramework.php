<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstProcessFramework extends Model
{
    use HasFactory;
    protected $table = 'mst_processframework';
    protected $primaryKey = 'process_framework_id';
}
