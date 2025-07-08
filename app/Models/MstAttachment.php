<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstAttachment extends Model
{
    use HasFactory;
    protected $table = 'mst_attachments';
    protected $primaryKey = 'attachment_id';
}
