<?php

namespace App\Extensions\Amqp;

use App\Extensions\Amqp\config\ConfigRabbitConnection;
use App\Extensions\Amqp\dto\ConsumeConfigurationDto;
use App\Extensions\Amqp\dto\ExchangeConfigurationDto;
use App\Extensions\Amqp\dto\QueueBindConfigurationDto;
use App\Extensions\Amqp\dto\QueueConfigurationDto;
use App\Extensions\Amqp\dto\QueueUnBindConfigurationDto;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


/**
 * Компонент для работы с RabbitMQ по протоколу AMQP
 *
 * Class AmqpBundle
 */
class AmqpRabbitMQ
{
    /**
     * @var array
     *
     * Применяется при создании exchanges
     *
     * example
     *  array (
     *      array(
     *          'name' => 'Name exchange',
     *          'type' => 'topic|direct|headers|fanout',
     *          'config' => array(
     *            'passive' => optional default config,
     *            'durable' => optional default config,
     *            'autoDelete' => optional default config
     *          ),
     *      ),
     *      ...
     *  )
     */
    public array $exchanges = [];

    /**
     * @var array
     * Применяется при создании exchanges
     *
     * example
     *  array (
     *      array(
     *          'name' => 'Name queue',
     *          'config' => array(
     *              'passive' => optional default config,
     *              'durable' => optional default config,
     *              'exclusive' => optional default config,
     *              'autoDelete' => optional default config
     *          )
     *      ),
     *      ...
     *  )
     */
    public array $queues = [];

    /**
     * @var array
     * применяется при создании маршрутов от exchange к очередям
     *
     * example
     *
     * array (
     *      array(
     *          'queue' => 'Name queue',
     *          'exchange' => 'Name exchange',
     *          'routing' => array('routing', ...)
     *      ),
     *      ...
     * )
     */
    public array $bindings = [];


    const TOPIC = 'topic';
    const FANOUT = 'fanout';
    const DIRECT = 'direct';
    const HEADERS = 'headers';

    protected ConfigRabbitConnection $config;

    /** Соединение с RabbitMQ */
    public ?AMQPStreamConnection $connection = null;

    /** Канал для работы с RabbitMQ */
    public AMQPChannel $channel;

    /**
     * Создание соединения с RabbitMQ,
     * а так же создание канала связи
     *
     * @throws \Exception
     */
    public function __construct(ConfigRabbitConnection $config, $exchanges, $queues, $bindings)
    {
        $this->config = $config;
        $this->exchanges = $exchanges;
        $this->queues = $queues;
        $this->bindings = $bindings;

        $this->connection = new AMQPStreamConnection(
            $config->getHost(),
            $config->getPort(),
            $config->getLogin(),
            $config->getPassword(),
            $config->getVhost()
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * Отправка сообщения в очередь
     *
     * @param AMQPMessage $msg
     * @param $routingKey
     * @param $exchange
     * @return bool
     * @throws \Exception
     */
    public function publish(AMQPMessage $msg, $routingKey, $exchange, $withoutCloseConnection = false): bool
    {
        if (!$this->channel->is_open()) {
            $this->reconnect();
        }

        $this->channel->basic_publish($msg, $exchange, $routingKey);

        if (!$withoutCloseConnection) {
            $this->channel->close();
            $this->connection->close();
        }

        return true;
    }

    /**
     * Создание прослушивателя указанной очереди
     *
     * @param string $queue
     * @param $callback
     * @param ConsumeConfigurationDto $config
     * @throws \Exception
     */
    public function consume(string $queue, callable $callback, ConsumeConfigurationDto $config): void
    {
        $channel = $this->channel;

        $channel->basic_qos(0, 1, false);
        $channel->basic_consume(
            $queue,
            $config->consumerTag,
            $config->noLocal,
            $config->noAckItem,
            $config->exclusive,
            $config->noWait,
            $callback,
            $config->ticket,
            $config->arguments
        );

        while ($channel->is_open()) {
            $channel->wait();
        }
        $channel->close();
        $this->connection->close();
    }

    /**
     * Связывание очереди и exchange по указанным маршрутам
     *
     * @param $queue
     * @param $exchange
     * @param QueueBindConfigurationDto $config
     */
    public function queueBind($queue, $exchange, QueueBindConfigurationDto $config): void
    {
        $channel = $this->channel;

        $channel->queue_bind(
            $queue,
            $exchange,
            $config->route,
            $config->nowait,
            $config->arguments,
            $config->ticket
        );
    }

    /**
     * Удаление связи маршрутов между exchange и очередью
     *
     * @param $queue
     * @param $exchange
     * @param QueueUnBindConfigurationDto $config
     */
    public function queueUnBind($queue, $exchange, QueueUnBindConfigurationDto $config): void
    {
        $channel = $this->channel;

        $channel->queue_unbind(
            $queue,
            $exchange,
            $config->route,
            $config->arguments,
            $config->ticket
        );
    }

    /**
     * Объявление очереди в rabbitmq
     *
     * @param $queue
     * @param QueueConfigurationDto $config
     */
    public function declareQueue($queue, QueueConfigurationDto $config): void
    {
        $this->channel->queue_declare(
            $queue,
            $config->passive,
            $config->durable,
            $config->exclusive,
            $config->autoDelete,
            $config->nowait,
            $config->arguments,
            $config->ticket
        );
    }

    /**
     * Объявление exchange в rabbitmq
     *
     * @param $name
     * @param $type
     * @param ExchangeConfigurationDto $config
     */
    public function declareExchange($name, $type, ExchangeConfigurationDto $config): void
    {
        $this->channel->exchange_declare(
            $name,
            $type,
            $config->passive,
            $config->durable,
            $config->autoDelete,
            $config->internal,
            $config->nowait,
            $config->arguments,
        );
    }

    /**
     * Удаление указанной очереди
     *
     * @param $queue
     */
    public function deleteQueue($queue): void
    {
        $this->channel->queue_delete($queue);
    }

    /**
     * Удаление указанного exchange
     *
     * @param $exchange
     */
    public function deleteExchange($exchange): void
    {
        $this->channel->exchange_delete($exchange);
    }

    /**
     * Переподключение к серверу rabbitMQ
     */
    protected function reconnect(): void
    {
        if (!$this->connection->isConnected()) {
            $this->connection->reconnect();
        }

        $this->channel = $this->connection->channel();
    }
}
