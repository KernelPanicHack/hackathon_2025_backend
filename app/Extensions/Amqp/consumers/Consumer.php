<?php

namespace App\Extensions\Amqp\consumers;


use App\Extensions\Amqp\AmqpRabbitMQ;
use App\Extensions\Amqp\dto\ConsumeConfigurationDto;
use App\Extensions\Amqp\handlers\exceptions\ExceptionHandlerInterface;
use App\Extensions\Amqp\helpers\MessageSerializer;
use App\Extensions\Amqp\jobs\JobInterface;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{
    /**
     * Массив обработчиков исключений. В случае если возникнет исключение
     * во время исполнения очереди, то будут выполнены обработчики исключений
     *
     * @var ExceptionHandlerInterface[]
     */
    public $handlersExceptions;

    /**
     * Название очереди, с которой будут считываться сообщения
     *
     * @var string
     */
    public $queue;

    public function __construct($queue, array $handlersExceptions)
    {
        $this->queue = $queue;
        $this->handlersExceptions = $handlersExceptions;
    }

    /**
     * Метод выполняет работу слушателя очереди указанной в конструкторе.
     */
    public function consume()
    {
        /** @var  $msg AMQPMessage */
        $callback = function (AMQPMessage $msg) {
            try {
                /** @var $item JobInterface */
                $item = MessageSerializer::unserializeMessage($msg);
                $item->log('Принято в обработку, задача класса ' . get_class($item));
                $item->execute();
                $msg->ack();
            } catch (Exception $exception) {
                $item->log(
                    'Ошибка во время выполнения работы класса ' . get_class($item) . "\r\n" . $exception->getMessage()
                );
                foreach ($this->handlersExceptions as $handlersException) {
                    $handlersException->handleTheException($msg, $exception);
                }
                $msg->reject(false);
            }
        };

        Log::info('Успешное подключение к rabbitmq, прослушивается очередь - ' . $this->queue);
        $amqp = App::make(AmqpRabbitMQ::class);
        $amqp->consume($this->queue, $callback, new ConsumeConfigurationDto());
    }
}
