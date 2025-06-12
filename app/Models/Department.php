<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $table = 'mst_department';
    protected $primaryKey = 'department_id';

    // public function client() {
    //     return $this->belongsTo(MstClient::class, 'client_id', 'client_id');
    // }

    public function client()
    {
        return $this->belongsTo(MstClient::class, 'client_id', 'client_id')
                    ->select([ 'client_id','company_name']);
    }
}
