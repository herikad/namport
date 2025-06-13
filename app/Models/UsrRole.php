<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsrRole extends Model
{
    use HasFactory;

    protected $table = 'pb_usr_role';
    protected $primaryKey = 'role_id';
}
