<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrustSetupTables extends Migration
{
    public function up()
    {
        // Create table for storing roles
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        // Create table for storing permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        $this->setupFoundorAndBaseRolsPermission();
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('permission_role');
        Schema::drop('permissions');
        Schema::drop('role_user');
        Schema::drop('roles');
    }

    public function setupFoundorAndBaseRolsPermission()
    {
        // Create Roles
        $founder = new App\Role;
        $founder->name = 'Founder';
        $founder->save();

        $admin = new App\Role;
        $admin->name = 'Admin';
        $admin->save();

        // Create User
        $user = App\User::create([
                'github_id' => 1,
                'github_url' => 'goodgoto.com',
                'name' => 'summerblue'
            ]);

        // Attach Roles to user
        $user->roles()->attach($founder->id);

        // Create Permissions
        $manageTopics = new App\Permission;
        $manageTopics->name = 'manage_topics';
        $manageTopics->display_name = 'Manage Topics';
        $manageTopics->save();

        $manageUsers = new App\Permission;
        $manageUsers->name = 'manage_users';
        $manageUsers->display_name = 'Manage Users';
        $manageUsers->save();

        // Assign Permission to Role
        $founder->perms()->sync([$manageTopics->id, $manageUsers->id]);
        $admin->perms()->sync([$manageTopics->id]);
    }
}
