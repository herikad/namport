<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsrUserrole extends Model
{
    use HasFactory;

    protected $table = 'pb_usr_userrole';
    protected $primaryKey = 'user_role_id';
}
