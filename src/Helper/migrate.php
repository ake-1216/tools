<?php

use Illuminate\Database\Schema\Blueprint;

if(!function_exists('migrateSeo')){
    /**
     * @description:seo字段
     * @param Blueprint $table
     * @Author:AKE
     * @Date:2021/12/20 10:05
     */
    function migrateSeo(Blueprint $table)
    {
        $table->string('seo_title')->comment('seo标题')->nullable();
        $table->longText('seo_keyword')->comment('seo关键字')->nullable();
        $table->longText('seo_description')->comment('seo描述')->nullable();
    }
}

if (!function_exists('migrateOrder')){
    /**
     * @description:排序 字段
     * @param Blueprint $table
     * @Author:AKE
     * @Date:2021/12/24 12:01
     */
    function migrateOrder(Blueprint $table)
    {
        $table->integer('order')->default(100)->comment('排序')->nullable();
        $table->index('order');
    }
}

if (!function_exists('commentTable')){
    /**
     * @description:数据表添加注释
     * @param string $table 表名字
     * @param string $comment 注释内容
     * @Author:AKE
     * @Date:2022/6/14 14:29
     */
    function commentTable(string $table, string $comment)
    {
        \Illuminate\Support\Facades\DB::statement("alter table `$table` comment '$comment'");
    }
}

if (!function_exists('migrateTree')){
    /**
     * @description:模型树表字段 order ，parent_id
     * @param Blueprint $table
     * @Author:AKE
     * @Date:2022/6/15 9:12
     */
    function migrateTree(Blueprint $table)
    {
        $table->integer('order')->default(100)->comment('排序')->nullable();
        $table->integer('parent_id')->default(0)->comment('所属父级')->nullable();
        $table->index(['order', 'parent_id']);
    }
}

if (!function_exists('migratePublish')){
    /**
     * @description:发布字段
     * @param Blueprint $table
     * @Author:AKE
     * @Date:2022/8/5 15:17
     */
    function migratePublish(Blueprint $table)
    {
        $table->tinyInteger('is_publish')->default(1)->comment('是否发布')->nullable();
        $table->timestamp('published_at')->comment('发布时间')->nullable();
        $table->index(['is_publish', 'published_at']);
    }
}


if (!function_exists('migrateOther')){
    /**
     * @description:其他json字段
     * @param Blueprint $table
     * @Author:AKE
     * @Date:2023/4/11 13:35
     */
    function migrateOther(Blueprint $table)
    {
        $table->json('other')->comment('其他')->nullable();
    }
}