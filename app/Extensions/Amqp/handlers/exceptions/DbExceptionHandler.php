<?php

namespace App\Extensions\Amqp\handlers\exceptions;


use App\Extensions\Amqp\handlers\exceptions\ExceptionHandlerInterface;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class DbExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @param AMQPMessage $msg
     * @param Exception $exception
     * @return void
     */
    public function handleTheException(AMQPMessage $msg, Exception $exception): void
    {
        /** todo дописать логирование в базу ошибок */
    }
}
