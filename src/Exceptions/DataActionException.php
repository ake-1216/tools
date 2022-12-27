<?php
/**
 * @file 数据处理的异常类
 */
namespace Ake\Tools\Exceptions;

class DataActionException extends \Exception
{
    /**
     * @var string[] 错误消息数组
     */
    public array $message_arr = [
        'model' => '模型，表名不存在',
    ];

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = array_key_exists($message, $this->message_arr) ? $this->message_arr[$message] : $message;
        parent::__construct($message, $code, $previous);
    }
}