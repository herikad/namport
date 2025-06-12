<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientContacts extends Model
{
    use HasFactory;
    protected $table = 'mst_client_contacts';
    protected $primaryKey = 'client_contacts_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
