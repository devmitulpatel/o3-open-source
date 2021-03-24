<?php


namespace MS\Core\Traits\Models;


trait ModelAction
{

    //    protected
    public static function do_when_retrieved($collection){}

    public static function do_when_creating($collection){}

    public static function do_when_created($collection){}

    public static function do_when_updating($collection){}

    public static function do_when_updated($collection){}

    public static function do_when_saving($collection){}

    public static function do_when_saved($collection){}

    public static function do_when_deleting($collection){}

    public static function do_when_deleted($collection){}

    public static function do_when_restoring($collection){}

    public static function do_when_restored($collection){}

    public static function do_when_replicating($collection){}

    protected static function booted(){

        $dispatchesEvents=[
        'retrieved',
        'creating',
        'created',
        'updating',
        'updated',
        'saving',
        'saved',
        'deleting',
        'deleted',
        'restoring',
        'restored',
        'replicating',
       ];
        foreach ($dispatchesEvents as $method){
            static::created(function ($collection)use($method) {
                call_user_func(implode('::',['static',implode('_',['do_when',$method])]),$collection);
            });

        }


    }
}
