<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstEmailSetting extends Model
{
    use HasFactory;

    protected $table = 'pb_mst_email_setting';
    protected $primaryKey = 'email_setting_id';
}
