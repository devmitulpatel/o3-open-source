<?php


namespace MS\Core\Traits\Models;


use App\Models\Group;
use App\Models\Permission;

trait HasPermission
{


    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

}
