<?php
/**
 * @file dcat 表单自定义函数
 */
use Dcat\Admin\Form;

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

if (!function_exists('formPublish')){
    /**
     * @description: 表单发布信息
     * @param Form $form
     * @return Form
     * @Author:AKE
     * @Date:2023/1/5 13:33
     */
    function formPublish(Form $form) :Form
    {
        $form->switch('is_publish')->default(1);
        $form->datetime('published_at')->default(date('Y-m-d H:i:s'));
        return $form;
    }
}

