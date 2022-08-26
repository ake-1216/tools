<?php
/**
 * @file dcat 操作按钮样式
 */

namespace Ake\Tools\Dcat\Menu;

use Dcat\Admin\Grid\Displayers\Actions;

class MenuAction extends Actions
{
    protected function getViewLabel()
    {
        return '<span style="font-size: 12px;padding: .2vw;" title="详情">详情</span>';
    }

    protected function getEditLabel()
    {
        return '<span style="font-size: 12px;padding: .2vw;" title="编辑">编辑</span>';
    }

    protected function getDeleteLabel()
    {
        return '<span style="font-size: 12px;padding: .2vw;" title="删除">删除</span>';
    }

    protected function getQuickEditLabel()
    {
        return '<span style="font-size: 12px;padding: .2vw;" title="编辑">编辑</span>';
    }
}
