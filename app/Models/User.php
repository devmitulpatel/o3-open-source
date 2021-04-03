<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use MS\Core\Traits\Models\HasRoles;
use MS\Core\Traits\Models\ModelAction;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, ModelAction;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function do_when_created($collection)
    {
        Ledger::create(
            ['name' => 'master', 'current_balance' => 0, 'user_id' => $collection->id, 'type_id' => 1, 'status' => 1]
        );
        Ledger::create(
            ['name' => 'cash', 'current_balance' => 0, 'user_id' => $collection->id, 'type_id' => 2, 'status' => 1]
        );
    }

    public function company()
    {
        return $this->belongsToMany(Company::class);
    }

}
