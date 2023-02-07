<?php
/**
 * @file wechat 迁移文件
 * users可修改成对应的表
 */


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWechatUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('wechat')->comment('微信标识')->nullable();
            $table->text('wechat_avatar')->comment('微信头像')->nullable();
            $table->string('wechat_nickname')->comment('微信昵称')->nullable();
            $table->string('last_login_ip')->comment('上次登录ip')->nullable();
            $table->timestamp('last_login_at')->comment('上次登录时间')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wechat');
            $table->dropColumn('wechat_avatar');
            $table->dropColumn('wechat_nickname');
            $table->dropColumn('last_login_ip');
            $table->dropColumn('last_login_at');
        });
    }
}
