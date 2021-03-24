<?php


namespace MS\Core\Traits\Models;


trait ModelPlus
{
    public static function first(){
        return (new self())->where('id','>',0)->take(1)->first();
    }
}
