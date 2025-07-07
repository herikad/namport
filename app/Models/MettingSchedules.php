<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MettingSchedules extends Model
{
    use HasFactory;
    protected $table = 'prj_meetingschedules';
    protected $primaryKey = 'meeting_schedule_id';
}
