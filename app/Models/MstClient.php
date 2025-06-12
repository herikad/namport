<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstClient extends Model
{
    use HasFactory;
    protected $table = 'mst_client';
    protected $primaryKey = 'client_id';


}
