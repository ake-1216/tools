<?php

/**
 * @file 验证手机号格式（自定义规则）
 */

namespace Ake\Tools\Rule;

use Illuminate\Contracts\Validation\Rule;

class Mobile implements Rule
{
    /**
     * 判断是否通过验证规则
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match("/^1[3456789]\d{9}$/", $value);
    }

    /**
     * 获取校验错误信息
     *
     * @return string
     */
    public function message()
    {
        return trans('ake-tools::validation.mobile');
    }
}