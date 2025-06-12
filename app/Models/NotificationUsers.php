<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationUsers extends Model
{
    use HasFactory;
    protected $table = 'pb_notification_users';
    protected $primaryKey = 'notify_id';
}
