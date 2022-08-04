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
        $table->integer('order')->default(0)->comment('排序')->nullable();
        $table->integer('parent_id')->default(0)->comment('所属父级')->nullable();
        $table->index(['order', 'parent_id']);
    }
}