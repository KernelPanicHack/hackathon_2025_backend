<?php

namespace App\Extensions\Amqp\dto;


use PhpAmqpLib\Wire\AMQPTable;

/**
 * Класс, который содержит конфигурацию очередей,
 * необходимую при создании очереди
 *
 * @package application\extensions\amqp\dto
 */
class QueueConfigurationDto
{
    /** @var $passive bool */
    public bool $passive;

    /** @var $durable bool */
    public bool $durable;

    /** @var $exclusive bool */
    public bool $exclusive;

    /** @var $autoDelete bool */
    public bool $autoDelete;

    /** @var $nowait bool */
    public bool $nowait;

    /** @var $arguments array|AMQPTable */
    public array|AMQPTable $arguments;

    /** @var $ticket int|null */
    public int|null $ticket;

    public function __construct(
        $passive = false,
        $durable = false,
        $exclusive = false,
        $autoDelete = true,
        $nowait = false,
        $arguments = array(),
        $ticket = null
    )
    {
        $this->passive = $passive;
        $this->durable = $durable;
        $this->exclusive = $exclusive;
        $this->autoDelete = $autoDelete;
        $this->nowait = $nowait;
        $this->arguments = $arguments;
        $this->ticket = $ticket;
    }
}
