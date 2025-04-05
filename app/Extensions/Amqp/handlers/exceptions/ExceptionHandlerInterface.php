<?php

namespace App\Extensions\Amqp\handlers\exceptions;


use Exception;
use PhpAmqpLib\Message\AMQPMessage;

interface ExceptionHandlerInterface
{
    /**
     * @param AMQPMessage $msg
     * @param Exception $exception
     * @return void
     */
    public function handleTheException(AMQPMessage $msg, Exception $exception): void;
}
