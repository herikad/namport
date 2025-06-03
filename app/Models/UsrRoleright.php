<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsrRoleright extends Model
{
    use HasFactory;

    protected $table = 'pb_usr_roleright';
    protected $primaryKey = 'role_right_id';
}
