<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username',20)->default('')->comment('账号');
            $table->string('password',255)->default('')->comment('密码');
            $table->string('email',50)->nullable()->comment('邮箱');
            $table->text('last_login_token')->nullable()->comment('上次登录token');
            $table->enum('status',['0','1'])->default('0')->comment('登录状态');
            $table->timestamps();
            //软删除 生成一字段 deleted_at字段
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
