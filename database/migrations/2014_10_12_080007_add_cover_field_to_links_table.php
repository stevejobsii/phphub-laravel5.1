<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoverFieldToLinksTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->text('cover')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('cover');
        });
    }

    public function initData()
    {
        DB::table('links')->truncate();

        $links = [
            [
                'title' => 'Ruby China',
                'cover' => cdn('assets/images/friends/ruby-china.png'),
                'link' => 'https://ruby-china.org',
            ],
            [
                'title' => 'Golang 中国',
                'cover' => cdn('assets/images/friends/golangcn.png'),
                'link' => 'http://golangtc.com/',
            ],
            [
                'title' => 'CNode：Node.js 中文社区',
                'cover' => cdn('assets/images/friends/cnodejs.png'),
                'link' => 'http://cnodejs.org/',
            ],
            [
                'title' => 'F2E - 前端技术社区',
                'cover' => cdn('assets/images/friends/f2e.png'),
                'link' => 'http://f2e.im/',
            ]
        ];
        DB::table('links')->insert($links);
    }
}
