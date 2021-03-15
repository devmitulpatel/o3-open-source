<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

class AttachRoleWithUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::MigrateForRoles();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        User::MigrateForRoles(false);
    }
}
