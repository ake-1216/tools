<?php

namespace Ake\Tools\Rule;

use Illuminate\Contracts\Validation\Rule;

class Email implements Rule
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
        $regex = "/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.|\-]?)*[a-zA-Z0-9]+(\.[a-zA-Z]{2,3})+$/";
        return preg_match($regex, $value);
    }

    /**
     * 获取校验错误信息
     *
     * @return string
     */
    public function message()
    {
        return trans('ake-tools::validation.email');
    }
}