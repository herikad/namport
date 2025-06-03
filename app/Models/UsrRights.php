<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsrRights extends Model
{
    use HasFactory;

    protected $table = 'pb_usr_rights';
    protected $primaryKey = 'right_id';
}
