<?php


namespace MS\Core\Traits\Models;


use App\Models\Group;
use App\Models\Permission;
use App\Models\PermissionType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait HasRoles
{

    public static function MigrateForRoles($up = true)
    {
        $tables = [
            'roles',
            'groups',
            'permission_types',
            'permissions',
            'permission_role',
            'group_permission',
            'role_user',

        ];
        if ($up) {
            Schema::create($tables[0], function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('role_id');
                $table->timestamps();
            });
            Schema::create($tables[1], function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('group_id');
                $table->timestamps();
            });
            Schema::create($tables[2], function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('permission_type_id');
                $table->timestamps();
            });
            Schema::create($tables[3], function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->string('name');
                $table->string('permission_id');
                $table->string('permission_type_id');
                $table->timestamps();
            });
            Schema::create($tables[4], function (Blueprint $table) {
                // $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');
                //   $table->timestamps();
            });
            Schema::create($tables[5], function (Blueprint $table) {
                //     $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');
                //   $table->timestamps();
            });

            Schema::create($tables[6], function (Blueprint $table) {
                //      $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('user_id');
                //    $table->timestamps();
            });
            self::SeedForRoles();
        } else {
            array_walk($tables, function ($a) {
                Schema::dropIfExists($a);
            });
        }
    }

    public static function SeedForRoles($up = true)
    {
        if ($up) {
            $user = User::factory()->create(['email' => 'admin@admin.com', 'password' => bcrypt('password')]);
            $user->roles()->attach(1);
            $makeName = function ($a) {
                $explode = explode('_', $a);
                if (count($explode) > 1) {
                    return ucwords(implode(' ', $explode));
                } else {
                    return ucfirst($a);
                }
            };
            $roles = ['admin', 'business_owner', 'business_employee', 'business_user', 'broker', 'user'];
            array_walk($roles, function ($a) use ($makeName) {
                Role::create(['name' => $makeName($a), 'role_id' => $a]);
            });
            $permission_types = ['list', 'detail', 'view', 'create', 'edit', 'delete'];
            array_walk($permission_types, function ($a) use ($makeName) {
                PermissionType::create(['name' => $makeName($a), 'permission_type_id' => $a]);
            });
            $group = ['user'];
            array_walk($group, function ($a) use ($makeName) {
                Group::create(['name' => $makeName($a), 'group_id' => $a]);
            });
            $groups = Group::all()->toArray();

            array_walk($groups, function ($a, $k) use ($makeName, $permission_types) {
                array_walk($permission_types, function ($b, $k) use ($makeName, $a) {

                    Permission::create([
                        'name' => $makeName(implode('_', [$a['name'], $b])),
                        'permission_id' => implode('.', [$a['group_id'], $b]),
                        'group_id' => $a['id'],
                        'permission_type_id' => $k + 1
                    ]);
                });

            });
        } else {

        }
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
