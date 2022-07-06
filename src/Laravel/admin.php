<?php

use Dcat\Admin\Form;
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

if(!function_exists('formSeo')){
    /**
     * @description: form  SEO数据添加
     * @param Form $form
     * @return Form
     * @Author:AKE
     * @Date:2021/12/6 16:24
     */
    function formSeo(Form $form) :Form
    {
        $form->text('seo_title');
        $form->textarea('seo_keyword');
        $form->textarea('seo_description');
        return $form;
    }
}


if (!function_exists('formOrder')){
    /**
     * @description:排序字段
     * @param Form $form
     * @return Form
     * @Author:AKE
     * @Date:2022/7/4 15:22
     */
    function formOrder(Form $form) :Form
    {
        $form->number('order')->min(0)->default(100)->help('数字越大越靠后');
        return $form;
    }
}