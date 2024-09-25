<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categries extends Model
{
    use HasFactory;

    protected $table = 'category';

    protected $fillable=[
        'name',
        'image'
    ];
    protected $hidden = [
        'created_at',
        'update_at'
    ];
}
