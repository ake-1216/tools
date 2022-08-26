<?php

/**
 * @file dcat 初始化配置
 */

namespace Ake\Tools\Dcat;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Show;
use Dcat\Admin\Grid\Filter;

class config
{
    #基本配置信息
    public function init()
    {
        $this->grid();
        $this->show();
        $this->form();

        return $this;
    }

    #登录验证码
    public function captcha($captcha = true)
    {
        if (!config('app.debug')) $captcha = true;
        config(['captcha.enable' => $captcha]);
        return $this;
    }

    #主题颜色
    public function theme()
    {
        $color = (admin_setting('color') == 'light') ? 'blue-light' : admin_setting('color', 'blue-light');
        $nav_color = admin_setting('navbar_color') ? 'bg-' . admin_setting('navbar_color') : 'bg-dark';
        config([
            'admin.layout.horizontal_menu' => admin_setting('horizontal_menu', 0),
            'admin.layout.sidebar_collapsed' => admin_setting('sidebar_collapsed', 0),
            'admin.layout.color' => $color ?: 'default',
            'admin.layout.sidebar_style' => admin_setting('sidebar_style', 'dark'),
            'admin.layout.navbar_color' => $nav_color,
        ]);
        return $this;
    }

    #菜单样式
    public function gridMenuAction()
    {
        config([
            'grid.grid_action_class' => Ake\Tools\Dcat\Menu\MenuAction::class,
        ]);
    }

    private function grid()
    {
        Grid::resolving(function (Grid $grid) {
            #分页默认一页10条
            $grid->paginate(10);
            #禁用显示按钮
            $grid->disableViewButton();
            #禁用按钮样式
            $grid->toolsWithOutline(false);
            #不展开筛选栏
            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand(false);
            });
        });
    }

    private function show()
    {
        Show::resolving(function (Show $show){
            $show->disableDeleteButton();
        });
    }

    private function form()
    {
        Form::resolving(function (Form $form) {
            #禁用页面内的删除按钮
            $form->disableDeleteButton();
            #禁用页面内的视图按钮
            $form->disableViewButton();
            #禁用跳转多选按钮(视图,继续编辑,继续创建)
            $form->disableViewCheck();
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
        });
    }
}