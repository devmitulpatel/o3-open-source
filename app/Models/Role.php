<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MS\Core\Traits\Models\HasPermission;

class Role extends Model
{
    use HasFactory, HasPermission;

    protected $fillable = [
        'role_id',
        'name'
    ];

}
