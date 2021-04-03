<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'permission_id',
        'group_id',
        'permission_type_id'
    ];

    public function permission_type()
    {
        return $this->belongsTo(PermissionType::class);
    }
}
