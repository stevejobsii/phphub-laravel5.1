<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('password');
            $table->string('email')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->string('remember_token')->nullable();
            $table->boolean('is_banned')->default(false)->index();
            $table->integer('topic_count')->default(0)->index();
            $table->integer('reply_count')->default(0)->index();
            $table->string('city')->nullable();
            $table->string('company')->nullable();
            $table->string('weibo_account')->nullable();
            $table->string('personal_website')->nullable();
            $table->string('signature')->nullable();
            $table->string('introduction')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
