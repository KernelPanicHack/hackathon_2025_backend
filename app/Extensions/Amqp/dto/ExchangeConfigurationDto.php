<?php


namespace App\Extensions\Amqp\dto;


use PhpAmqpLib\Wire\AMQPTable;

/**
 * Класс, который содержит конфигурацию,необходимую
 * при создании маршрутизатора
 *
 * @package application\extensions\amqp\dto
 */
class ExchangeConfigurationDto
{
    /** @var $passive bool */
    public bool $passive;

    /** @var $durable bool */
    public bool $durable;

    /** @var $autoDelete bool */
    public bool $autoDelete;

    /** @var $internal bool */
    public bool $internal;

    /** @var $nowait bool */
    public bool $nowait;

    /** @var $arguments AMQPTable|array */
    public AMQPTable|array $arguments;

    public function __construct(
        $passive = false,
        $durable = false,
        $autoDelete = true,
        $internal = false,
        $nowait = false,
        $arguments = array(),
    )
    {
        $this->passive = $passive;
        $this->durable = $durable;
        $this->autoDelete = $autoDelete;
        $this->internal = $internal;
        $this->nowait = $nowait;
        $this->arguments = $arguments;
    }

}
