<?php


namespace App\Extensions\Amqp\helpers;


use App\Extensions\Amqp\jobs\JobInterface;
use PhpAmqpLib\Message\AMQPMessage;

class MessageSerializer
{
    /**
     * Метод, который подготавливает данные для отправки в очередь
     *
     * @param JobInterface $content
     * @return AMQPMessage
     */
    public static function serializeMessage(JobInterface $content): AMQPMessage
    {
        return new AMQPMessage(serialize($content));
    }

    /**
     * Метод выполняет роль извлечения данных из сообщения
     * полученного через очередь.
     *
     * @param AMQPMessage $message
     * @return mixed
     */
    public static function unserializeMessage(AMQPMessage $message): mixed
    {
        return unserialize($message->body);
    }

}
