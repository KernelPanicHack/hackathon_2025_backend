<?php

namespace App\Extensions\Amqp\helpers;


use App\Extensions\Amqp\AmqpRabbitMQ;
use Illuminate\Support\Facades\App;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Класс для удобного отправления сообщений в очереди
 *
 * @package application\extensions\amqp\helpers
 */
class QueueHelper
{
    const QUEUE_EXCHANGE = 'main.topic';

    /**
     * Добавляет сообщение в очередь, где обработка сообщенией
     * занимает длительное время
     *
     * @param AMQPMessage $message
     * @param $routingKey
     * @throws \Exception
     */
    static function addToSlowQueue(AMQPMessage $message, $routingKey, $withoutCloseConnection = false): void
    {
        $amqp = App::make(AmqpRabbitMQ::class);
        $amqp->publish($message, $routingKey, self::QUEUE_EXCHANGE, $withoutCloseConnection);
    }
}
