<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('图片名')->nullable();
            $table->string('slug')->comment('标识')->nullable()->default('image');
            $table->longText('image')->comment('图片')->nullable();
            $table->longText('url')->comment('链接')->nullable();
            $table->tinyInteger('is_publish')->comment('是否发布')->default(1)->nullable();
            migrateOrder($table);
            migrateJson($table);
            $table->timestamps();
        });
        commentTable('images', '图片表');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
