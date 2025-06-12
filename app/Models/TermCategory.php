<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermCategory extends Model
{
    use HasFactory;

    protected $table = 'pb_term_category';
    protected $primaryKey = 'term_category_id';
}
