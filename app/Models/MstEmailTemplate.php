<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstEmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'pb_mst_email_template';
    protected $primaryKey = 'email_template_id';
}
