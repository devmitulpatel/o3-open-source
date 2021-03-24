<?php


namespace MS\Core\Traits\Models;


use App\Models\User;

trait AttachUser
{
    public function user(){
        return $this->belongsToMany(User::class);
    }
}
