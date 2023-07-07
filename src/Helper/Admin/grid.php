<?php
/**
 * @file dcat 表格自定义函数
 */

use Dcat\Admin\Grid;

if(!function_exists('gridDialog')){
    /**
     * @description:grid 弹窗创建,编辑
     * @param Grid $grid
     * @return Grid
     * @Author:AKE
     * @Date:2021/11/25 9:19
     */
    function gridDialog(Grid &$grid): Grid
    {
        $grid->enableDialogCreate();
        $grid->showQuickEditButton();
        $grid->disableEditButton();
        return $grid;
    }
}

if (!function_exists('gridOrderSort')){
    /**
     * @description: 表格排序查询
     * @param Grid $grid
     * @return Grid
     * @Author:AKE
     * @Date:2022/9/11 12:11
     */
    function gridOrderSort(Grid  $grid) :Grid
    {
        $grid->model()->orderBy('order')->orderBy('id', 'desc');
        return $grid;
    }
}

if (!function_exists('gridOrder')) {
    /**
     * @description: 表格排序字段
     * @param Grid $grid
     * @return Grid
     * @Author:AKE
     * @Date:2023/1/5 13:38
     */
    function gridOrder(Grid $grid) :Grid
    {
        $grid->column('order')->help('数字越大越靠后')->sortable();
        return $grid;
    }
}